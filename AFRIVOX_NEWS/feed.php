<?php require __DIR__.'/app/bootstrap.php';
header('Content-Type: application/rss+xml; charset=UTF-8');
$items=published_articles(30);
echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss version="2.0"><channel><title><?=e(cfg('site_name'))?></title><link><?=e(absolute_url())?></link><description><?=e(cfg('site_tagline'))?></description><language>rw</language><?php foreach($items as $a):?><item><title><?=e($a['title'])?></title><link><?=e(absolute_url('article.php?slug='.rawurlencode($a['slug'])))?></link><guid isPermaLink="true"><?=e(absolute_url('article.php?slug='.rawurlencode($a['slug'])))?></guid><pubDate><?=e(date(DATE_RSS,strtotime($a['published_at'])))?></pubDate><description><?=e($a['summary'])?></description></item><?php endforeach;?></channel></rss>
