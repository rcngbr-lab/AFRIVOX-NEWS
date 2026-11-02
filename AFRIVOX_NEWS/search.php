<?php require __DIR__.'/app/bootstrap.php';
$q=trim(mb_substr((string)($_GET['q']??''),0,120));
$results=[];
if(mb_strlen($q)>=2) { $term='%'.str_replace(['%','_'],['\\%','\\_'],$q).'%'; $results=query_all(public_article_select()." WHERE ".published_where()." AND (a.title LIKE ? OR a.summary LIKE ? OR a.body LIKE ?) ORDER BY a.published_at DESC LIMIT 48",[$term,$term,$term]); }
render_header('Shakisha'); ?>
<main class="container content-section"><div class="category-header"><span class="overline">THE ARCHIVE</span><h1>Shakisha<span class="brand-accent">.</span></h1><p>Shakisha inkuru zose zatangajwe.</p></div><form method="get" action="<?=e(path('search.php'))?>" class="search-form"><label class="sr-only" for="search">Ijambo ushaka</label><input id="search" type="search" name="q" value="<?=e($q)?>" placeholder="Andika ijambo cyangwa ingingo..." minlength="2"><button type="submit">SHAKISHA →</button></form><?php if($q!==''):?><h2 class="results-heading"><?=count($results)?> ibisubizo bya “<?=e($q)?>”</h2><div class="stories-grid archive-grid"><?php foreach($results as $a) card($a);?></div><?php endif;?></main><?php render_footer(); ?>
