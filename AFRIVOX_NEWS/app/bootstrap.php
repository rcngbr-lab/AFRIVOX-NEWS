<?php
declare(strict_types=1);
require_once __DIR__.'/EditorialWorkflow.php';
require_once __DIR__.'/Security.php';
require_once __DIR__.'/MediaService.php';
require_once __DIR__.'/AdService.php';
require_once __DIR__.'/PasswordResetService.php';
$configPath = dirname(__DIR__) . '/config.local.php';
if (!is_file($configPath)) {
    http_response_code(503);
    exit('Setup required: copy config.example.php to config.local.php and set your MySQL credentials. See README.md.');
}
$cfg = require $configPath;
if (!is_array($cfg)) { http_response_code(500); exit('Invalid configuration.'); }
date_default_timezone_set($cfg['timezone'] ?? 'Africa/Kigali');
if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode','1');
    ini_set('session.use_only_cookies','1');
    session_name($cfg['session_name'] ?? 'news_session');
    session_set_cookie_params([
        'lifetime' => 0, 'path' => ($cfg['base_path'] ?? '') ?: '/',
        'secure' => (($cfg['environment'] ?? 'production')==='production') || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => true, 'samesite' => 'Lax'
    ]);
    session_start();
}
if (!empty($_SESSION['user'])) {
    $last=(int)($_SESSION['last_activity']??time());
    $started=(int)($_SESSION['session_started_at']??$last);
    if (time()-$last>1800 || time()-$started>43200) { $_SESSION=[]; session_regenerate_id(true); }
    else { $_SESSION['last_activity']=time(); }
}
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-Frame-Options: SAMEORIGIN');
header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), usb=()');
header('Cross-Origin-Opener-Policy: same-origin');
header('X-Permitted-Cross-Domain-Policies: none');
header("Content-Security-Policy: default-src 'self'; img-src 'self' https://images.unsplash.com data:; style-src 'self' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; script-src 'self'; frame-src https://www.youtube-nocookie.com; base-uri 'self'; form-action 'self'; object-src 'none'");
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') header('Strict-Transport-Security: max-age=31536000');
if(str_contains(str_replace('\\','/',(string)($_SERVER['SCRIPT_NAME']??'')),'/admin/'))header('Cache-Control: no-store, private, max-age=0');
function cfg(string $key, $default = null) { global $cfg; return $cfg[$key] ?? $default; }
if((string)cfg('environment','production')==='production'){
    try{Security::encryptionKey((string)(encryption_keys()[active_encryption_key_id()]??''));Security::encryptionKey((string)cfg('audit_key',''));}
    catch(Throwable $e){error_log('Production secret configuration failed: '.$e->getMessage());http_response_code(503);exit('Secure production configuration is incomplete.');}
}
function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    try {
        $pdo = new PDO(cfg('db_dsn'), cfg('db_user'), cfg('db_password'), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
        if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME)==='sqlite') $pdo->exec('PRAGMA foreign_keys=ON');
        return $pdo;
    } catch (Throwable $e) {
        error_log('Database connection failed: '.$e->getMessage());
        http_response_code(503);
        exit('Database unavailable. Verify server configuration and PHP PDO MySQL extension.');
    }
}
function db_is_sqlite(): bool { return db()->getAttribute(PDO::ATTR_DRIVER_NAME)==='sqlite'; }
function sql_now(): string { return db_is_sqlite()?"datetime('now')":'NOW()'; }
function e(?string $v): string { return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function path(string $sub = ''): string { return rtrim((string)cfg('base_path', ''), '/') . '/' . ltrim($sub, '/'); }
function localized_path(string $sub='',string $language=''): string {
    $language=$language?:site_language();
    $url=path($sub);$parts=parse_url($url);$query=[];
    if(!empty($parts['query']))parse_str((string)$parts['query'],$query);
    $query['lang']=in_array($language,['rw','en'],true)?$language:'en';
    return (string)($parts['path']??'/').'?'.http_build_query($query);
}
function site_language(): string {
    $default=(string)cfg('default_language','en');
    if(!in_array($default,['rw','en'],true))$default='en';
    $language=(string)($_GET['lang']??$default);
    return in_array($language,['rw','en'],true)?$language:$default;
}
function language_url(string $language): string {
    $language=in_array($language,['rw','en'],true)?$language:'rw';
    $uri=(string)($_SERVER['REQUEST_URI']??'/');
    $parts=parse_url($uri);$query=[];
    if(!empty($parts['query']))parse_str((string)$parts['query'],$query);
    $query['lang']=$language;
    return (string)($parts['path']??'/').'?'.http_build_query($query);
}
function relative_time(?string $value,string $language='rw'): string {
    if(!$value)return '';$seconds=max(0,time()-strtotime($value));
    if($seconds<3600){$n=max(1,(int)floor($seconds/60));return $language==='en'?$n.' min ago':'Hashize iminota '.$n;}
    if($seconds<86400){$n=(int)floor($seconds/3600);return $language==='en'?$n.' hour'.($n===1?'':'s').' ago':'Hashize amasaha '.$n;}
    $n=(int)floor($seconds/86400);return $language==='en'?$n.' day'.($n===1?'':'s').' ago':'Hashize iminsi '.$n;
}
function absolute_url(string $p = ''): string {
    $base=rtrim((string)cfg('app_url'),'/');$bp=rtrim((string)cfg('base_path',''),'/');$basePath=rtrim((string)(parse_url($base,PHP_URL_PATH)??''),'/');
    $suffix=($bp!==''&&!str_ends_with($basePath,$bp))?$bp:'';return $base.$suffix.'/'.ltrim($p,'/');
}
function csrf_token(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function csrf_field(): string { return '<input type="hidden" name="csrf" value="'.e(csrf_token()).'">'; }
function csrf_verify(): void {
    if (!hash_equals(csrf_token(), (string)($_POST['csrf'] ?? ''))) {
        http_response_code(419); exit('Security token expired. Refresh the page and try again.');
    }
}
function current_user(): ?array { return $_SESSION['user'] ?? null; }
function end_session(string $reason=''): never {
    if($reason)error_log('Session ended: '.$reason);$_SESSION=[];if(session_status()===PHP_SESSION_ACTIVE)session_regenerate_id(true);header('Location: '.path('admin/login.php?expired=1'));exit;
}
function load_user_roles(int $userId,string $fallback): array {
    try{$rows=query_all("SELECT role_key FROM user_roles WHERE user_id=? AND scope_type='global' AND scope_id=0 AND (expires_at IS NULL OR expires_at>".sql_now().')',[$userId]);$roles=array_values(array_unique(array_column($rows,'role_key')));if(in_array('system_administrator',$roles,true)||in_array('admin',$roles,true))return ['system_administrator'];return $roles?:[$fallback];}catch(Throwable){return [$fallback];}
}
function require_login(bool $allowMfaSetup=false): array {
    if (!$sessionUser=current_user()) { header('Location: '.path('admin/login.php')); exit; }
    $fresh=query_one('SELECT id,full_name,email,role,active,session_version,mfa_required,mfa_enabled,force_password_change FROM users WHERE id=?',[(int)$sessionUser['id']]);
    if(!$fresh||!(int)$fresh['active']||(int)($fresh['session_version']??0)!==(int)($sessionUser['session_version']??0))end_session('revoked_or_inactive');
    $roles=load_user_roles((int)$fresh['id'],(string)$fresh['role']);
    $_SESSION['user']=array_merge($sessionUser,['name'=>$fresh['full_name'],'email'=>$fresh['email'],'role'=>$fresh['role'],'roles'=>$roles,'mfa_required'=>(bool)$fresh['mfa_required'],'mfa_enabled'=>(bool)$fresh['mfa_enabled'],'force_password_change'=>(bool)$fresh['force_password_change']]);
    if(!$allowMfaSetup&&!empty($fresh['mfa_required'])&&empty($fresh['mfa_enabled'])){header('Location: '.path('admin/mfa.php'));exit;}
    if(!$allowMfaSetup&&!empty($fresh['force_password_change'])&&basename((string)($_SERVER['SCRIPT_NAME']??''))!=='change-password.php'){header('Location: '.path('admin/change-password.php'));exit;}
    return $_SESSION['user'];
}
function can(string $permission): bool { $u=current_user();return EditorialWorkflow::allowsRoles((array)($u['roles']??[$u['role']??'']),$permission); }
function require_permission(string $permission): void {
    if (!can($permission)) { http_response_code(403); exit('Permission required: '.e($permission)); }
}
function is_editor(): bool { return can('article.view_all') || can('article.*'); }
function require_editor(): void { require_permission('article.view_all'); }
function article_status_labels(): array {
    return ['draft'=>'Draft','submitted'=>'Submitted','under_review'=>'Under review','revision_requested'=>'Revision requested','fact_check_pending'=>'Fact-check pending','legal_review_pending'=>'Legal review pending','approved'=>'Approved','scheduled'=>'Scheduled','published'=>'Published','unpublished'=>'Unpublished','archived'=>'Archived','retracted'=>'Retracted'];
}
function workflow_context(int $articleId): array {
    $checks=query_all('SELECT check_type,outcome FROM editorial_checks WHERE article_id=?',[$articleId]);$out=[];
    foreach($checks as $check){$out[$check['check_type'].'_outcome']=$check['outcome'];$out[$check['check_type'].'_passed']=$check['outcome']==='passed';}
    $media=query_all('SELECT m.approval_status,m.rights_expires_at,m.public_path FROM article_media am JOIN media_assets m ON m.id=am.media_id WHERE am.article_id=?',[$articleId]);$out['media_count']=count($media);$out['media_all_approved']=true;
    foreach($media as $asset)if($asset['approval_status']!=='approved'||empty($asset['public_path'])||(!empty($asset['rights_expires_at'])&&strtotime($asset['rights_expires_at'])<=time()))$out['media_all_approved']=false;
    $article=query_one('SELECT cover_image,internal_source_notes,verification_evidence,source_confidentiality_level FROM articles WHERE id=?',[$articleId]);$out['untracked_upload_media']=!empty($article['cover_image'])&&str_starts_with((string)$article['cover_image'],'uploads/')&&$out['media_count']===0;
    $out['source_classification_complete']=in_array((string)($article['source_confidentiality_level']??''),['public','internal','confidential','restricted'],true);$out['confidential_data_protected']=true;
    foreach(['internal_source_notes','verification_evidence'] as $field){$value=(string)($article[$field]??'');if($value!==''&&!str_starts_with($value,'v1.')&&!str_starts_with($value,'v2.'))$out['confidential_data_protected']=false;}
    return $out;
}
function encryption_keys(): array {$keys=cfg('app_keys',[]);if(is_array($keys)&&$keys)return $keys;$legacy=(string)cfg('app_key','');return $legacy!==''?['primary'=>$legacy]:[];}
function active_encryption_key_id(): string {return (string)cfg('active_app_key_id','primary');}
function source_access_log(int $articleId,string $action='view',string $reason='editorial_work'): void {
    $u=current_user();try{db()->prepare('INSERT INTO source_access_logs(article_id,user_id,action,reason,request_id) VALUES (?,?,?,?,?)')->execute([$articleId,$u['id']??null,substr($action,0,40),substr($reason,0,240),substr(hash('sha256',session_id().'|'.microtime(true)),0,32)]);}catch(Throwable $e){error_log('Source access audit failed: '.$e->getMessage());}
}
function record_revision(array $article,int $userId,string $note=''): void {
    $next=(int)(query_one('SELECT COALESCE(MAX(revision_no),0)+1 AS n FROM article_revisions WHERE article_id=?',[(int)$article['id']])['n']??1);
    $snapshot=json_encode($article,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR);
    db()->prepare('INSERT INTO article_revisions(article_id,revision_no,title,summary,body,snapshot_json,change_note,created_by) VALUES (?,?,?,?,?,?,?,?)')
      ->execute([(int)$article['id'],$next,$article['title'],$article['summary'],$article['body'],$snapshot,substr($note,0,600)?:null,$userId]);
}
function transition_article(int $articleId,string $to,int $userId,string|array $roles,string $explanation=''): void {
    $article=query_one('SELECT * FROM articles WHERE id=?',[$articleId]);if(!$article)throw new RuntimeException('Article missing.');
    $from=(string)$article['status'];$context=array_merge($article,workflow_context($articleId),['actor_id'=>$userId]);
    if(!EditorialWorkflow::canTransition($roles,$from,$to,$context))throw new RuntimeException('This role, ownership rule, or workflow state cannot perform that transition.');
    if(EditorialWorkflow::transitionRequiresExplanation($from,$to)&&mb_strlen(trim($explanation))<10)throw new RuntimeException('A clear explanation of at least 10 characters is required.');
    $scheduled=$to==='scheduled'?(string)($article['scheduled_at']??''):null;
    if($to==='scheduled'&&(!$scheduled||strtotime($scheduled)<=time()))throw new RuntimeException('A future schedule time is required.');
    $published=$to==='published'?($article['published_at']?:date('Y-m-d H:i:s')):$article['published_at'];
    $retracted=$to==='retracted'?date('Y-m-d H:i:s'):$article['retracted_at'];
    $submitted=$to==='submitted'?date('Y-m-d H:i:s'):($article['submitted_at']??null);$approved=$to==='approved'?date('Y-m-d H:i:s'):($article['approved_at']??null);$approvedBy=$to==='approved'?$userId:($article['approved_by']??null);$publishedBy=$to==='published'?$userId:($article['published_by']??null);
    db()->beginTransaction();
    try{
      $s=db()->prepare('UPDATE articles SET status=?,editor_id=?,published_at=?,retracted_at=?,submitted_at=?,approved_at=?,approved_by=?,published_by=?,lock_version=lock_version+1 WHERE id=? AND status=? AND lock_version=?');$s->execute([$to,$userId,$published,$retracted,$submitted,$approved,$approvedBy,$publishedBy,$articleId,$from,(int)($article['lock_version']??1)]);if($s->rowCount()!==1)throw new RuntimeException('Concurrent workflow change detected. Reload before continuing.');
      db()->prepare('INSERT INTO workflow_transitions(article_id,from_status,to_status,explanation,performed_by) VALUES (?,?,?,?,?)')->execute([$articleId,$from,$to,substr(trim($explanation),0,1000)?:null,$userId]);
      db()->commit();
    }catch(Throwable $e){if(db()->inTransaction())db()->rollBack();throw $e;}
}
function slugify(string $s): string {
    $s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s) ?: $s;
    $s = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $s));
    return trim($s, '-') ?: 'inkuru';
}
function unique_slug(string $raw, int $exclude = 0): string {
    $base = substr(slugify($raw), 0, 220); $slug = $base; $n=2;
    while (true) {
       $q=db()->prepare('SELECT id FROM articles WHERE slug=? AND id<>?'); $q->execute([$slug,$exclude]);
       if (!$q->fetch()) return $slug;
       $slug=$base.'-'.$n++;
    }
}
function query_all(string $sql,array $params=[]): array { $s=db()->prepare($sql); $s->execute($params); return $s->fetchAll(); }
function query_one(string $sql,array $params=[]): ?array { $s=db()->prepare($sql); $s->execute($params); return $s->fetch() ?: null; }
function categories(): array { return query_all('SELECT * FROM categories WHERE active=1 ORDER BY sort_order,id'); }
function published_where(): string {
    $where="a.status='published' AND a.published_at IS NOT NULL AND a.published_at <= ".sql_now();
    if((string)cfg('environment','production')==='production')$where.=' AND a.is_demo=0';
    return $where;
}
function article_select(): string { return 'SELECT a.*,c.name AS category_name,c.slug AS category_slug,u.full_name AS author_name FROM articles a JOIN categories c ON c.id=a.category_id JOIN users u ON u.id=a.author_id'; }
function public_article_select(): string { return 'SELECT a.id,a.title,a.slug,a.subheadline,a.summary,a.body,a.key_points,a.language,a.original_article_id,a.category_id,a.author_id,a.type,a.status,a.cover_image,a.image_caption,a.image_alt,a.image_credit,a.video_url,a.seo_title,a.meta_description,a.is_featured,a.is_breaking,a.is_premium,a.is_demo,a.public_sources,a.correction_note,a.published_at,a.created_at,a.updated_at,c.name AS category_name,c.slug AS category_slug,u.full_name AS author_name FROM articles a JOIN categories c ON c.id=a.category_id JOIN users u ON u.id=a.author_id'; }
function active_homepage_articles(): array {
    $now=sql_now();
    $select=preg_replace('/^SELECT /','SELECT h.slot_key AS homepage_slot,',public_article_select(),1);
    $rows=query_all($select.' JOIN homepage_slots h ON h.article_id=a.id WHERE '.published_where()." AND (h.starts_at IS NULL OR h.starts_at <= $now) AND (h.ends_at IS NULL OR h.ends_at > $now) ORDER BY h.updated_at DESC");
    $slots=[];
    foreach($rows as $row){$key=(string)$row['homepage_slot'];unset($row['homepage_slot']);if(!isset($slots[$key]))$slots[$key]=$row;}
    return $slots;
}
function active_homepage_ads(string $language): array {
    if(!in_array($language,['en','rw'],true))$language='en';$now=date('Y-m-d H:i:s');
    try{$rows=query_all("SELECT p.id placement_id,p.slot_key,p.starts_at placement_start,p.ends_at placement_end,cr.id creative_id,cr.title,cr.alt_text,cr.destination_url,cr.button_text,cr.locale,cr.disclosure,cr.public_path,c.id campaign_id,c.name campaign_name,a.name advertiser_name FROM ad_placements p JOIN ad_creatives cr ON cr.id=p.creative_id JOIN ad_campaigns c ON c.id=cr.campaign_id JOIN advertisers a ON a.id=c.advertiser_id WHERE p.status='active' AND cr.status='approved' AND cr.public_path IS NOT NULL AND c.status='active' AND p.starts_at<=? AND p.ends_at>? AND c.starts_at<=? AND c.ends_at>? AND cr.locale IN ('all',?) ORDER BY p.slot_key,p.updated_at DESC",[$now,$now,$now,$now,$language]);}
    catch(Throwable $e){error_log('Active advertisement lookup failed: '.$e->getMessage());return [];}
    $slots=[];foreach($rows as $row)if(!isset($slots[$row['slot_key']]))$slots[$row['slot_key']]=$row;return $slots;
}
function published_articles(int $limit=12, ?string $category=null, int $offset=0,?string $language=null): array {
    $limit=max(1,min(48,$limit)); $offset=max(0,$offset);
    $sql=public_article_select().' WHERE '.published_where(); $args=[];
    if ($category) { $sql.=' AND c.slug=?'; $args[]=$category; }
    if ($language&&in_array($language,['rw','en'],true)) { $sql.=' AND a.language=?'; $args[]=$language; }
    $sql.=' ORDER BY a.is_breaking DESC,a.published_at DESC LIMIT '.$limit.' OFFSET '.$offset;
    return query_all($sql,$args);
}
function display_date(?string $d): string { return $d ? date('d/m/Y · H:i', strtotime($d)) : ''; }
function estimate_readtime(string $body): int { return max(1,(int)ceil(str_word_count(strip_tags($body))/190)); }
function media_url(?string $cover): string {
    if (!$cover) return '';
    if (str_starts_with($cover,'https://images.unsplash.com/')) return $cover;
    if ((str_starts_with($cover,'uploads/') || str_starts_with($cover,'assets/editorial/')) && !str_contains($cover,'..')) return path($cover);
    return '';
}
function audit_log(string $action,string $entity,int $id=0,string $detail=''): void {
    $u=current_user();$userId=$u['id']??null;$entityId=$id?:null;$detail=substr($detail,0,600);$requestId=substr(hash('sha256',session_id().'|'.microtime(true)),0,32);$configured=(string)cfg('audit_key','');
    if($configured===''){$s=db()->prepare('INSERT INTO audit_logs (user_id,action,entity_type,entity_id,detail,request_id) VALUES (?,?,?,?,?,?)');$s->execute([$userId,$action,$entity,$entityId,$detail,$requestId]);return;}
    $key=Security::encryptionKey($configured);$ownTransaction=!db()->inTransaction();if($ownTransaction)db()->beginTransaction();
    try{$last=query_one('SELECT event_hash FROM audit_logs WHERE event_hash IS NOT NULL ORDER BY id DESC LIMIT 1');$prev=(string)($last['event_hash']??str_repeat('0',64));$payload=json_encode([$prev,$userId===null?null:(string)$userId,$action,$entity,$entityId===null?null:(string)$entityId,$detail,$requestId],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);$event=hash_hmac('sha256',(string)$payload,$key);$s=db()->prepare('INSERT INTO audit_logs (user_id,action,entity_type,entity_id,detail,request_id,prev_hash,event_hash) VALUES (?,?,?,?,?,?,?,?)');$s->execute([$userId,$action,$entity,$entityId,$detail,$requestId,$prev,$event]);if($ownTransaction)db()->commit();}
    catch(Throwable $e){if($ownTransaction&&db()->inTransaction())db()->rollBack();throw $e;}
}
function flash(string $value): void { $_SESSION['notice']=$value; }
function notice(): string { $s=(string)($_SESSION['notice'] ?? ''); unset($_SESSION['notice']); return $s; }
function article_url(array $a,string $language=''): string { return localized_path('article.php?slug='.rawurlencode($a['slug']),$language); }
function excerpt(string $s, int $len=135): string { $s=trim(strip_tags($s)); return mb_strlen($s)>$len ? mb_substr($s,0,$len).'…' : $s; }
function preferred_article_language(array $article,string $language): array {
    if(($article['language']??'')===$language)return $article;
    $root=(int)($article['original_article_id']?:$article['id']);
    $translated=query_one(public_article_select()." WHERE ".published_where()." AND a.language=? AND (a.id=? OR a.original_article_id=?) ORDER BY CASE WHEN a.id=? THEN 0 ELSE 1 END LIMIT 1",[$language,$root,$root,$root]);
    return $translated?:$article;
}
function render_header(string $pageTitle='',string $description='',string $language='',array $alternates=[],?string $socialImage=null,string $pageType='website'): void {
    $language=$language?:site_language();
    $title=$pageTitle? $pageTitle.' | '.cfg('site_name') : cfg('site_name').' — '.cfg('site_tagline');
    $description=$description ?: ($language==='en'?'Trusted, accurate and in-depth news from Rwanda, Africa and the world.':'Amakuru yizewe kandi acukumbuye yo mu Rwanda, Afurika no ku isi.');
    $language=in_array($language,['rw','en'],true)?$language:'rw';$locale=['rw'=>'rw_RW','en'=>'en_US'][$language];
    $currentScript=basename((string)(parse_url($_SERVER['REQUEST_URI']??'/',PHP_URL_PATH)??''));
    $requestPath=(string)(parse_url((string)($_SERVER['REQUEST_URI']??'/'),PHP_URL_PATH)??'/');
    $canonicalQuery=[];
    foreach(['slug','type','q','page','p','id','lang'] as $key)if(isset($_GET[$key])&&is_scalar($_GET[$key]))$canonicalQuery[$key]=(string)$_GET[$key];
    $canonical=absolute_url(ltrim($requestPath,'/').($canonicalQuery?'?'.http_build_query($canonicalQuery):''));
    if(!$alternates){foreach(['rw','en'] as $code)$alternates[$code]=absolute_url(ltrim(language_url($code),'/'));$alternates['x-default']=$alternates[(string)cfg('default_language','en')]??$alternates['en'];}
    $labels=$language==='en'
      ?['home'=>'Home','world'=>'World','business'=>'Business','technology'=>'Technology','health'=>'Health','community'=>'Community','investigations'=>'Investigations','videos'=>'Videos','about'=>'About Us','contact'=>'Contact Us','search'=>'Search news…','menu'=>'Open menu','skip'=>'Skip to stories']
      :['home'=>'Ahabanza','world'=>'Isi','business'=>'Ubukungu','technology'=>'Ikoranabuhanga','health'=>'Ubuzima','community'=>'Umuryango','investigations'=>'Iperereza','videos'=>'Video','about'=>'Abo turi bo','contact'=>'Twandikire','search'=>'Shakisha amakuru…','menu'=>'Fungura menu','skip'=>'Jya ku nkuru'];
    $nav=[
      [$labels['home'],localized_path('', $language), 'index.php'],['Rwanda',localized_path('category.php?slug=amakuru',$language),'category.php'],['Africa',localized_path('search.php?q=Africa',$language),'search.php'],[$labels['world'],localized_path('category.php?slug=isi',$language),'category.php'],
      [$labels['business'],localized_path('category.php?slug=ubukungu',$language),'category.php'],[$labels['technology'],localized_path('category.php?slug=ikoranabuhanga',$language),'category.php'],[$labels['health'],localized_path('category.php?slug=ubuzima',$language),'category.php'],[$labels['community'],localized_path('category.php?slug=umuco',$language),'category.php'],
      [$labels['investigations'],localized_path('category.php?slug=iperereza',$language),'category.php'],[$labels['videos'],localized_path('media.php',$language),'media.php'],[$labels['about'],localized_path('about.php',$language),'about.php'],[$labels['contact'],localized_path('contact.php',$language),'contact.php']
    ];
    $social=(array)cfg('social_profiles',[]);
    $socialItem=static function(string $key,string $label,string $mark)use($social):string{$url=(string)($social[$key]??'');$safe=preg_match('~^https://~i',$url)===1;if(!$safe)return '<span class="av-social is-disabled" aria-label="'.e($label).' — not configured" title="'.e($label).' — not configured">'.$mark.'</span>';return '<a class="av-social" href="'.e($url).'" target="_blank" rel="noopener noreferrer" aria-label="'.e($label).'">'.$mark.'</a>';};
    ?>
<!doctype html><html lang="<?=e($language)?>"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="theme-color" content="#101d21"><title><?=e($title)?></title><meta name="description" content="<?=e($description)?>"><link rel="stylesheet" href="<?=e(path('assets/style.css'))?>"><link rel="icon" type="image/svg+xml" href="<?=e(path('assets/favicon.svg'))?>"><link rel="alternate" type="application/rss+xml" title="RSS" href="<?=e(path('feed.php'))?>"><link rel="canonical" href="<?=e($canonical)?>"><?php foreach($alternates as $code=>$url):if(in_array($code,['rw','en','fr','x-default'],true)):?><link rel="alternate" hreflang="<?=e($code)?>" href="<?=e($url)?>"><?php endif;endforeach;?><meta property="og:site_name" content="<?=e(cfg('site_name'))?>"><meta property="og:title" content="<?=e($title)?>"><meta property="og:description" content="<?=e($description)?>"><meta property="og:type" content="<?=e($pageType)?>"><meta property="og:url" content="<?=e($canonical)?>"><meta property="og:locale" content="<?=e($locale)?>"><?php if($socialImage):?><meta property="og:image" content="<?=e($socialImage)?>"><meta name="twitter:card" content="summary_large_image"><?php else:?><meta name="twitter:card" content="summary"><?php endif;?><meta name="twitter:title" content="<?=e($title)?>"><meta name="twitter:description" content="<?=e($description)?>"></head><body>
<a class="skip-link" href="#main-content"><?=e($labels['skip'])?></a>
<header class="av-header"><div class="container av-header-inner"><a href="<?=e(localized_path('', $language))?>" class="av-logo" aria-label="<?=e(cfg('site_name'))?> — <?=e($labels['home'])?>"><img src="<?=e(path('assets/afrivox-logo.svg'))?>" alt="AFRIVOX NEWS — Africa Has a Voice."></a><div class="av-header-tools"><div class="av-language" aria-label="Language"><a href="<?=e($alternates['rw']??language_url('rw'))?>" lang="rw" <?=$language==='rw'?'aria-current="page"':''?>>Kinyarwanda</a><a href="<?=e($alternates['en']??language_url('en'))?>" lang="en" <?=$language==='en'?'aria-current="page"':''?>>English</a></div><form class="av-search" action="<?=e(path('search.php'))?>" method="get"><label class="sr-only" for="header-search"><?=e($labels['search'])?></label><input id="header-search" type="search" name="q" minlength="2" placeholder="<?=e($labels['search'])?>"><input type="hidden" name="lang" value="<?=e($language)?>"><button type="submit" aria-label="<?=e($labels['search'])?>">⌕</button></form><div class="av-socials" aria-label="Social media"><?=$socialItem('facebook','Facebook','f')?><?=$socialItem('x','X','𝕏')?><?=$socialItem('youtube','YouTube','▶')?><?=$socialItem('linkedin','LinkedIn','in')?></div><button class="av-menu-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation" aria-label="<?=e($labels['menu'])?>"><span></span><span></span><span></span></button></div></div></header>
<div class="av-nav-shell"><nav id="primary-navigation" class="container av-nav" aria-label="Primary navigation"><?php foreach($nav as [$label,$url,$script]):$active=($currentScript===''&&$script==='index.php')||$currentScript===$script;if($active&&$script==='category.php'){parse_str((string)parse_url($url,PHP_URL_QUERY),$targetQuery);$active=(string)($_GET['slug']??'')===(string)($targetQuery['slug']??'');}if($active&&$script==='search.php'){parse_str((string)parse_url($url,PHP_URL_QUERY),$targetQuery);$active=(string)($_GET['q']??'')===(string)($targetQuery['q']??'');}?><a href="<?=e($url)?>" <?=$active?'class="is-active" aria-current="page"':''?>><?=e($label)?></a><?php endforeach;?></nav></div>
<?php if ($msg=notice()):?><div class="container notice" role="status"><?=e($msg)?></div><?php endif;?>
<?php }
function render_footer():void { $language=site_language(); ?>
<footer class="av-footer"><div class="container av-footer-grid"><div><img class="av-footer-logo" src="<?=e(path('assets/afrivox-logo.svg'))?>" alt="AFRIVOX NEWS"><p>Africa Has a Voice. Trusted reporting from Rwanda, Africa and the world.</p><small>Demo-marked stories are test content and must not be presented as current news.</small></div><div><strong>NEWSROOM</strong><a href="<?=e(localized_path('about.php',$language))?>">About Us</a><a href="<?=e(localized_path('category.php?slug=iperereza',$language))?>">Investigations</a><a href="<?=e(localized_path('section.php?type=factcheck',$language))?>">Fact-checks</a><a href="<?=e(localized_path('policies.php?p=editorial',$language))?>">Editorial Policy</a><a href="<?=e(localized_path('contact.php',$language))?>">Contact / Complaints</a></div><div><strong>RESOURCES</strong><a href="<?=e(localized_path('feed.php',$language))?>">RSS Feed</a><a href="<?=e(path('sitemap.php'))?>">Sitemap</a><a href="<?=e(path('news-sitemap.php'))?>">News Sitemap</a><a href="<?=e(localized_path('privacy.php',$language))?>">Privacy Policy</a><a href="<?=e(localized_path('policies.php?p=accessibility',$language))?>">Accessibility</a></div><div><strong>NEWSLETTER</strong><p>Get selected reporting and analysis in your inbox.</p><a class="av-footer-cta" href="<?=e(localized_path('newsletter.php',$language))?>">Subscribe →</a></div></div><div class="container av-footer-bottom"><span>© <?=date('Y')?> AFRIVOX NEWS</span><span>AFRICA HAS A VOICE.</span></div></footer><script src="<?=e(path('assets/site.js'))?>" defer></script></body></html>
<?php }
function card(array $a,string $variant=''):void { $cover=media_url($a['cover_image'] ?? ''); ?>
<article class="story-card <?=e($variant)?>"><a class="story-image" href="<?=e(article_url($a,site_language()))?>" aria-label="<?=e($a['title'])?>"><?php if($cover):?><img src="<?=e($cover)?>" loading="lazy" alt="<?=e(($a['image_alt']??'') ?: ($a['image_caption'] ?: $a['title']))?>"><?php else:?><span class="image-fallback"><?=e(cfg('site_name'))?></span><?php endif;?></a><div class="story-body"><div class="story-eyebrow"><?=e($a['category_name'])?> <span>·</span> <?=e(strtoupper(str_replace('_',' ',$a['type'])))?></div><h3><a href="<?=e(article_url($a,site_language()))?>"><?=e($a['title'])?></a></h3><p><?=e(excerpt($a['summary']))?></p><div class="story-meta"><span><?=e($a['author_name'])?></span><span><?=e(display_date($a['published_at']))?></span></div><?php if($a['is_demo']):?><span class="demo-chip">DEMO / URUGERO</span><?php endif;?></div></article>
<?php }
