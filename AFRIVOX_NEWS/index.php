<?php
require __DIR__.'/app/bootstrap.php';

$language=site_language();
$copy=[
  'en'=>[
    'demo'=>'DEMO CONTENT','demo_text'=>'Stories labelled DEMO are test records from the CMS, not current news.','featured'=>'Featured News','all'=>'View All News','subscribe'=>'Subscribe to Our Newsletter','subscribe_help'=>'Get the latest news delivered to your inbox.','email'=>'Your email address','consent'=>'I agree to receive AFRIVOX NEWS email and can unsubscribe at any time.','latest'=>'Latest News','about'=>'We bring you trusted, accurate and in-depth news from Rwanda, across Africa and around the world. Our mission is to amplify African voices, promote positive change and keep you informed on the issues that matter.','learn'=>'Learn More','empty'=>'No published stories are available yet.','advertisement'=>'ADVERTISEMENT','popular'=>'Most Read','view'=>'View All'
  ],
  'rw'=>[
    'demo'=>'IBIRIMO BY’IGERAGEZA','demo_text'=>'Inkuru ziriho ikimenyetso cya DEMO ni records zo kugerageza CMS; ntabwo ari amakuru y’ubu.','featured'=>'Inkuru Zatoranyijwe','all'=>'Reba Amakuru Yose','subscribe'=>'Iyandikishe kuri Newsletter','subscribe_help'=>'Akira amakuru aheruka kuri email yawe.','email'=>'Email yawe','consent'=>'Nemeye kwakira AFRIVOX NEWS kuri email kandi nshobora kwivana ku rutonde igihe cyose.','latest'=>'Amakuru Aheruka','about'=>'Tubagezaho amakuru yizewe kandi acukumbuye yo mu Rwanda, Afurika no ku isi. Intego yacu ni ukumvikanisha amajwi y’Abanyafurika no kubagezaho ibibazo by’ingenzi.','learn'=>'Menya Byinshi','empty'=>'Nta nkuru zatangajwe ziraboneka.','advertisement'=>'UMWANYA W’AMAMAZA','popular'=>'Izasomwe Cyane','view'=>'Reba Zose'
  ]
];
$t=static fn(string $key):string=>$copy[$language][$key]??$key;

$articles=array_map(static fn(array $article):array=>preferred_article_language($article,$language),published_articles(48));
$slots=active_homepage_articles();
foreach($slots as $slot=>$article)$slots[$slot]=preferred_article_language($article,$language);
$used=[];
$takeSlot=static function(string $key)use($slots,&$used):?array{$story=$slots[$key]??null;if(!$story||isset($used[(int)$story['id']]))return null;$used[(int)$story['id']]=true;return $story;};
$takeNext=static function(int $count)use($articles,&$used):array{$picked=[];foreach($articles as $story){$id=(int)$story['id'];if(isset($used[$id]))continue;$used[$id]=true;$picked[]=$story;if(count($picked)>=$count)break;}return $picked;};

$lead=$takeSlot('lead')??($takeNext(1)[0]??null);
$secondary=array_values(array_filter([$takeSlot('secondary_1'),$takeSlot('secondary_2'),$takeSlot('secondary_3'),$takeSlot('secondary_4')]));
if(count($secondary)<4)$secondary=array_merge($secondary,$takeNext(4-count($secondary)));
$featured=[];
foreach($articles as $story){$storyId=(int)$story['id'];if(isset($used[$storyId]))continue;$used[$storyId]=true;$featured[$storyId]=$story;if(count($featured)>=4)break;}
$featured=array_values($featured);
$latest=array_values(array_filter($articles,static fn(array $story):bool=>!$lead||(int)$story['id']!==(int)$lead['id']));
$latest=array_slice($latest,0,6);

$categorySections=[];
foreach([
  'amakuru'=>$language==='en'?'Rwanda News':'Amakuru y’u Rwanda',
  'ubukungu'=>$language==='en'?'Business & Economy':'Ubukungu',
  'ikoranabuhanga'=>$language==='en'?'Technology':'Ikoranabuhanga',
  'ubuzima'=>$language==='en'?'Health':'Ubuzima',
  'isi'=>$language==='en'?'World News':'Amakuru yo ku Isi',
  'iperereza'=>$language==='en'?'Investigations':'Iperereza'
] as $slug=>$title){$items=array_map(static fn(array $article):array=>preferred_article_language($article,$language),published_articles(4,$slug));if($items)$categorySections[]=['slug'=>$slug,'title'=>$title,'items'=>$items];}

