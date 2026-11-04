<?php
require __DIR__.'/app/bootstrap.php';
header('Content-Type: application/xml; charset=UTF-8');
header('Cache-Control: public, max-age=300');
$cutoff=date('Y-m-d H:i:s',time()-172800);
$articles=query_all("SELECT a.title,a.slug,a.language,a.published_at FROM articles a WHERE ".published_where()." AND a.is_demo=0 AND a.published_at>=? ORDER BY a.published_at DESC LIMIT 1000",[$cutoff]);
echo '<?xml version="1.0" encoding="UTF-8"?>';
?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9"><?php foreach($articles as $article):$language=in_array($article['language'],['rw','en','fr'],true)?$article['language']:'rw';?><url><loc><?=e(absolute_url('article.php?slug='.rawurlencode($article['slug'])))?></loc><news:news><news:publication><news:name><?=e((string)cfg('site_name','AFRIVOX NEWS'))?></news:name><news:language><?=e($language)?></news:language></news:publication><news:publication_date><?=e(date('c',strtotime($article['published_at'])))?></news:publication_date><news:title><?=e(mb_substr((string)$article['title'],0,110))?></news:title></news:news></url><?php endforeach;?></urlset>
