<?php
declare(strict_types=1);

/**
 * Editorial video registry for externally hosted, privacy-enhanced YouTube media.
 *
 * AFRIVOX NEWS does not claim to transcode or virus-scan video binaries in this
 * service. It records a reviewed provider reference, accessibility metadata and
 * publication rights, then exposes only independently approved records.
 */
final class VideoService
{
    public static function extractYouTubeId(string $url): ?string
    {
        $url=trim($url);
        if($url===''||!filter_var($url,FILTER_VALIDATE_URL))return null;
        $parts=parse_url($url);
        if(!is_array($parts)||strtolower((string)($parts['scheme']??''))!=='https')return null;
        $host=strtolower((string)($parts['host']??''));
        $host=preg_replace('/^www\./','',$host)??$host;
        $path=trim((string)($parts['path']??''),'/');
        $candidate='';
        if($host==='youtu.be')$candidate=explode('/',$path)[0]??'';
        elseif(in_array($host,['youtube.com','m.youtube.com'],true)){
            if($path==='watch'){
                parse_str((string)($parts['query']??''),$query);$candidate=(string)($query['v']??'');
            }elseif(preg_match('~^(?:shorts|embed)/([A-Za-z0-9_-]{11})(?:/|$)~',$path,$match))$candidate=$match[1];
        }
        return preg_match('/^[A-Za-z0-9_-]{11}$/',$candidate)?$candidate:null;
    }

    public static function normalizedSourceUrl(string $providerId): string
    {
        if(!preg_match('/^[A-Za-z0-9_-]{11}$/',$providerId))throw new RuntimeException('Invalid YouTube video identifier.');
        return 'https://www.youtube.com/watch?v='.$providerId;
    }

    public static function embedUrl(string $providerId): string
    {
        if(!preg_match('/^[A-Za-z0-9_-]{11}$/',$providerId))throw new RuntimeException('Invalid YouTube video identifier.');
        return 'https://www.youtube-nocookie.com/embed/'.$providerId;
    }

    /** @return array<string,mixed> */
    private static function validateMetadata(array $meta): array
    {
        $providerId=self::extractYouTubeId((string)($meta['source_url']??''));
        if($providerId===null)throw new RuntimeException('Use a valid HTTPS YouTube watch, short, embed or youtu.be URL.');
        $title=trim((string)($meta['title']??''));
        $description=trim((string)($meta['description']??''));
        $alt=trim((string)($meta['alt_text']??''));
        $rightsHolder=trim((string)($meta['rights_holder']??''));
        $license=trim((string)($meta['license_reference']??''));
        $language=(string)($meta['language']??'rw');
        $transcript=trim((string)($meta['transcript_text']??''));
        $captions=!empty($meta['captions_available'])?1:0;
        $duration=max(0,(int)($meta['duration_seconds']??0));
        if(mb_strlen($title)<5||mb_strlen($title)>240)throw new RuntimeException('Video title must contain 5 to 240 characters.');
        if($description!==''&&mb_strlen($description)>5000)throw new RuntimeException('Video description is too long.');
        if(mb_strlen($alt)<10||mb_strlen($alt)>500)throw new RuntimeException('Accessible video description must contain 10 to 500 characters.');
        if($rightsHolder===''||mb_strlen($rightsHolder)>240||$license===''||mb_strlen($license)>500)throw new RuntimeException('Rights holder and licence reference are required.');
        if(!in_array($language,['rw','en','fr','multi'],true))throw new RuntimeException('Invalid video language.');
        if($duration>86400)throw new RuntimeException('Video duration cannot exceed 24 hours.');
        if(!$captions&&mb_strlen($transcript)<20)throw new RuntimeException('Confirm provider captions or provide a transcript of at least 20 characters.');
        if(mb_strlen($transcript)>200000)throw new RuntimeException('Transcript is too long.');
        $expiry=trim((string)($meta['rights_expires_at']??''));
        if($expiry!==''){
            $time=strtotime($expiry);if($time===false)throw new RuntimeException('Invalid rights expiry date.');
            $expiry=date('Y-m-d H:i:s',$time);
        }else $expiry=null;
        $restrictions=substr(trim((string)($meta['usage_restrictions']??'')),0,5000)?:null;
        if($restrictions&&preg_match('/\b(no[ -]?publish|not for publication|publication prohibited|internal use only)\b/i',$restrictions))throw new RuntimeException('The recorded usage restrictions prohibit publication.');
        return [
            'provider_id'=>$providerId,'source_url'=>self::normalizedSourceUrl($providerId),'title'=>$title,
            'description'=>$description?:null,'language'=>$language,'duration_seconds'=>$duration?:null,
            'transcript_text'=>$transcript?:null,'captions_available'=>$captions,'alt_text'=>$alt,
            'rights_holder'=>$rightsHolder,'license_reference'=>$license,'rights_expires_at'=>$expiry,
            'usage_restrictions'=>$restrictions,
        ];
    }

