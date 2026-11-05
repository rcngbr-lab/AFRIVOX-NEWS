<?php
declare(strict_types=1);

final class AdService
{
    private static function privateRoot(): string
    {
        return rtrim((string)cfg('private_media_path',dirname(dirname(__DIR__)).'/AFRIVOX_NEWS_PRIVATE_MEDIA'),'/\\').'/advertising';
    }

    public static function resolvePrivateCreativePath(string $path): ?string
    {
        $root=realpath(self::privateRoot());$resolved=realpath($path);
        if(!$root||!$resolved||!is_file($resolved))return null;
        $root=rtrim(str_replace('\\','/',$root),'/').'/';$resolvedNormalized=str_replace('\\','/',$resolved);
        return str_starts_with($resolvedNormalized,$root)?$resolved:null;
    }

    private static function removeStoredFile(?string $path): void
    {
        if(!$path)return;
        $candidate=preg_match('~^(?:[A-Za-z]:[\\\\/]|/)~',$path)?$path:dirname(__DIR__).'/'.ltrim($path,'/\\');
        $resolved=realpath($candidate);if(!$resolved||!is_file($resolved))return;
        $allowed=[];foreach([self::privateRoot(),dirname(__DIR__).'/uploads/ads'] as $root){$real=realpath($root);if($real)$allowed[]=rtrim(str_replace('\\','/',$real),'/').'/';}
        $normalized=str_replace('\\','/',$resolved);foreach($allowed as $root)if(str_starts_with($normalized,$root)){@unlink($resolved);return;}
    }

    private static function metadata(array $meta): array
    {
        $title=trim((string)($meta['title']??''));$alt=trim((string)($meta['alt_text']??''));$url=trim((string)($meta['destination_url']??''));
        $button=trim((string)($meta['button_text']??''));$locale=(string)($meta['locale']??'all');$disclosure=(string)($meta['disclosure']??'ADVERTISEMENT');
        $holder=trim((string)($meta['rights_holder']??''));$license=trim((string)($meta['license_reference']??''));
        if(mb_strlen($title)<2||mb_strlen($title)>200||mb_strlen($alt)<5||mb_strlen($alt)>500)throw new RuntimeException('Creative title and descriptive alt text are required.');
        if(!filter_var($url,FILTER_VALIDATE_URL)||strtolower((string)parse_url($url,PHP_URL_SCHEME))!=='https')throw new RuntimeException('Destination must be a valid HTTPS URL.');
        if(!in_array($locale,['all','en','rw'],true)||!in_array($disclosure,['ADVERTISEMENT','SPONSORED'],true))throw new RuntimeException('Invalid language or disclosure.');
        if($holder===''||$license==='')throw new RuntimeException('Rights holder and licence/contract reference are required.');
        return [mb_substr($title,0,200),mb_substr($alt,0,500),substr($url,0,2048),mb_substr($button,0,80)?:null,$locale,$disclosure,mb_substr($holder,0,240),mb_substr($license,0,500)];
    }