$trendingSelect=preg_replace('/^SELECT /','SELECT (SELECT COUNT(*) FROM analytics_events ae WHERE ae.article_id=a.id) AS view_count,',public_article_select(),1);
$trending=query_all($trendingSelect.' WHERE '.published_where().' ORDER BY view_count DESC,a.published_at DESC LIMIT 4');
if(!array_filter($trending,static fn(array $story):bool=>(int)($story['view_count']??0)>0))$trending=[];

$quickNav=[
  ['Rwanda','amakuru','#1262a8','<path d="M5 11 12 5l7 6v8h-5v-5h-4v5H5z"/>'],
  ['Africa','Africa','#08743f','<path d="M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18Zm-2 3 4 1 2 4-3 2-1 5-4-3-2-5 4-4Z"/>'],
  [$language==='en'?'World':'Isi','isi','#14549e','<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"/>'],
  [$language==='en'?'Business':'Ubukungu','ubukungu','#14628f','<path d="M4 19V9h4v10H4Zm6 0V4h4v15h-4Zm6 0v-7h4v7h-4Z"/>'],
  [$language==='en'?'Technology':'Ikoranabuhanga','ikoranabuhanga','#0b80b3','<rect x="6" y="6" width="12" height="12" rx="2"/><path d="M9 1v4m6-4v4M9 19v4m6-4v4M1 9h4m-4 6h4m14-6h4m-4 6h4M10 10h4v4h-4z"/>'],
  [$language==='en'?'Health':'Ubuzima','ubuzima','#10824b','<path d="M12 20S4 15 4 9a4 4 0 0 1 7-3 4 4 0 0 1 7 3c0 6-6 11-6 11Z"/><path d="M7 12h3l1-3 2 6 1-3h3"/>'],
  [$language==='en'?'Community':'Umuryango','umuco','#71309b','<circle cx="9" cy="9" r="3"/><circle cx="16" cy="10" r="2.5"/><path d="M3 20c0-4 2-6 6-6s6 2 6 6m0-5c3 0 5 2 5 5"/>'],
  [$language==='en'?'Investigations':'Iperereza','iperereza','#125ca5','<circle cx="10" cy="10" r="6"/><path d="m15 15 6 6"/>'],
  [$language==='en'?'Videos':'Video','media','#c81e48','<circle cx="12" cy="12" r="10"/><path d="m10 8 6 4-6 4V8Z"/>']
];

function av_home_card(array $story,string $language,string $class=''):void{$cover=media_url($story['cover_image']??'');?>
<article class="av-news-card <?=e($class)?>"><a class="av-card-image" href="<?=e(article_url($story))?>" aria-label="<?=e($story['title'])?>"><?php if($cover):?><img src="<?=e($cover)?>" loading="lazy" alt="<?=e(($story['image_alt']??'')?:($story['image_caption']??'')?:$story['title'])?>"><?php else:?><span>AFRIVOX NEWS</span><?php endif;?><b><?=e(strtoupper((string)$story['category_name']))?></b></a><div class="av-card-body"><h3><a href="<?=e(article_url($story))?>"><?=e($story['title'])?></a></h3><div class="av-meta"><span>◷ <?=e(relative_time($story['published_at'],$language))?></span></div><?php if(!empty($story['is_demo'])):?><span class="demo-chip">DEMO / URUGERO</span><?php endif;?></div></article>
<?php }

render_header('', $language==='en'?'Trusted, accurate and in-depth news from Rwanda, Africa and the world.':'Amakuru yizewe kandi acukumbuye yo mu Rwanda, Afurika no ku isi.',$language);
?>
<main id="main-content" class="afrivox-home" tabindex="-1">
<section class="container av-hero" aria-label="Featured stories">
<?php if($lead):$leadCover=media_url($lead['cover_image']??'');?>
  <article class="av-lead"><a href="<?=e(article_url($lead))?>" class="av-lead-image"><?php if($leadCover):?><img src="<?=e($leadCover)?>" fetchpriority="high" alt="<?=e(($lead['image_alt']??'')?:($lead['image_caption']??'')?:$lead['title'])?>"><?php else:?><span class="av-image-fallback">AFRIVOX NEWS</span><?php endif;?><span class="av-lead-overlay"><b><?=e(strtoupper((string)$lead['category_name']))?></b><h1><?=e($lead['title'])?></h1><p><?=e(excerpt($lead['summary'],180))?></p><span class="av-lead-meta">◷ <?=e(relative_time($lead['published_at'],$language))?></span><?php if($lead['is_demo']):?><em>DEMO / URUGERO</em><?php endif;?></span></a></article>
  <div class="av-secondary" aria-label="More top stories"><?php foreach($secondary as $story):$cover=media_url($story['cover_image']??'');?><article><a class="av-secondary-image" href="<?=e(article_url($story))?>"><?php if($cover):?><img src="<?=e($cover)?>" loading="eager" alt="<?=e(($story['image_alt']??'')?:($story['image_caption']??'')?:$story['title'])?>"><?php else:?><span>AV</span><?php endif;?></a><div><b><?=e(strtoupper((string)$story['category_name']))?></b><h2><a href="<?=e(article_url($story))?>"><?=e($story['title'])?></a></h2><small>◷ <?=e(relative_time($story['published_at'],$language))?></small><?php if($story['is_demo']):?><em>DEMO</em><?php endif;?></div></article><?php endforeach;?></div>
  <aside class="av-about-card"><img src="<?=e(path('assets/africa-mark.svg'))?>" alt="" aria-hidden="true"><h2>AFRIVOX NEWS</h2><h3>Africa Has a Voice.</h3><span></span><p><?=e($t('about'))?></p><a href="<?=e(path('about.php'))?>"><?=e($t('learn'))?> <b>→</b></a></aside>
