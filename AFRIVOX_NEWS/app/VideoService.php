<?php
declare(strict_types=1);

/** Moderated YouTube video catalogue. Media is hosted by YouTube, not GitHub or cPanel. */
final class VideoService
{
    public static function youtubeId(string $url): ?string
    {
        $parts=parse_url(trim($url));
        if(!is_array($parts)||strtolower((string)($parts['scheme']??''))!=='https')return null;
        if(isset($parts['user'])||isset($parts['pass'])||isset($parts['port']))return null;
        $host=strtolower((string)($parts['host']??''));
        $path=(string)($parts['path']??'');
        $id=null;
        if(in_array($host,['www.youtube.com','youtube.com','m.youtube.com'],true)){
            if($path==='/watch'){parse_str((string)($parts['query']??''),$args);$id=$args['v']??null;}
            elseif(preg_match('~^/(?:shorts|live|embed)/([A-Za-z0-9_-]{11})/?$~',$path,$m))$id=$m[1];
        }elseif($host==='youtu.be' && preg_match('~^/([A-Za-z0-9_-]{11})/?$~',$path,$m))$id=$m[1];
        return is_string($id)&&preg_match('/^[A-Za-z0-9_-]{11}$/D',$id)?$id:null;
    }

    public static function embedUrl(string $id): string
    {
        if(!preg_match('/^[A-Za-z0-9_-]{11}$/D',$id))throw new InvalidArgumentException('Invalid video identifier.');
        return 'https://www.youtube-nocookie.com/embed/'.$id.'?rel=0';
    }

    public static function thumbnailUrl(string $id): string
    {
        if(!preg_match('/^[A-Za-z0-9_-]{11}$/D',$id))throw new InvalidArgumentException('Invalid video identifier.');
        return 'https://i.ytimg.com/vi/'.$id.'/hqdefault.jpg';
    }

    public static function publicWhere(string $alias='v'): string
    {
        return $alias.".status='published' AND ".$alias.".published_at IS NOT NULL AND ".$alias.".published_at <= ".sql_now().
            " AND (".$alias.".rights_expires_at IS NULL OR ".$alias.".rights_expires_at > ".sql_now().")";
    }

    public static function validate(array $post): array
    {
        $id=self::youtubeId(trim((string)($post['youtube_url']??'')));
        if(!$id)throw new RuntimeException('A valid HTTPS YouTube video link is required.');
        $title=trim((string)($post['title']??''));
        $description=trim((string)($post['description']??''));
        if(mb_strlen($title)<8||mb_strlen($title)>240||mb_strlen($description)<20||mb_strlen($description)>5000)
            throw new RuntimeException('Title (8-240 characters) and description (20-5000 characters) are required.');
        foreach(['creator','source_organization','license_type','copyright_holder'] as $field){
            if(trim((string)($post[$field]??''))==='')throw new RuntimeException('All creator and licensing fields are required.');
        }
        $category=(int)($post['category_id']??0);
        if(!query_one('SELECT id FROM categories WHERE id=? AND active=1',[$category]))throw new RuntimeException('Choose an active category.');
        $article=($post['article_id']??'')!==''?(int)$post['article_id']:null;
        if($article!==null && ($article<1||!query_one('SELECT id FROM articles WHERE id=?',[$article])))throw new RuntimeException('Linked article not found.');
        $language=(string)($post['language']??'rw');
        if(!in_array($language,['rw','en'],true))throw new RuntimeException('Unsupported language.');
        $expiry=trim((string)($post['rights_expires_at']??''));$expires=null;
        if($expiry!==''){
            $dt=DateTimeImmutable::createFromFormat('!Y-m-d\TH:i',$expiry);
            if(!$dt||$dt->format('Y-m-d\TH:i')!==$expiry)throw new RuntimeException('Invalid rights expiry date.');
            $expires=$dt->format('Y-m-d H:i:s');
            if(strtotime($expires)<=time())throw new RuntimeException('Rights expiry must be in the future.');
        }
        $restrictions=trim((string)($post['usage_restrictions']??''));
        if(preg_match('/\b(no[ -]?publish|not for publication|publication prohibited|internal use only)\b/i',$restrictions))
            throw new RuntimeException('Rights restrictions prohibit publication.');
        return [
            'title'=>$title,'description'=>$description,'provider_video_id'=>$id,
            'category_id'=>$category,'article_id'=>$article,'language'=>$language,
            'creator'=>mb_substr(trim((string)$post['creator']),0,200),
            'source_organization'=>mb_substr(trim((string)$post['source_organization']),0,200),
            'license_type'=>mb_substr(trim((string)$post['license_type']),0,120),
            'copyright_holder'=>mb_substr(trim((string)$post['copyright_holder']),0,240),
            'usage_restrictions'=>mb_substr($restrictions,0,5000)?:null,
            'rights_expires_at'=>$expires
        ];
    }

