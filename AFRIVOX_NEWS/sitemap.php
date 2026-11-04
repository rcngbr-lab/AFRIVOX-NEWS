<?php
require __DIR__.'/app/bootstrap.php';
header('Content-Type: application/xml; charset=UTF-8');
header('Cache-Control: public, max-age=900');
$articles=query_all("SELECT a.slug,a.updated_at FROM articles a WHERE ".published_where()." AND a.is_demo=0 ORDER BY a.updated_at DESC LIMIT 5000");
$cats=categories();
$authors=query_all("SELECT DISTINCT u.id FROM users u JOIN articles a ON a.author_id=u.id WHERE u.active=1 AND ".published_where()." AND a.is_demo=0 ORDER BY u.id LIMIT 1000");
$events=query_all("SELECT slug,COALESCE(closed_at,started_at,created_at) updated_at FROM live_events WHERE status IN ('live','closed','archived') ORDER BY updated_at DESC LIMIT 500");
$static=['','search.php','live.php','media.php','section.php?type=investigation','section.php?type=factcheck','section.php?type=analysis','section.php?type=opinion','about.php','contact.php','newsletter.php','privacy.php','policies.php?p=editorial','policies.php?p=corrections','policies.php?p=accessibility'];
echo '<?xml version="1.0" encoding="UTF-8"?>';
?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"><?php foreach($static as $route):?><url><loc><?=e(absolute_url($route))?></loc></url><?php endforeach;?><?php foreach($cats as $c):?><url><loc><?=e(absolute_url('category.php?slug='.rawurlencode($c['slug'])))?></loc></url><?php endforeach;?><?php foreach($articles as $a):?><url><loc><?=e(absolute_url('article.php?slug='.rawurlencode($a['slug'])))?></loc><lastmod><?=e(date('c',strtotime($a['updated_at'])))?></lastmod></url><?php endforeach;?><?php foreach($authors as $author):?><url><loc><?=e(absolute_url('author.php?id='.$author['id']))?></loc></url><?php endforeach;?><?php foreach($events as $event):?><url><loc><?=e(absolute_url('live.php?slug='.rawurlencode($event['slug'])))?></loc><lastmod><?=e(date('c',strtotime($event['updated_at'])))?></lastmod></url><?php endforeach;?></urlset>
