<?php
declare(strict_types=1);
require __DIR__.'/app/bootstrap.php';
header('Content-Type: application/xml; charset=UTF-8');
header('Cache-Control: public, max-age=900');
$articles=query_all("SELECT a.slug,a.language,a.updated_at FROM articles a JOIN categories c ON c.id=a.category_id LEFT JOIN categories p ON p.id=c.parent_id WHERE ".published_where()." AND a.is_demo=0 AND (c.slug='ubuzima' OR p.slug='ubuzima') ORDER BY a.updated_at DESC LIMIT 5000");
$routes=['','search.php','health-news.php','health-topics.php','health-tips.php','health-tools.php','bmi-calculator.php','pregnancy-calculator.php','child-growth-tracker.php','wellness.php','health-talks.php','about.php','partners.php','contact.php','newsletter.php','privacy.php','policies.php?p=editorial','policies.php?p=corrections','policies.php?p=accessibility','policies.php?p=complaints','policies.php?p=ai'];
foreach(health_topics() as $item)$routes[]='health-topic.php?slug='.rawurlencode($item['slug']);
foreach(health_tips() as $item)$routes[]='health-tip.php?slug='.rawurlencode($item['slug']);
echo '<?xml version="1.0" encoding="UTF-8"?>';
?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"><?php foreach($routes as $route):?><url><loc><?=e(absolute_url($route))?></loc></url><?php endforeach;?><?php foreach($articles as $article):?><url><loc><?=e(absolute_url('article.php?slug='.rawurlencode($article['slug']).'&lang='.rawurlencode($article['language'])))?></loc><lastmod><?=e(date('c',strtotime($article['updated_at'])))?></lastmod></url><?php endforeach;?></urlset>