    public static function save(array $post,int $userId,int $id=0,bool $editAll=false): int
    {
        $data=self::validate($post);
        if($id){
            $previous=query_one('SELECT * FROM video_clips WHERE id=?',[$id]);
            if(!$previous)throw new RuntimeException('Video not found.');
            if(!$editAll && (int)$previous['created_by']!==$userId)throw new RuntimeException('Cannot edit someone else’s video.');
            if(!in_array($previous['status'],['draft','rejected'],true))throw new RuntimeException('Video must be returned to draft before editing.');
            $fields=array_keys($data);$set=implode(',',array_map(static fn($f)=>$f.'=?',$fields));
            db()->prepare('UPDATE video_clips SET '.$set.",status='draft',reviewed_by=NULL,reviewed_at=NULL,review_note=NULL WHERE id=?")
                ->execute([...array_values($data),$id]);
            return $id;
        }
        $slug=substr(slugify($data['title']),0,210).'-'.bin2hex(random_bytes(4));
        $fields=array_keys($data);
        db()->prepare('INSERT INTO video_clips (slug,created_by,'.implode(',',$fields).') VALUES (?,?,'.implode(',',array_fill(0,count($fields),'?')).')')
            ->execute([$slug,$userId,...array_values($data)]);
        return (int)db()->lastInsertId();
    }

    public static function changeStatus(int $id,string $action,int $actorId,bool $canApprove,bool $canPublish): void
    {
        $v=query_one('SELECT * FROM video_clips WHERE id=?',[$id]);
        if(!$v)throw new RuntimeException('Video not found.');
        $from=(string)$v['status'];
        if($action==='submit' && in_array($from,['draft','rejected'],true) && (int)$v['created_by']===$actorId){
            db()->prepare("UPDATE video_clips SET status='pending',review_note=NULL WHERE id=? AND status=?")->execute([$id,$from]);
        }elseif($action==='approve' && $from==='pending' && $canApprove && (int)$v['created_by']!==$actorId){
            self::verifyRights($v);
            db()->prepare("UPDATE video_clips SET status='approved',reviewed_by=?,reviewed_at=".sql_now()." WHERE id=? AND status='pending'")->execute([$actorId,$id]);
        }elseif($action==='publish' && $from==='approved' && $canPublish && (int)$v['created_by']!==$actorId){
            self::verifyRights($v);
            db()->prepare("UPDATE video_clips SET status='published',published_by=?,published_at=".sql_now()." WHERE id=? AND status='approved'")->execute([$actorId,$id]);
        }elseif($action==='unpublish' && $from==='published' && $canPublish){
            db()->prepare("UPDATE video_clips SET status='draft',published_at=NULL,published_by=NULL,reviewed_by=NULL,reviewed_at=NULL WHERE id=? AND status='published'")->execute([$id]);
        }elseif($action==='reject' && in_array($from,['pending','approved'],true) && $canApprove && (int)$v['created_by']!==$actorId){
            db()->prepare("UPDATE video_clips SET status='rejected',reviewed_by=?,reviewed_at=".sql_now()." WHERE id=? AND status=?")->execute([$actorId,$id,$from]);
        }else throw new RuntimeException('Not authorized or invalid video workflow transition.');
    }

    private static function verifyRights(array $v): void
    {
        if(!empty($v['rights_expires_at']) && strtotime((string)$v['rights_expires_at'])<=time())
            throw new RuntimeException('Video rights have expired.');
        foreach(['creator','source_organization','license_type','copyright_holder'] as $field)
            if(trim((string)($v[$field]??''))==='')throw new RuntimeException('Incomplete licensing information.');
        if(preg_match('/\b(no[ -]?publish|not for publication|publication prohibited|internal use only)\b/i',(string)($v['usage_restrictions']??'')))
            throw new RuntimeException('Rights restrictions prohibit publication.');
    }
}
