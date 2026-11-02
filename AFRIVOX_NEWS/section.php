<?php
require __DIR__.'/app/bootstrap.php';
$type=(string)($_GET['type']??'');
$sections=[
    'investigation'=>['Iperereza','INVESTIGATIONS','Inkuru z’iperereza zishingiye ku bimenyetso, ku masoko arinzwe no ku igenzura ryimbitse.'],
    'factcheck'=>['Fact-checks','FACT-CHECK DESK','Igenzura ry’ibivugwa n’ibimenyetso bishobora kwemezwa n’abasomyi.'],
    'analysis'=>['Isesengura','ANALYSIS','Isesengura ritandukanywa neza n’amakuru kandi rigaragaza icyo rishingiraho.'],
    'opinion'=>['Ibitekerezo','OPINION','Ibitekerezo by’abanditsi n’abatumirwa, bigaragazwa ko atari inkuru y’amakuru.'],
    'editorial'=>['Ijwi ry’ubwanditsi','EDITORIAL','Ibitekerezo byemejwe n’ubwanditsi bwa AFRIVOX NEWS, bitandukanijwe n’inkuru zisanzwe.'],
    'interview'=>['Ibiganiro','INTERVIEWS','Ibiganiro byateguwe, byagenzuwe kandi bigaragaza neza uwatanze ibisubizo.'],
];
if(!isset($sections[$type])){http_response_code(404);render_header('Urupapuro ntirubonetse');echo '<main id="main-content" class="container content-section"><h1>Urupapuro ntirubonetse.</h1></main>';render_footer();exit;}
[$title,$kicker,$description]=$sections[$type];
$page=max(1,min(1000,(int)($_GET['page']??1)));$limit=9;$offset=($page-1)*$limit;
$items=query_all(public_article_select().' WHERE '.published_where().' AND a.type=? ORDER BY a.published_at DESC LIMIT '.$limit.' OFFSET '.$offset,[$type]);
render_header($title,$description);
?>
<main id="main-content" class="container content-section"><div class="category-header"><span class="overline"><?=e($kicker)?></span><h1><?=e($title)?><span class="brand-accent">.</span></h1><p><?=e($description)?></p></div><div class="stories-grid archive-grid"><?php foreach($items as $article)card($article);?></div><?php if(!$items):?><p class="empty-state">Nta nkuru zatangajwe muri iki gice.</p><?php endif;?><nav class="pagination" aria-label="Amapaji"><?php if($page>1):?><a href="<?=e(path('section.php?type='.rawurlencode($type).'&page='.($page-1)))?>">← IZABANJE</a><?php endif;?><span>PAGE <?=e((string)$page)?></span><?php if(count($items)===$limit):?><a href="<?=e(path('section.php?type='.rawurlencode($type).'&page='.($page+1)))?>">IZIKURIKIRA →</a><?php endif;?></nav></main>
<?php render_footer(); ?>
