<?php
declare(strict_types=1);

$language=health_language();
$ui=[
 'rw'=>[
  'description'=>'Amakuru y’ubuzima, inyigisho n’ibikoresho byizewe bifasha Abanyarwanda gufata ibyemezo byiza.','updates'=>'UBUMENYI BUSHYA','education'=>'INYIGISHO Z’UBUZIMA','hero_title'=>'Inkingo: ubumenyi bufasha kwirinda indwara','hero_summary'=>'Menya akamaro k’inkingo, uko zifasha umuryango n’igihe cyo kugisha inama umukozi w’ubuzima ubifitiye ubumenyi.','read'=>'Soma byinshi','latest'=>'Amakuru Agezweho','all_news'=>'Reba yose','tools'=>'Ibikoresho by’Ubuzima','topics'=>'Ibyiciro by’Ubuzima','all_topics'=>'Reba ibyiciro byose','featured'=>'Inama zatoranyijwe','all_tips'=>'Reba inama zose','videos'=>'Amashusho y’Ubuzima','all_videos'=>'Reba ibiganiro','newsletter'=>'Iyandikishe ku Kanyamakuru','newsletter_help'=>'Bona inyigisho n’amakuru y’ubuzima byemejwe.','email'=>'Andika email yawe…','subscribe'=>'Iyandikishe','most_read'=>'Ibisomwe Cyane','empty_news'=>'Nta nkuru y’ubuzima yemejwe iratangazwa. Zizagaragara hano zimaze kunyura mu isuzuma ry’ubwanditsi.','empty_video'=>'Nta video yemejwe iratangazwa. Video igaragara hano gusa imaze gusuzumwa, kwemezwa n’uburenganzira bwayo.','views'=>'ibisomwa','education_note'=>'UBUZIMACARE itanga ubumenyi rusange; ntabwo isimbura inama, isuzuma cyangwa ubuvuzi bw’umuganga.'
 ],
 'en'=>[
  'description'=>'Trusted health news, education and digital tools that help Rwandans make informed decisions.','updates'=>'HEALTH UPDATES','education'=>'HEALTH EDUCATION','hero_title'=>'Vaccination: informed protection for healthier communities','hero_summary'=>'Learn why vaccination matters, how it protects communities and when to seek guidance from a qualified health professional.','read'=>'Read more','latest'=>'Latest Health News','all_news'=>'View all','tools'=>'Health Tools','topics'=>'Health Topics','all_topics'=>'View all topics','featured'=>'Featured Health Guidance','all_tips'=>'View all tips','videos'=>'Health Videos','all_videos'=>'View health talks','newsletter'=>'Subscribe to Our Newsletter','newsletter_help'=>'Receive reviewed health education and newly published stories.','email'=>'Your email address…','subscribe'=>'Subscribe','most_read'=>'Most Read','empty_news'=>'No reviewed health story is published yet. Approved newsroom content will appear here.','empty_video'=>'No approved video is published yet. Videos appear here only after accessibility, rights and editorial review.','views'=>'views','education_note'=>'UBUZIMACARE provides general education and does not replace professional medical advice, diagnosis or treatment.'
 ],
 'fr'=>[
  'description'=>'Actualités, éducation et outils de santé fiables pour des décisions éclairées au Rwanda.','updates'=>'MISES À JOUR SANTÉ','education'=>'ÉDUCATION SANTÉ','hero_title'=>'Vaccination : une protection éclairée pour des communautés en santé','hero_summary'=>'Comprendre le rôle de la vaccination et savoir quand demander conseil à un professionnel de santé qualifié.','read'=>'Lire','latest'=>'Actualités santé','all_news'=>'Tout voir','tools'=>'Outils santé','topics'=>'Thèmes santé','all_topics'=>'Voir tous les thèmes','featured'=>'Conseils sélectionnés','all_tips'=>'Voir tous les conseils','videos'=>'Vidéos santé','all_videos'=>'Voir les entretiens','newsletter'=>'S’abonner à l’infolettre','newsletter_help'=>'Recevez des informations santé révisées et les nouveaux articles.','email'=>'Votre adresse e-mail…','subscribe'=>'S’abonner','most_read'=>'Les plus lus','empty_news'=>'Aucun article santé révisé n’est encore publié.','empty_video'=>'Aucune vidéo approuvée n’est encore publiée. Les droits et l’accessibilité sont vérifiés avant publication.','views'=>'vues','education_note'=>'UBUZIMACARE informe et ne remplace pas un avis, diagnostic ou traitement médical professionnel.'
 ]
][$language];

