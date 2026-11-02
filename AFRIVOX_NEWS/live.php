<?php
require __DIR__.'/app/bootstrap.php';
$slug=substr((string)($_GET['slug']??''),0,255);
if($slug){
 $event=query_one("SELECT * FROM live_events WHERE slug=? AND status IN ('live','closed','archived')",[$slug]);
 if(!$event){http_response_code(404);render_header('Live event ntibonetse');echo '<main id="main-content" class="container content-section"><h1>Live event ntibonetse.</h1></main>';render_footer();exit;}
 $updates=query_all("SELECT lu.*,u.full_name FROM live_updates lu JOIN users u ON u.id=lu.author_id WHERE lu.live_event_id=? AND lu.status IN ('approved','corrected') ORDER BY lu.is_pinned DESC,lu.published_at DESC",[(int)$event['id']]);
 render_header($event['title'],$event['summary']);
 ?><main id="main-content" class="container content-section live-page"><header class="category-header"><span class="live-badge"><?=e($event['status']==='live'?'LIVE':'CLOSED')?></span><h1><?=e($event['title'])?></h1><p><?=e($event['summary'])?></p></header><ol class="live-timeline"><?php foreach($updates as $update):?><li class="<?=$update['is_pinned']?'pinned':''?>"><time datetime="<?=e(date(DATE_ATOM,strtotime($update['published_at'])))?>"><?=e(display_date($update['published_at']))?></time><div><?=nl2br(e($update['body']))?></div><small><?=e($update['full_name'])?></small><?php if($update['status']==='corrected'):?><p class="correction"><?=e($update['correction_note'])?></p><?php endif;?></li><?php endforeach;?></ol></main><?php render_footer();exit;
}
$events=query_all("SELECT * FROM live_events WHERE status IN ('live','closed','archived') ORDER BY COALESCE(started_at,created_at) DESC LIMIT 30");
render_header('Live reporting','Inkuru zikurikiranwa uko ziba n’ububiko bwa live coverage.');
?><main id="main-content" class="container content-section"><header class="category-header"><span class="overline">LIVE REPORTING</span><h1>Inkuru zikurikiranwa <i>ako kanya.</i></h1><p>Updates zemejwe n’ubwanditsi gusa ni zo zigaragara hano.</p></header><div class="live-list"><?php foreach($events as $event):?><a href="<?=e(path('live.php?slug='.rawurlencode($event['slug'])))?>"><span class="live-badge"><?=e($event['status'])?></span><h2><?=e($event['title'])?></h2><p><?=e($event['summary'])?></p></a><?php endforeach;?></div><?php if(!$events):?><p class="empty-state">Nta live event iriho ubu.</p><?php endif;?></main><?php render_footer(); ?>