<?php else:?><p class="empty-state"><?=e($t('empty'))?></p><?php endif;?>
</section>

<nav class="container av-quick-nav" aria-label="News categories"><?php foreach($quickNav as $quickIndex=>[$label,$slug,$color,$icon]):$url=$slug==='Africa'?path('search.php?q=Africa'):($slug==='media'?path('media.php'):path('category.php?slug='.$slug));?><a href="<?=e($url)?>" class="av-quick-item av-quick-<?=e((string)$quickIndex)?>"><span><svg viewBox="0 0 24 24" aria-hidden="true"><?=$icon?></svg></span><b><?=e($label)?></b></a><?php endforeach;?></nav>

<?php if($featured):?><section class="container av-featured"><div class="av-section-title"><h2><?=e($t('featured'))?></h2><a href="<?=e(path('search.php'))?>"><?=e($t('all'))?> <b>→</b></a></div><div class="av-featured-layout"><div class="av-featured-grid"><?php foreach($featured as $story)av_home_card($story,$language);?></div><aside class="av-home-sidebar"><section class="av-newsletter-widget"><div class="av-envelope" aria-hidden="true">✉</div><h2><?=e($t('subscribe'))?></h2><p><?=e($t('subscribe_help'))?></p><form action="<?=e(path('newsletter.php'))?>" method="post"><?=csrf_field()?><div class="honeypot" aria-hidden="true"><label>Website<input type="text" name="website" autocomplete="off" tabindex="-1"></label></div><input type="hidden" name="locale" value="<?=e($language)?>"><label class="sr-only" for="home-newsletter-email"><?=e($t('email'))?></label><div><input id="home-newsletter-email" type="email" name="email" required maxlength="190" autocomplete="email" placeholder="<?=e($t('email'))?>"><button type="submit">Subscribe</button></div><label class="av-consent"><input type="checkbox" name="consent" value="1" required> <?=e($t('consent'))?></label></form></section><section class="av-brand-banner"><img src="<?=e(path('assets/africa-mark.svg'))?>" alt="" aria-hidden="true"><small>AFRIVOX NEWS</small><strong>Africa Has a Voice.</strong><span></span></section></aside></div></section><?php endif;?>

<?php if(array_filter($articles,static fn(array $story):bool=>!empty($story['is_demo']))):?><div class="container av-demo-alert"><strong><?=e($t('demo'))?></strong><span><?=e($t('demo_text'))?></span></div><?php endif;?>

<?php if($latest):?><section class="av-soft-section"><div class="container"><div class="av-section-title"><h2><?=e($t('latest'))?></h2><a href="<?=e(path('search.php'))?>"><?=e($t('view'))?> <b>→</b></a></div><div class="av-latest-grid"><?php foreach($latest as $story)av_home_card($story,$language);?></div></div></section><?php endif;?>

<?php foreach($categorySections as $section):?><section class="container av-category-section"><div class="av-section-title"><h2><?=e($section['title'])?></h2><a href="<?=e(path('category.php?slug='.$section['slug']))?>"><?=e($t('view'))?> <b>→</b></a></div><div class="av-category-grid"><?php foreach($section['items'] as $story)av_home_card($story,$language);?></div></section><?php endforeach;?>

<?php if($trending):?><section class="container av-category-section"><div class="av-section-title"><h2><?=e($t('popular'))?></h2></div><div class="av-category-grid"><?php foreach($trending as $story)av_home_card(preferred_article_language($story,$language),$language);?></div></section><?php endif;?>

<aside class="container av-ad-placement" aria-label="Advertisement"><span><?=e($t('advertisement'))?></span><p>970 × 90</p></aside>
</main>
<?php render_footer(); ?>
