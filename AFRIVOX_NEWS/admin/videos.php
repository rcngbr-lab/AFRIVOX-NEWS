<?php
require dirname(__DIR__).'/app/bootstrap.php';
$me=require_login();
$canUpload=can('media.upload');
$canApprove=can('media.approve');
$canPublish=can('workflow.publish');
if(!$canUpload&&!$canApprove&&!$canPublish){http_response_code(403);exit('Video access denied.');}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 csrf_verify();
 try{
  $action=(string)($_POST['action']??'');
  if($action==='save'){
   if(!$canUpload)throw new RuntimeException('Upload permission required.');
   $id=VideoService::save($_POST,(int)$me['id'],(int)($_POST['id']??0),can('media.edit_all'));
   audit_log('video_save','video_clip',$id);flash('Video metadata saved as draft.');
  }else{
   $id=(int)($_POST['id']??0);
   if($action==='submit'&&!$canUpload)throw new RuntimeException('Upload permission required.');
   VideoService::changeStatus($id,$action,(int)$me['id'],$canApprove,$canPublish);
   audit_log('video_'.$action,'video_clip',$id);
   flash('Video status updated: '.strtoupper($action).'.');
  }
  header('Location: '.path('admin/videos.php'));exit;
 }catch(Throwable $e){$error=$e->getMessage();}
}
$editId=(int)($_GET['edit']??0);
$editing=$editId?query_one('SELECT * FROM video_clips WHERE id=?',[$editId]):null;
if($editing&&(!$canUpload||(!can('media.edit_all')&&(int)$editing['created_by']!==(int)$me['id']))){http_response_code(403);exit('Cannot edit this video.');}
$videos=query_all('SELECT v.*,u.full_name,c.name AS category_name FROM video_clips v JOIN users u ON u.id=v.created_by JOIN categories c ON c.id=v.category_id ORDER BY v.created_at DESC LIMIT 200');
$counts=array_count_values(array_column($videos,'status'));
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Video Studio · AFRIVOX NEWS</title><link rel="stylesheet" href="<?=e(path('assets/style.css'))?>"><link rel="stylesheet" href="<?=e(path('assets/admin.css'))?>"><link rel="stylesheet" href="<?=e(path('assets/video.css'))?>"></head>
<body class="admin-body"><main class="admin-content video-admin">
<p><a href="<?=e(path('admin/index.php'))?>">← NEWSROOM</a> · <a href="<?=e(path('admin/media-library.php'))?>">MEDIA LIBRARY</a> · <a href="<?=e(path('media.php'))?>" target="_blank" rel="noopener">PUBLIC VIDEO DESK ↗</a></p>
<header class="video-studio-head"><div><span class="overline">AFRIVOX NEWS / VISUAL JOURNALISM</span><h1>Video Studio<span class="brand-accent">.</span></h1><p>Real video links, independent rights review and editorial publication. Nothing goes live automatically.</p></div><div class="video-stats"><?php foreach(['draft','pending','approved','published'] as $status):?><div><b><?=(int)($counts[$status]??0)?></b><small><?=e(strtoupper($status))?></small></div><?php endforeach;?></div></header>
<?php if($error):?><div class="form-error" role="alert"><?=e($error)?></div><?php endif;?><?php if($note=notice()):?><div class="admin-notice" role="status"><?=e($note)?></div><?php endif;?>
<?php if($canUpload):?><section class="setting-panel">
<h2><?=$editing?'Edit video':'Register a new video'?></h2><p>Video must be hosted on an authorized YouTube channel. Verify source, permissions and availability before review. Only HTTPS YouTube links are accepted.</p>
<form class="public-form video-form" method="post"><?=csrf_field()?><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?=(int)($editing['id']??0)?>">
<label>VIDEO TITLE<input name="title" maxlength="240" minlength="8" required value="<?=e($editing['title']??'')?>"></label>
<label>YOUTUBE VIDEO URL<input name="youtube_url" type="url" placeholder="https://www.youtube.com/watch?v=..." required value="<?=e(!empty($editing['provider_video_id'])?'https://www.youtube.com/watch?v='.$editing['provider_video_id']:'')?>"></label>
<label>DESCRIPTION<textarea name="description" minlength="20" maxlength="5000" rows="4" required><?=e($editing['description']??'')?></textarea></label>
<label>CATEGORY<select name="category_id" required><?php foreach(categories() as $c):?><option value="<?=(int)$c['id']?>" <?=($editing['category_id']??0)==$c['id']?'selected':''?>><?=e($c['name'])?></option><?php endforeach;?></select></label>
<label>LANGUAGE<select name="language"><option value="rw" <?=($editing['language']??'rw')==='rw'?'selected':''?>>Kinyarwanda</option><option value="en" <?=($editing['language']??'')==='en'?'selected':''?>>English</option></select></label>
<label>LINKED ARTICLE (OPTIONAL)<select name="article_id"><option value="">None — standalone video</option><?php foreach(query_all("SELECT id,title FROM articles ORDER BY created_at DESC LIMIT 100") as $article):?><option value="<?=(int)$article['id']?>" <?=($editing['article_id']??null)==$article['id']?'selected':''?>><?=e($article['title'])?></option><?php endforeach;?></select></label>
<label>VIDEO CREATOR<input name="creator" maxlength="200" required value="<?=e($editing['creator']??'')?>"></label>
<label>SOURCE ORGANIZATION<input name="source_organization" maxlength="200" required value="<?=e($editing['source_organization']??'')?>"></label>
<label>LICENSE / PERMISSION<input name="license_type" maxlength="120" placeholder="Owned / written permission / licensed" required value="<?=e($editing['license_type']??'')?>"></label>
<label>COPYRIGHT HOLDER<input name="copyright_holder" maxlength="240" required value="<?=e($editing['copyright_holder']??'')?>"></label>
<label>RIGHTS EXPIRE (OPTIONAL)<input name="rights_expires_at" type="datetime-local" value="<?=e(!empty($editing['rights_expires_at'])?date('Y-m-d\TH:i',strtotime((string)$editing['rights_expires_at'])):'')?>"></label>
<label>USAGE RESTRICTIONS<textarea name="usage_restrictions" rows="2"><?=e($editing['usage_restrictions']??'')?></textarea></label>
<button type="submit">SAVE VIDEO DRAFT →</button></form></section><?php endif;?>
<section class="setting-panel"><h2>Video review queue</h2><div class="video-admin-list">
<?php foreach($videos as $v):?><article class="video-admin-item">
<div class="video-admin-thumb"><img loading="lazy" src="<?=e(VideoService::thumbnailUrl($v['provider_video_id']))?>" alt=""></div>
<div><span class="overline"><?=e(strtoupper($v['status']))?> / <?=e($v['category_name'])?></span><h3><?=e($v['title'])?></h3><p><?=e($v['full_name'])?> · <?=e($v['creator'])?> · <?=e($v['license_type'])?></p><small>Copyright: <?=e($v['copyright_holder'])?><?php if($v['rights_expires_at']):?> · Rights expire <?=e(display_date($v['rights_expires_at']))?><?php endif;?></small><p><a href="<?=e('https://www.youtube.com/watch?v='.$v['provider_video_id'])?>" target="_blank" rel="noopener noreferrer">VERIFY SOURCE ↗</a><?php if($canUpload&&in_array($v['status'],['draft','rejected'],true)&&($v['created_by']==$me['id']||can('media.edit_all'))):?> · <a href="<?=e(path('admin/videos.php?edit='.$v['id']))?>">EDIT</a><?php endif;?></p></div>
<div class="video-actions">
<?php
$allowed=[];
if(in_array($v['status'],['draft','rejected'],true)&&$canUpload&&(int)$v['created_by']===(int)$me['id'])$allowed[]='submit';
if($v['status']==='pending'&&$canApprove&&(int)$v['created_by']!==(int)$me['id'])$allowed=['approve','reject'];
if($v['status']==='approved'&&$canPublish&&(int)$v['created_by']!==(int)$me['id'])$allowed[]='publish';
if($v['status']==='published'&&$canPublish)$allowed[]='unpublish';
?>
<?php foreach($allowed as $action):?><form method="post"><?=csrf_field()?><input type="hidden" name="id" value="<?=(int)$v['id']?>"><input type="hidden" name="action" value="<?=e($action)?>"><button type="submit"><?=e(strtoupper($action))?></button></form><?php endforeach;?>
</div></article><?php endforeach;?>
<?php if(!$videos):?><p class="empty-state">No videos registered. Add a licensed video to start the workflow.</p><?php endif;?>
</div></section></main></body></html>
