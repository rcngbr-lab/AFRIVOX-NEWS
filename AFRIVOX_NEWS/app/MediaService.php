<?php
declare(strict_types=1);

final class MediaService
{
    private static function privateRoot(): string{return rtrim((string)cfg('private_media_path',dirname(dirname(__DIR__)).'/AFRIVOX_NEWS_PRIVATE_MEDIA'),'/\\');}

    /** Store an image in private quarantine. Nothing public is created before approval. */
    public static function storeImage(array $file,array $meta,int $userId): ?array
    {
        if(($file['error']??UPLOAD_ERR_NO_FILE)===UPLOAD_ERR_NO_FILE)return null;
        if(($file['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_OK||(int)$file['size']>8*1024*1024)throw new RuntimeException('Image must upload successfully and be under 8 MB.');
        if(!extension_loaded('gd'))throw new RuntimeException('Secure image processing is unavailable: install PHP GD before accepting uploads.');
        $tmp=(string)$file['tmp_name'];$finfo=new finfo(FILEINFO_MIME_TYPE);$mime=(string)$finfo->file($tmp);$info=@getimagesize($tmp);
        $load=['image/jpeg'=>'imagecreatefromjpeg','image/png'=>'imagecreatefrompng','image/webp'=>'imagecreatefromwebp'];
        if(!$info||!isset($load[$mime])||($info['mime']??'')!==$mime)throw new RuntimeException('Only genuine JPEG, PNG or WebP images are accepted.');
        [$width,$height]=[(int)$info[0],(int)$info[1]];
        if($width<1||$height<1||$width>8000||$height>8000||$width*$height>40000000)throw new RuntimeException('Image dimensions are unsafe. Maximum: 40 megapixels and 8000 px per side.');
        foreach(['title','alt_text','creator','source_organization','license_type','copyright_holder'] as $required)if(trim((string)($meta[$required]??''))==='')throw new RuntimeException('Media title, alt text, creator, source organization, license and copyright holder are required.');
        $private=self::privateRoot();if(!is_dir($private)&&!mkdir($private,0770,true))throw new RuntimeException('Private media storage could not be created.');
        $token=bin2hex(random_bytes(18));$original=$private.'/'.$token.'.original';$candidate=$private.'/'.$token.'.candidate.jpg';
        if(!move_uploaded_file($tmp,$original))throw new RuntimeException('Private original storage failed.');chmod($original,0600);
        try{
            $image=$load[$mime]($original);if(!$image)throw new RuntimeException('Image decoding failed.');
            if(!imagejpeg($image,$candidate,86)){imagedestroy($image);throw new RuntimeException('Safe image derivative failed.');}imagedestroy($image);chmod($candidate,0600);
            $sha=hash_file('sha256',$original);$scanStatus=(string)cfg('malware_scanner_enabled',false)==='1'?'pending':'unavailable';
            db()->prepare('INSERT INTO media_assets(title,description,asset_type,storage_path,original_path,public_path,sha256,mime_type,file_size,width,height,creator,source_organization,license_type,copyright_holder,usage_restrictions,rights_expires_at,caption,alt_text,approval_status,malware_scan_status,uploaded_by) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)')->execute([
                substr(trim((string)$meta['title']),0,240),substr(trim((string)($meta['description']??'')),0,5000)?:null,'image',$candidate,$original,null,$sha,'image/jpeg',filesize($candidate),$width,$height,substr(trim((string)$meta['creator']),0,200),substr(trim((string)$meta['source_organization']),0,200),substr(trim((string)$meta['license_type']),0,120),substr(trim((string)$meta['copyright_holder']),0,240),substr(trim((string)($meta['usage_restrictions']??'')),0,5000)?:null,trim((string)($meta['rights_expires_at']??''))?:null,substr(trim((string)($meta['caption']??'')),0,2000)?:null,substr(trim((string)$meta['alt_text']),0,500),'pending',$scanStatus,$userId
            ]);$id=(int)db()->lastInsertId();return ['id'=>$id,'status'=>'pending'];
        }catch(Throwable $e){@unlink($candidate);@unlink($original);throw $e;}
    }

    /** Approve and atomically expose the safe derivative. Uploader cannot approve their own asset. */
    public static function approveImage(int $mediaId,int $approverId): string
    {
        $asset=query_one('SELECT * FROM media_assets WHERE id=?',[$mediaId]);if(!$asset||$asset['asset_type']!=='image')throw new RuntimeException('Image asset not found.');
        if((int)$asset['uploaded_by']===$approverId)throw new RuntimeException('Media uploader cannot approve their own asset.');
        if($asset['approval_status']==='approved'&&!empty($asset['public_path']))return (string)$asset['public_path'];
        $scan=(string)($asset['malware_scan_status']??'unavailable');if((bool)cfg('malware_scanner_required',false)&&$scan!=='clean')throw new RuntimeException('A clean malware scan is required before approval.');if(!in_array($scan,['clean','unavailable'],true))throw new RuntimeException('Media cannot be approved before scan completion.');
        if(!empty($asset['rights_expires_at'])&&strtotime((string)$asset['rights_expires_at'])<=time())throw new RuntimeException('Media rights have expired.');
        foreach(['creator','source_organization','license_type','copyright_holder','alt_text'] as $required)if(trim((string)($asset[$required]??''))==='')throw new RuntimeException('Required rights/accessibility metadata is missing.');
        if(preg_match('/\b(no[ -]?publish|not for publication|publication prohibited|internal use only)\b/i',(string)($asset['usage_restrictions']??'')))throw new RuntimeException('The recorded rights restrictions prohibit publication.');
        $candidate=(string)$asset['storage_path'];$private=realpath(self::privateRoot());$real=realpath($candidate);if(!$private||!$real||!str_starts_with(str_replace('\\','/',$real),rtrim(str_replace('\\','/',$private),'/').'/'))throw new RuntimeException('Invalid quarantined derivative path.');
        $publicDir=dirname(__DIR__).'/uploads';if(!is_dir($publicDir)&&!mkdir($publicDir,0770,true))throw new RuntimeException('Public media storage could not be created.');$relative='uploads/'.bin2hex(random_bytes(18)).'.jpg';$absolute=dirname(__DIR__).'/'.$relative;
        if(!copy($real,$absolute))throw new RuntimeException('Approved derivative publication failed.');chmod($absolute,0644);
        db()->beginTransaction();try{
            db()->prepare('UPDATE media_assets SET public_path=?,approval_status=\'approved\',approved_by=?,approved_at='.sql_now().' WHERE id=?')->execute([$relative,$approverId,$mediaId]);
            db()->prepare("UPDATE articles SET cover_image=?,lock_version=lock_version+1 WHERE id IN (SELECT article_id FROM article_media WHERE media_id=? AND usage_role='cover')")->execute([$relative,$mediaId]);db()->commit();
        }catch(Throwable $e){if(db()->inTransaction())db()->rollBack();@unlink($absolute);throw $e;}
        return $relative;
    }

    public static function reject(int $mediaId,int $reviewerId,string $reason): void
    {
        if(mb_strlen(trim($reason))<10)throw new RuntimeException('A rejection reason of at least 10 characters is required.');db()->prepare('UPDATE media_assets SET approval_status=\'rejected\',review_note=?,approved_by=?,approved_at='.sql_now().' WHERE id=?')->execute([substr(trim($reason),0,1000),$reviewerId,$mediaId]);
    }
}