    public static function create(array $meta,int $creatorId): int
    {
        $v=self::validateMetadata($meta);
        db()->prepare('INSERT INTO video_assets(provider,provider_video_id,source_url,title,description,language,duration_seconds,transcript_text,captions_available,alt_text,rights_holder,license_reference,rights_expires_at,usage_restrictions,status,created_by) VALUES (\'youtube\',?,?,?,?,?,?,?,?,?,?,?,?,?,\'draft\',?)')->execute([
            $v['provider_id'],$v['source_url'],$v['title'],$v['description'],$v['language'],$v['duration_seconds'],$v['transcript_text'],$v['captions_available'],$v['alt_text'],$v['rights_holder'],$v['license_reference'],$v['rights_expires_at'],$v['usage_restrictions'],$creatorId
        ]);
        return (int)db()->lastInsertId();
    }

    public static function updateDraft(int $videoId,array $meta,int $actorId,bool $canEditAll=false): void
    {
        $asset=query_one('SELECT * FROM video_assets WHERE id=?',[$videoId]);
        if(!$asset)throw new RuntimeException('Video asset not found.');
        if(!in_array((string)$asset['status'],['draft','rejected'],true))throw new RuntimeException('Only draft or rejected videos can be edited.');
        if(!$canEditAll&&(int)$asset['created_by']!==$actorId)throw new RuntimeException('You may edit only your own video drafts.');
        $v=self::validateMetadata($meta);
        $stmt=db()->prepare('UPDATE video_assets SET provider_video_id=?,source_url=?,title=?,description=?,language=?,duration_seconds=?,transcript_text=?,captions_available=?,alt_text=?,rights_holder=?,license_reference=?,rights_expires_at=?,usage_restrictions=?,status=\'draft\',review_note=NULL,approved_by=NULL,approved_at=NULL,lock_version=lock_version+1 WHERE id=? AND lock_version=?');
        $stmt->execute([$v['provider_id'],$v['source_url'],$v['title'],$v['description'],$v['language'],$v['duration_seconds'],$v['transcript_text'],$v['captions_available'],$v['alt_text'],$v['rights_holder'],$v['license_reference'],$v['rights_expires_at'],$v['usage_restrictions'],$videoId,(int)$asset['lock_version']]);
        if($stmt->rowCount()!==1)throw new RuntimeException('Concurrent video edit detected. Reload before saving.');
    }

    public static function submit(int $videoId,int $actorId,bool $canEditAll=false): void
    {
        $asset=query_one('SELECT * FROM video_assets WHERE id=?',[$videoId]);
        if(!$asset||!in_array((string)$asset['status'],['draft','rejected'],true))throw new RuntimeException('Only a draft or rejected video can be submitted.');
        if(!$canEditAll&&(int)$asset['created_by']!==$actorId)throw new RuntimeException('You may submit only your own video.');
        $stmt=db()->prepare("UPDATE video_assets SET status='submitted',review_note=NULL,lock_version=lock_version+1 WHERE id=? AND status IN ('draft','rejected') AND lock_version=?");
        $stmt->execute([$videoId,(int)$asset['lock_version']]);
        if($stmt->rowCount()!==1)throw new RuntimeException('Video changed in another session. Reload before submitting.');
    }

    public static function approve(int $videoId,int $approverId): void
    {
        $asset=query_one('SELECT * FROM video_assets WHERE id=?',[$videoId]);
        if(!$asset||(string)$asset['status']!=='submitted')throw new RuntimeException('Only a submitted video can be approved.');
        if((int)$asset['created_by']===$approverId)throw new RuntimeException('Video creator cannot approve their own work.');
        if(empty($asset['captions_available'])&&mb_strlen(trim((string)$asset['transcript_text']))<20)throw new RuntimeException('Captions or a transcript are required before approval.');
        if(!empty($asset['rights_expires_at'])&&strtotime((string)$asset['rights_expires_at'])<=time())throw new RuntimeException('Video publication rights have expired.');
        $stmt=db()->prepare("UPDATE video_assets SET status='approved',review_note=NULL,approved_by=?,approved_at=".sql_now().",lock_version=lock_version+1 WHERE id=? AND status='submitted' AND created_by<>? AND lock_version=?");
        $stmt->execute([$approverId,$videoId,$approverId,(int)$asset['lock_version']]);
        if($stmt->rowCount()!==1)throw new RuntimeException('Video approval conflict. Reload before continuing.');
    }

