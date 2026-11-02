<?php
require __DIR__.'/app/bootstrap.php';
$id=max(1,(int)($_GET['id']??0));
$author=query_one('SELECT id,full_name,department,created_at FROM users WHERE id=? AND active=1',[$id]);
if(!$author){http_response_code(404);render_header('Umwanditsi ntabonetse');echo '<main id="main-content" class="container content-section"><h1>Umwanditsi ntabonetse.</h1></main>';render_footer();exit;}
$items=query_all(public_article_select().' WHERE '.published_where().' AND a.author_id=? ORDER BY a.published_at DESC LIMIT 60',[$id]);
render_header($author['full_name'],'Inkuru zanditswe na '.$author['full_name']);
?><main id="main-content" class="container content-section"><header class="category-header"><span class="overline">AUTHOR / CONTRIBUTOR</span><h1><?=e($author['full_name'])?><span class="brand-accent">.</span></h1><p><?=e($author['department']?:'AFRIVOX NEWS Editorial')?></p></header><div class="stories-grid archive-grid"><?php foreach($items as $item)card($item);?></div><?php if(!$items):?><p class="empty-state">Nta nkuru zatangajwe n’uyu mwanditsi.</p><?php endif;?></main><?php render_footer(); ?>