$stories=array_values(array_filter(
    array_map(static fn(array $article):array=>preferred_article_language($article,$language),published_articles(30,'ubuzima')),
    static fn(array $article):bool=>empty($article['is_demo'])
));
$featured=array_values(array_filter($stories,static fn(array $article):bool=>!empty($article['is_featured'])));
$featuredIds=array_map(static fn(array $item):int=>(int)$item['id'],$featured);
foreach($stories as $story)if(count($featured)<4&&!in_array((int)$story['id'],$featuredIds,true)){$featured[]=$story;$featuredIds[]=(int)$story['id'];}
$lead=$featured[0]??null;
$latest=array_values(array_filter($stories,static fn(array $story):bool=>$lead===null||(int)$story['id']!==(int)$lead['id']));
$latest=array_slice($latest,0,3);
$topics=health_topics();
$tools=health_tools();
$tips=health_tips();

$viewCounts=[];
try{foreach(query_all("SELECT article_id,COUNT(*) AS views FROM analytics_events WHERE event_type='article_view' AND article_id IS NOT NULL GROUP BY article_id") as $row)$viewCounts[(int)$row['article_id']]=(int)$row['views'];}catch(Throwable){$viewCounts=[];}
$mostRead=$stories;
usort($mostRead,static fn(array $a,array $b):int=>($viewCounts[(int)$b['id']]??0)<=>($viewCounts[(int)$a['id']]??0));
$mostRead=array_slice($mostRead,0,3);

$videos=[];
try{$videos=query_all("SELECT id,provider_video_id,title,alt_text,duration_seconds FROM video_assets WHERE status='approved' AND (rights_expires_at IS NULL OR rights_expires_at>".sql_now().") AND language IN (?, 'multi') ORDER BY approved_at DESC,created_at DESC LIMIT 2",[$language]);}catch(Throwable){$videos=[];}

$updates=[];
if($stories){foreach(array_slice($stories,0,3) as $story)$updates[]=['label'=>$story['title'],'url'=>article_url($story,$language)];}
else{foreach(array_slice($tips,0,3) as $tip)$updates[]=['label'=>health_text($tip['name'],$language),'url'=>localized_path('health-tip.php?slug='.$tip['slug'],$language)];}

$leadUrl=$lead?article_url($lead,$language):localized_path('health-tip.php?slug=disease-prevention',$language);
$leadTitle=$lead?(string)$lead['title']:$ui['hero_title'];
$leadSummary=$lead?excerpt((string)$lead['summary'],175):$ui['hero_summary'];
$leadCover=$lead?media_url($lead['cover_image']??''):'';
$leadImage=$leadCover?:path('assets/health/ubuzimacare-vaccine-hero-v2.jpg');
$leadAlt=$lead?((string)($lead['image_alt']??'')?:$leadTitle):'Rwandan health professional administering a routine vaccination to an adult patient';
$tipImages=['healthy-nutrition'=>'healthy-nutrition-v1.jpg','physical-activity'=>'physical-activity-v1.jpg','healthy-sleep'=>'healthy-sleep-v1.jpg'];
$featuredTips=[];foreach(['healthy-nutrition','physical-activity','healthy-sleep'] as $slug){$tip=health_item_by_slug($tips,$slug);if($tip)$featuredTips[]=$tip;}