    public static function reject(int $videoId,int $reviewerId,string $reason): void
    {
        $reason=trim($reason);if(mb_strlen($reason)<10)throw new RuntimeException('A rejection reason of at least 10 characters is required.');
        $asset=query_one('SELECT * FROM video_assets WHERE id=?',[$videoId]);
        if(!$asset||(string)$asset['status']!=='submitted')throw new RuntimeException('Only a submitted video can be rejected.');
        if((int)$asset['created_by']===$reviewerId)throw new RuntimeException('Video creator cannot review their own work.');
        $stmt=db()->prepare("UPDATE video_assets SET status='rejected',review_note=?,approved_by=?,approved_at=".sql_now().",lock_version=lock_version+1 WHERE id=? AND status='submitted' AND created_by<>? AND lock_version=?");
        $stmt->execute([substr($reason,0,1000),$reviewerId,$videoId,$reviewerId,(int)$asset['lock_version']]);
        if($stmt->rowCount()!==1)throw new RuntimeException('Video review conflict. Reload before continuing.');
    }

    public static function archive(int $videoId,int $reviewerId,string $reason): void
    {
        $reason=trim($reason);if(mb_strlen($reason)<10)throw new RuntimeException('An archive or takedown reason of at least 10 characters is required.');
        $asset=query_one('SELECT * FROM video_assets WHERE id=?',[$videoId]);
        if(!$asset||!in_array((string)$asset['status'],['approved','rejected'],true))throw new RuntimeException('Only an approved or rejected video can be archived.');
        $stmt=db()->prepare("UPDATE video_assets SET status='archived',review_note=?,approved_by=?,approved_at=".sql_now().",lock_version=lock_version+1 WHERE id=? AND status=? AND lock_version=?");
        $stmt->execute([substr($reason,0,1000),$reviewerId,$videoId,(string)$asset['status'],(int)$asset['lock_version']]);
        if($stmt->rowCount()!==1)throw new RuntimeException('Video archive conflict. Reload before continuing.');
    }

    public static function attachApproved(int $articleId,int $videoId,int $actorId): void
    {
        $article=query_one('SELECT id FROM articles WHERE id=?',[$articleId]);if(!$article)throw new RuntimeException('Article not found.');
        $asset=query_one("SELECT * FROM video_assets WHERE id=? AND status='approved'",[$videoId]);
        if(!$asset)throw new RuntimeException('Only an approved video can be attached.');
        if(!empty($asset['rights_expires_at'])&&strtotime((string)$asset['rights_expires_at'])<=time())throw new RuntimeException('Video publication rights have expired.');
        db()->beginTransaction();
        try{
            db()->prepare('DELETE FROM article_videos WHERE article_id=?')->execute([$articleId]);
            db()->prepare('INSERT INTO article_videos(article_id,video_asset_id,attached_by) VALUES (?,?,?)')->execute([$articleId,$videoId,$actorId]);
            db()->prepare('UPDATE articles SET video_url=?,lock_version=lock_version+1 WHERE id=?')->execute([(string)$asset['source_url'],$articleId]);
            db()->commit();
        }catch(Throwable $e){if(db()->inTransaction())db()->rollBack();throw $e;}
    }

    public static function detach(int $articleId): void
    {
        $attached=query_one('SELECT v.source_url FROM article_videos av JOIN video_assets v ON v.id=av.video_asset_id WHERE av.article_id=?',[$articleId]);
        db()->beginTransaction();
        try{
            db()->prepare('DELETE FROM article_videos WHERE article_id=?')->execute([$articleId]);
            if($attached)db()->prepare('UPDATE articles SET video_url=CASE WHEN video_url=? THEN NULL ELSE video_url END,lock_version=lock_version+1 WHERE id=?')->execute([(string)$attached['source_url'],$articleId]);
            db()->commit();
        }catch(Throwable $e){if(db()->inTransaction())db()->rollBack();throw $e;}
    }

    public static function attachmentForArticle(int $articleId): ?array
    {
        return query_one('SELECT v.*,av.attached_by,av.created_at AS attached_at FROM article_videos av JOIN video_assets v ON v.id=av.video_asset_id WHERE av.article_id=?',[$articleId]);
    }

    public static function publicForArticle(int $articleId): ?array
    {
        $asset=self::attachmentForArticle($articleId);
        if(!$asset||(string)$asset['status']!=='approved')return null;
        if(!empty($asset['rights_expires_at'])&&strtotime((string)$asset['rights_expires_at'])<=time())return null;
        if(preg_match('/\b(no[ -]?publish|not for publication|publication prohibited|internal use only)\b/i',(string)($asset['usage_restrictions']??'')))return null;
        $asset['embed_url']=self::embedUrl((string)$asset['provider_video_id']);
        return $asset;
    }
}
