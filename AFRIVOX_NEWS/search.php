<?php
declare(strict_types=1);
require __DIR__.'/app/bootstrap.php';

$language=health_language();
$query=trim(mb_substr((string)($_GET['q']??''),0,120));
$copy=[
 'en'=>['title'=>'Search Health Information','deck'=>'Search reviewed health stories, topics, tips and browser-based tools.','placeholder'=>'Condition, topic or keyword…','button'=>'SEARCH','count'=>'resources available','none'=>'No health resource matches this search. Try a broader term.'],
 'rw'=>['title'=>'Shakisha Amakuru y’Ubuzima','deck'=>'Shakisha inkuru zagenzuwe, ibyiciro, inama n’ibikoresho by’ubuzima.','placeholder'=>'Indwara, ingingo cyangwa ijambo…','button'=>'SHAKISHA','count'=>'ibisubizo biboneka','none'=>'Nta makuru y’ubuzima ahuye n’iri jambo. Gerageza irindi jambo.'],
 'fr'=>['title'=>'Rechercher des informations santé','deck'=>'Recherchez des articles révisés, thèmes, conseils et outils.','placeholder'=>'Maladie, thème ou mot-clé…','button'=>'RECHERCHER','count'=>'ressources disponibles','none'=>'Aucune ressource ne correspond. Essayez un terme plus général.'],
][$language];

$resources=[];
foreach(health_topics() as $item){
    $resources[]=['kind'=>'topic','icon'=>$item['icon'],'title'=>health_text($item['name'],$language),'summary'=>health_text($item['summary'],$language),'url'=>localized_path('health-topic.php?slug='.$item['slug'],$language)];
}
foreach(health_tips() as $item){
    $resources[]=['kind'=>'tip','icon'=>$item['icon'],'title'=>health_text($item['name'],$language),'summary'=>health_text($item['summary'],$language),'url'=>localized_path('health-tip.php?slug='.$item['slug'],$language)];
}
foreach(health_tools() as $item){
    $resources[]=['kind'=>'tool','icon'=>$item['icon'],'title'=>health_text($item['name'],$language),'summary'=>health_text($item['summary'],$language),'url'=>localized_path($item['slug'],$language)];
}
foreach(published_articles(100,'ubuzima',0,null) as $article){
    if(!empty($article['is_demo']))continue;
    $article=preferred_article_language($article,$language);
    $resources[]=['kind'=>'article','icon'=>'▤','title'=>(string)$article['title'],'summary'=>(string)$article['summary'],'url'=>article_url($article,$language)];
}
$initialTerms=array_values(array_filter(preg_split('/\s+/u',mb_strtolower($query))?:[]));
$initialVisible=0;
foreach($resources as &$resource){
    $resource['search_text']=mb_strtolower($resource['title'].' '.$resource['summary'].' '.$resource['kind']);
    $resource['initial_match']=true;
    foreach($initialTerms as $term)if(!str_contains($resource['search_text'],$term)){$resource['initial_match']=false;break;}
    if($resource['initial_match'])$initialVisible++;
}
unset($resource);

render_header($copy['title'],$copy['deck'],$language);
?>
<main id="main-content" class="container content-section uz-page">
 <header class="category-header uz-page-header"><span class="overline">UBUZIMACARE</span><h1><?=e($copy['title'])?><span class="brand-accent">.</span></h1><p><?=e($copy['deck'])?></p></header>
 <section class="uz-search-shell" data-health-search>
  <form method="get" action="<?=e(path('search.php'))?>" class="search-form uz-client-search" role="search">
   <label class="sr-only" for="health-search"><?=e($copy['placeholder'])?></label>
   <input id="health-search" type="search" name="q" value="<?=e($query)?>" placeholder="<?=e($copy['placeholder'])?>" maxlength="120" autocomplete="off" data-health-search-input>
   <input type="hidden" name="lang" value="<?=e($language)?>">
   <button type="submit"><?=e($copy['button'])?> →</button>
  </form>
  <p class="uz-search-count" role="status" aria-live="polite"><strong data-health-search-count><?=$initialVisible?></strong> <?=e($copy['count'])?></p>
  <div class="uz-health-grid uz-search-results" data-health-search-results>
   <?php foreach($resources as $resource):?>
   <article class="uz-health-tile" data-health-search-item data-search-text="<?=e($resource['search_text'])?>" <?=$resource['initial_match']?'':'hidden'?>>
    <span aria-hidden="true"><?=e($resource['icon'])?></span><small><?=e(strtoupper($resource['kind']))?></small>
    <h2><a href="<?=e($resource['url'])?>"><?=e($resource['title'])?></a></h2><p><?=e($resource['summary'])?></p>
   </article>
   <?php endforeach;?>
  </div>
  <p class="empty-state" data-health-search-empty <?=$initialVisible===0?'':'hidden'?>><?=e($copy['none'])?></p>
 </section>
 <?php render_health_disclaimer($language);?>
</main>
<?php render_footer(); ?>