    private static function processImage(array $file): array
    {
        if(($file['error']??UPLOAD_ERR_NO_FILE)===UPLOAD_ERR_NO_FILE)throw new RuntimeException('Choose a 2:3 advertisement image.');
        if(($file['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_OK||(int)($file['size']??0)>5*1024*1024)throw new RuntimeException('Advertisement image must upload successfully and be under 5 MB.');
        if(!extension_loaded('gd'))throw new RuntimeException('Secure image processing requires the PHP GD extension.');
        $tmp=(string)$file['tmp_name'];$finfo=new finfo(FILEINFO_MIME_TYPE);$mime=(string)$finfo->file($tmp);$info=@getimagesize($tmp);
        $load=['image/jpeg'=>'imagecreatefromjpeg','image/png'=>'imagecreatefrompng','image/webp'=>'imagecreatefromwebp'];
        if(!$info||!isset($load[$mime])||($info['mime']??'')!==$mime)throw new RuntimeException('Only genuine JPEG, PNG or WebP advertisement images are accepted.');
        [$width,$height]=[(int)$info[0],(int)$info[1]];
        if($width<300||$height<450||$width>6000||$height>6000||$width*$height>24000000)throw new RuntimeException('Creative must be at least 300×450 px and no more than 24 megapixels.');
        if(abs(($width/$height)-(2/3))>.025)throw new RuntimeException('Creative must use the 60×90 (2:3) aspect ratio, for example 600×900 px.');
        $private=self::privateRoot();if(!is_dir($private)&&!mkdir($private,0770,true))throw new RuntimeException('Private advertising storage could not be created.');
        $token=bin2hex(random_bytes(18));$original=$private.'/'.$token.'.original';$candidate=$private.'/'.$token.'.candidate.jpg';
        if(!move_uploaded_file($tmp,$original))throw new RuntimeException('Private creative storage failed.');chmod($original,0600);
        try{
            $source=$load[$mime]($original);if(!$source)throw new RuntimeException('Advertisement image decoding failed.');
            $safe=imagecreatetruecolor($width,$height);$white=imagecolorallocate($safe,255,255,255);imagefill($safe,0,0,$white);imagecopy($safe,$source,0,0,0,0,$width,$height);imagedestroy($source);
            if(!imagejpeg($safe,$candidate,86)){imagedestroy($safe);throw new RuntimeException('Safe advertisement derivative failed.');}imagedestroy($safe);chmod($candidate,0600);
            return [$original,$candidate,'image/jpeg',(int)filesize($candidate),$width,$height,hash_file('sha256',$candidate)];
        }catch(Throwable $e){@unlink($candidate);@unlink($original);throw $e;}
    }

    public static function create(array $file,array $meta,int $campaignId,int $userId): int
    {
        $campaign=query_one('SELECT id FROM ad_campaigns WHERE id=? AND status NOT IN (\'completed\',\'cancelled\')',[$campaignId]);if(!$campaign)throw new RuntimeException('Choose an open campaign.');
        $m=self::metadata($meta);$image=self::processImage($file);
        try{
            db()->prepare('INSERT INTO ad_creatives(campaign_id,title,alt_text,destination_url,button_text,locale,disclosure,private_original_path,private_derivative_path,mime_type,file_size,width,height,sha256,rights_holder,license_reference,status,created_by) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,\'draft\',?)')->execute([$campaignId,$m[0],$m[1],$m[2],$m[3],$m[4],$m[5],$image[0],$image[1],$image[2],$image[3],$image[4],$image[5],$image[6],$m[6],$m[7],$userId]);
            return (int)db()->lastInsertId();
        }catch(Throwable $e){@unlink($image[0]);@unlink($image[1]);throw $e;}
    }

    public static function update(int $id,array $file,array $meta,int $userId): void
    {
        $creative=query_one("SELECT * FROM ad_creatives WHERE id=? AND status IN ('draft','rejected')",[$id]);if(!$creative)throw new RuntimeException('Only draft or rejected creatives can be edited.');
        $m=self::metadata($meta);$replace=(($file['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_NO_FILE);$image=$replace?self::processImage($file):null;
        try{
            if($image)db()->prepare("UPDATE ad_creatives SET title=?,alt_text=?,destination_url=?,button_text=?,locale=?,disclosure=?,rights_holder=?,license_reference=?,private_original_path=?,private_derivative_path=?,public_path=NULL,mime_type=?,file_size=?,width=?,height=?,sha256=?,status='draft',review_note=NULL,approved_by=NULL,approved_at=NULL,updated_at=".sql_now().' WHERE id=?')->execute([$m[0],$m[1],$m[2],$m[3],$m[4],$m[5],$m[6],$m[7],$image[0],$image[1],$image[2],$image[3],$image[4],$image[5],$image[6],$id]);
            else db()->prepare("UPDATE ad_creatives SET title=?,alt_text=?,destination_url=?,button_text=?,locale=?,disclosure=?,rights_holder=?,license_reference=?,status='draft',review_note=NULL,approved_by=NULL,approved_at=NULL,updated_at=".sql_now().' WHERE id=?')->execute([$m[0],$m[1],$m[2],$m[3],$m[4],$m[5],$m[6],$m[7],$id]);
            if($image){foreach(['private_original_path','private_derivative_path','public_path'] as $field)self::removeStoredFile($creative[$field]??null);}
        }catch(Throwable $e){if($image){@unlink($image[0]);@unlink($image[1]);}throw $e;}
    }

    public static function approve(int $id,int $approverId): string
    {
        $creative=query_one("SELECT cr.*,c.status campaign_status FROM ad_creatives cr JOIN ad_campaigns c ON c.id=cr.campaign_id WHERE cr.id=?",[$id]);
        if(!$creative||$creative['status']!=='pending_review')throw new RuntimeException('Creative is not awaiting review.');
        if((int)$creative['created_by']===$approverId)throw new RuntimeException('Creative creator cannot approve the same creative.');
        if(!in_array($creative['campaign_status'],['approved','active'],true))throw new RuntimeException('Campaign must be independently approved first.');
        $candidate=self::resolvePrivateCreativePath((string)$creative['private_derivative_path']);if(!$candidate)throw new RuntimeException('Invalid quarantined creative path.');
        $dir=dirname(__DIR__).'/uploads/ads';if(!is_dir($dir)&&!mkdir($dir,0770,true))throw new RuntimeException('Public advertising directory could not be created.');
        $relative='uploads/ads/'.bin2hex(random_bytes(18)).'.jpg';$absolute=dirname(__DIR__).'/'.$relative;if(!copy($candidate,$absolute))throw new RuntimeException('Creative publication failed.');chmod($absolute,0644);
        try{$stmt=db()->prepare("UPDATE ad_creatives SET public_path=?,status='approved',review_note=NULL,approved_by=?,approved_at=".sql_now().',updated_at='.sql_now()." WHERE id=? AND status='pending_review'");$stmt->execute([$relative,$approverId,$id]);if($stmt->rowCount()!==1)throw new RuntimeException('Creative review changed before approval completed.');}
        catch(Throwable $e){@unlink($absolute);throw $e;}return $relative;
    }
}
