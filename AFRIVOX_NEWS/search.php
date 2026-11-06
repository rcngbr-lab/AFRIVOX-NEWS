<?php
declare(strict_types=1);
require __DIR__.'/app/bootstrap.php';

$language=site_language();
$q=trim(mb_substr((string)($_GET['q']??''),0,120));
$page=max(1,min(1000,(int)($_GET['page']??1)));
$limit=12;$results=[];$total=0;
$literal=trim(str_replace(['%','_'],['',''],$q));
$searchable=mb_strlen($literal)>=2;
if($searchable){
    // Remove SQL wildcard characters so the prepared LIKE expression is literal and bounded.
    $term='%'.$literal.'%';
    $where=published_where().' AND a.language=? AND (a.title LIKE ? OR a.summary LIKE ? OR a.body LIKE ? OR a.seo_title LIKE ? OR a.meta_description LIKE ?)';
    $params=[$language,$term,$term,$term,$term,$term];
    $total=(int)(query_one('SELECT COUNT(*) AS n FROM articles a WHERE '.$where,$params)['n']??0);
    $results=query_all(public_article_select().' WHERE '.$where.' ORDER BY a.published_at DESC LIMIT '.$limit.' OFFSET '.(($page-1)*$limit),$params);
}
$copy=$language==='en'
 ?['title'=>'Search','deck'=>'Search verified published stories.','placeholder'=>'Keyword or topic…','button'=>'SEARCH →','short'=>'Enter at least two characters.','none'=>'No published stories matched this search.','results'=>'results for','previous'=>'← PREVIOUS','next'=>'NEXT →']
 :['title'=>'Shakisha','deck'=>'Shakisha inkuru zatangajwe kandi zemejwe.','placeholder'=>'Andika ijambo cyangwa ingingo…','button'=>'SHAKISHA →','short'=>'Andika nibura inyuguti ebyiri.','none'=>'Nta nkuru zatangajwe zihuye n’iri jambo.','results'=>'ibisubizo bya','previous'=>'← IZABANJE','next'=>'IZIKURIKIRA →'];
render_header($copy['title'],$copy['deck'],$language);
?>
<main id="main-content" class="container content-section"><div class="category-header"><span class="overline">THE ARCHIVE</span><h1><?=e($copy['title'])?><span class="brand-accent">.</span></h1><p><?=e($copy['deck'])?></p></div><div class="av-news-layout"><div class="av-news-main">
<form method="get" action="<?=e(path('search.php'))?>" class="search-form"><label class="sr-only" for="search"><?=e($copy['placeholder'])?></label><input id="search" type="search" name="q" value="<?=e($q)?>" placeholder="<?=e($copy['placeholder'])?>" minlength="2" maxlength="120"><input type="hidden" name="lang" value="<?=e($language)?>"><button type="submit"><?=e($copy['button'])?></button></form>
<?php if($q!==''&&!$searchable):?><p class="empty-state" role="status"><?=e($copy['short'])?></p><?php elseif($searchable):?><h2 class="results-heading"><?=e((string)$total)?> <?=e($copy['results'])?> “<?=e($q)?>”</h2><?php if($results):?><div class="stories-grid archive-grid"><?php foreach($results as $article)card($article);?></div><nav class="pagination" aria-label="Search result pages"><?php if($page>1):?><a href="<?=e(localized_path('search.php?q='.rawurlencode($q).'&page='.($page-1),$language))?>"><?=e($copy['previous'])?></a><?php endif;?><span>PAGE <?=e((string)$page)?></span><?php if($page*$limit<$total):?><a href="<?=e(localized_path('search.php?q='.rawurlencode($q).'&page='.($page+1),$language))?>"><?=e($copy['next'])?></a><?php endif;?></nav><?php else:?><p class="empty-state" role="status"><?=e($copy['none'])?></p><?php endif;?><?php endif;?></div><?php render_ad_rail($language,'search-advertising-title'); ?></div></main>
<?php render_footer(); ?>