render_header('', $ui['description'],$language);
?>
<main id="main-content" class="uz-news-home" tabindex="-1">
 <section class="uz-news-ticker" aria-label="<?=e($ui['updates'])?>"><div class="container"><strong><span aria-hidden="true">⚡</span> <?=e($ui['updates'])?></strong><div><?php foreach($updates as $index=>$update):?><a href="<?=e($update['url'])?>"><?=e($update['label'])?></a><?php if($index<count($updates)-1):?><i aria-hidden="true"></i><?php endif;?><?php endforeach;?></div></div></section>

 <section class="container uz-news-dashboard" aria-label="<?=e($ui['latest'])?>">
  <article class="uz-news-lead"><img src="<?=e($leadImage)?>" alt="<?=e($leadAlt)?>" fetchpriority="high"><div class="uz-news-lead-copy"><span><?=e($lead?($lead['category_name']??$ui['education']):$ui['education'])?></span><h1><?=e($leadTitle)?></h1><p><?=e($leadSummary)?></p><div class="uz-news-lead-meta"><?php if($lead):?><small>▣ <?=e(display_date($lead['published_at']))?></small><small>◉ <?=number_format($viewCounts[(int)$lead['id']]??0)?> <?=e($ui['views'])?></small><?php endif;?><a href="<?=e($leadUrl)?>"><?=e($ui['read'])?> →</a></div></div></article>

  <section class="uz-news-panel uz-news-latest" aria-labelledby="latest-news-title"><header><h2 id="latest-news-title"><?=e($ui['latest'])?></h2><a href="<?=e(localized_path('health-news.php',$language))?>"><?=e($ui['all_news'])?> →</a></header><?php if($latest):?><div class="uz-news-latest-list"><?php foreach($latest as $story):$cover=media_url($story['cover_image']??'');?><article><a class="uz-news-thumb" href="<?=e(article_url($story,$language))?>"><?php if($cover):?><img src="<?=e($cover)?>" alt="<?=e(($story['image_alt']??'')?:$story['title'])?>" loading="lazy"><?php else:?><span>✚</span><?php endif;?></a><div><h3><a href="<?=e(article_url($story,$language))?>"><?=e($story['title'])?></a></h3><small>▣ <?=e(display_date($story['published_at']))?> <b>◉ <?=number_format($viewCounts[(int)$story['id']]??0)?></b></small></div></article><?php endforeach;?></div><?php else:?><p class="uz-news-empty"><?=e($ui['empty_news'])?></p><?php endif;?></section>

  <aside class="uz-news-panel uz-news-tools" aria-labelledby="health-tools-title"><header><h2 id="health-tools-title"><span aria-hidden="true">⚒</span> <?=e($ui['tools'])?></h2></header><div><?php foreach($tools as $tool):?><a href="<?=e(localized_path($tool['slug'],$language))?>"><span aria-hidden="true"><?=e($tool['icon'])?></span><span><strong><?=e(health_text($tool['name'],$language))?></strong><small><?=e(health_text($tool['summary'],$language))?></small></span><b>›</b></a><?php endforeach;?></div></aside>
 </section>

 <section class="container uz-news-topics" aria-labelledby="health-topics-title"><header><h2 id="health-topics-title"><?=e($ui['topics'])?></h2><a href="<?=e(localized_path('health-topics.php',$language))?>"><?=e($ui['all_topics'])?> →</a></header><div><?php foreach($topics as $topic):?><a href="<?=e(localized_path('health-topic.php?slug='.$topic['slug'],$language))?>"><span aria-hidden="true"><?=e($topic['icon'])?></span><strong><?=e(health_text($topic['name'],$language))?></strong></a><?php endforeach;?></div></section>

 <div class="container uz-news-lower">
  <div class="uz-news-main-column">
   <section class="uz-news-featured" aria-labelledby="featured-guidance-title"><header><h2 id="featured-guidance-title"><?=e($ui['featured'])?></h2><a href="<?=e(localized_path('health-tips.php',$language))?>"><?=e($ui['all_tips'])?> →</a></header><div><?php foreach($featuredTips as $tip):?><article><a href="<?=e(localized_path('health-tip.php?slug='.$tip['slug'],$language))?>"><img src="<?=e(path('assets/health/'.$tipImages[$tip['slug']]))?>" alt="<?=e(health_text($tip['name'],$language))?>" loading="lazy"><span><?=e($ui['education'])?></span><h3><?=e(health_text($tip['name'],$language))?></h3><p><?=e(health_text($tip['summary'],$language))?></p></a></article><?php endforeach;?></div></section>

   <section class="uz-news-videos" aria-labelledby="health-videos-title"><header><h2 id="health-videos-title"><?=e($ui['videos'])?></h2><a href="<?=e(localized_path('health-talks.php',$language))?>"><?=e($ui['all_videos'])?> →</a></header><?php if($videos):?><div class="uz-video-grid"><?php foreach($videos as $video):?><article><div class="uz-video-frame"><iframe src="<?=e(VideoService::embedUrl((string)$video['provider_video_id']))?>" title="<?=e($video['alt_text'])?>" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div><h3><?=e($video['title'])?></h3></article><?php endforeach;?></div><?php else:?><p class="uz-news-empty"><?=e($ui['empty_video'])?></p><?php endif;?></section>
  </div>

  <aside class="uz-news-sidebar">
   <section class="uz-newsletter-card"><h2><span aria-hidden="true">✉</span> <?=e($ui['newsletter'])?></h2><p><?=e($ui['newsletter_help'])?></p><form action="<?=e(localized_path('newsletter.php',$language))?>" method="post"><?=csrf_field()?><input type="hidden" name="locale" value="<?=e($language)?>"><input class="honeypot" type="text" name="website" tabindex="-1" autocomplete="off"><label class="sr-only" for="home-email"><?=e($ui['email'])?></label><input id="home-email" type="email" name="email" required placeholder="<?=e($ui['email'])?>"><label class="uz-news-consent"><input type="checkbox" name="consent" value="1" required> <?=e($ui['education_note'])?></label><button><?=e($ui['subscribe'])?></button></form></section>

   <section class="uz-most-read" aria-labelledby="most-read-title"><header><h2 id="most-read-title"><span aria-hidden="true">◆</span> <?=e($ui['most_read'])?></h2></header><?php if($mostRead):?><ol><?php foreach($mostRead as $position=>$story):?><li><span><?=$position+1?></span><div><a href="<?=e(article_url($story,$language))?>"><?=e($story['title'])?></a><small>◉ <?=number_format($viewCounts[(int)$story['id']]??0)?></small></div></li><?php endforeach;?></ol><?php else:?><p class="uz-news-empty"><?=e($ui['empty_news'])?></p><?php endif;?></section>
  </aside>
 </div>
 <section class="container uz-portal-disclaimer" role="note"><strong>ⓘ</strong><p><?=e($ui['education_note'])?></p></section>
</main>
<?php render_footer();
