<?php require __DIR__.'/app/bootstrap.php';
header('Content-Type: application/rss+xml; charset=UTF-8');
$language=site_language();$items=published_articles(30,null,0,$language);
echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss version="2.0"><channel><title><?=e(cfg('site_name'))?></title><link><?=e(absolute_url('?lang='.rawurlencode($language)))?></link><description><?=e(cfg('site_tagline'))?></description><language><?=e($language)?></language><?php foreach($items as $a):$url=absolute_url('article.php?slug='.rawurlencode($a['slug']).'&lang='.rawurlencode($language));?><item><title><?=e($a['title'])?></title><link><?=e($url)?></link><guid isPermaLink="true"><?=e($url)?></guid><pubDate><?=e(date(DATE_RSS,strtotime($a['published_at'])))?></pubDate><description><?=e($a['summary'])?></description></item><?php endforeach;?></channel></rss>
