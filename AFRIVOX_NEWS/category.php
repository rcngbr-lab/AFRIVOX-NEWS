<?php require __DIR__.'/app/bootstrap.php';
$slug=substr((string)($_GET['slug']??''),0,120);
$c=query_one('SELECT * FROM categories WHERE slug=? AND active=1',[$slug]);
if(!$c){http_response_code(404);render_header('Icyiciro ntikibonetse');echo '<main class="container content-section"><h1>Icyiciro ntikibonetse.</h1></main>';render_footer();exit;}
$page=max(1,min(1000,(int)($_GET['page']??1))); $limit=9;
$items=published_articles($limit,$slug,($page-1)*$limit);
render_header($c['name']); ?>
<main class="container content-section"><div class="category-header"><span class="overline">THE <?=e(strtoupper($c['slug']))?> DESK</span><h1><?=e($c['name'])?><span class="brand-accent">.</span></h1><p>Inkuru, isesengura n'ibitekerezo byatoranyijwe n'ubwanditsi.</p></div><div class="stories-grid archive-grid"><?php foreach($items as $a) card($a);?></div><?php if(!$items):?><p class="empty-state">Nta nkuru ziboneka muri iki cyiciro.</p><?php endif;?><div class="pagination"><?php if($page>1):?><a href="<?=e(path('category.php?slug='.rawurlencode($slug).'&page='.($page-1)))?>">← IZABANJE</a><?php endif;?><span>PAGE <?=e((string)$page)?></span><?php if(count($items)===$limit):?><a href="<?=e(path('category.php?slug='.rawurlencode($slug).'&page='.($page+1)))?>">IZIKURIKIRA →</a><?php endif;?></div></main><?php render_footer(); ?>
