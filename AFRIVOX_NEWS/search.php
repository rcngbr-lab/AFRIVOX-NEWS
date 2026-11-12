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
$copy=[
 'en'=>['title'=>'Search Health Information','deck'=>'Search published health stories, topics, tips and tools.','placeholder'=>'Condition, topic or keyword…','button'=>'SEARCH →','short'=>'Enter at least two characters.','none'=>'No published health information matched this search.','results'=>'results for','previous'=>'← PREVIOUS','next'=>'NEXT →','resources'=>'Health resources'],
 'rw'=>['title'=>'Shakisha Amakuru y’Ubuzima','deck'=>'Shakisha inkuru, ibyiciro, inama n’ibikoresho by’ubuzima.','placeholder'=>'Indwara, ingingo cyangwa ijambo…','button'=>'SHAKISHA →','short'=>'Andika nibura inyuguti ebyiri.','none'=>'Nta makuru y’ubuzima ahuye n’iri jambo.','results'=>'ibisubizo bya','previous'=>'← IZABANJE','next'=>'IZIKURIKIRA →','resources'=>'Ibyerekeye ubuzima'],
 'fr'=>['title'=>'Rechercher des informations santé','deck'=>'Rechercher articles, thèmes, conseils et outils santé.','placeholder'=>'Maladie, thème ou mot-clé…','button'=>'RECHERCHER →','short'=>'Saisissez au moins deux caractères.','none'=>'Aucune information santé correspondante.','results'=>'résultats pour','previous'=>'← PRÉCÉDENT','next'=>'SUIVANT →','resources'=>'Ressources santé']
][$language];
$healthMatches=[];
if($searchable){foreach(array_merge(health_topics(),health_tips(),health_tools()) as $item){$haystack=mb_strtolower(health_text($item['name'],$language).' '.health_text($item['summary'],$language));if(str_contains($haystack,mb_strtolower($literal)))$healthMatches[]=$item;}}
render_header($copy['title'],$copy['deck'],$language);
?>
<main id="main-content" class="container content-section uz-page"><div class="category-header uz-page-header"><span class="overline">UBUZIMACARE</span><h1><?=e($copy['title'])?><span class="brand-accent">.</span></h1><p><?=e($copy['deck'])?></p></div><div class="av-news-layout"><div class="av-news-main">
<form method="get" action="<?=e(path('search.php'))?>" class="search-form"><label class="sr-only" for="search"><?=e($copy['placeholder'])?></label><input id="search" type="search" name="q" value="<?=e($q)?>" placeholder="<?=e($copy['placeholder'])?>" minlength="2" maxlength="120"><input type="hidden" name="lang" value="<?=e($language)?>"><button type="submit"><?=e($copy['button'])?></button></form>
<?php if($q!==''&&!$searchable):?><p class="empty-state" role="status"><?=e($copy['short'])?></p><?php elseif($searchable):?><h2 class="results-heading"><?=e((string)($total+count($healthMatches)))?> <?=e($copy['results'])?> “<?=e($q)?>”</h2><?php if($healthMatches):?><h3><?=e($copy['resources'])?></h3><div class="uz-health-grid"><?php foreach($healthMatches as $item):$href=str_ends_with($item['slug'],'.php')?$item['slug']:(health_item_by_slug(health_topics(),$item['slug'])?'health-topic.php?slug='.$item['slug']:'health-tip.php?slug='.$item['slug']);?><article class="uz-health-tile"><span><?=e($item['icon'])?></span><h2><a href="<?=e(localized_path($href,$language))?>"><?=e(health_text($item['name'],$language))?></a></h2><p><?=e(health_text($item['summary'],$language))?></p></article><?php endforeach;?></div><?php endif;?><?php if($results):?><div class="stories-grid archive-grid"><?php foreach($results as $article)card($article);?></div><nav class="pagination" aria-label="Search result pages"><?php if($page>1):?><a href="<?=e(localized_path('search.php?q='.rawurlencode($q).'&page='.($page-1),$language))?>"><?=e($copy['previous'])?></a><?php endif;?><span>PAGE <?=e((string)$page)?></span><?php if($page*$limit<$total):?><a href="<?=e(localized_path('search.php?q='.rawurlencode($q).'&page='.($page+1),$language))?>"><?=e($copy['next'])?></a><?php endif;?></nav><?php elseif(!$healthMatches):?><p class="empty-state" role="status"><?=e($copy['none'])?></p><?php endif;?><?php endif;?></div><?php render_ad_rail($language,'search-advertising-title'); ?></div></main>
<?php render_footer(); ?>
