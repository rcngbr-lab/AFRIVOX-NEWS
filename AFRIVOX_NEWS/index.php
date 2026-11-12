<?php
declare(strict_types=1);
require __DIR__.'/app/bootstrap.php';
$language=health_language();
$copy=[
 'rw'=>['top'=>'Inkuru y’Ingenzi','welcome'=>'Ubumenyi bufasha gufata ibyemezo byiza ku buzima','deck'=>'Amakuru y’ubuzima, inyigisho zisobanutse n’ibikoresho by’ikoranabuhanga—bigenzurwa n’ubwanditsi kandi bigaragaza aho byakomotse.','read'=>'Soma byinshi','recent'=>'Amakuru Agezweho','all'=>'Reba byose','news'=>'Amakuru y’Ubuzima','topics'=>'Ibyiciro by’Ubuzima','tips'=>'Inama z’Ubuzima','tools'=>'Ibikoresho','newsletter'=>'Iyandikishe ku Kanyamakuru','newsletter_help'=>'Uba uwa mbere kubona amakuru mashya.','email'=>'Andika email yawe…','join'=>'Iyandikishe','empty'=>'Nta nkuru y’ubuzima yemejwe iratangazwa. Ubwanditsi buzazishyira hano zimaze gusuzumwa.','education'=>'UBUZIMACARE itanga ubumenyi rusange; ntabwo isimbura umuganga.'],
 'en'=>['top'=>'Top Story','welcome'=>'Knowledge for better health decisions','deck'=>'Health news, clear education and useful digital tools—editorially reviewed and transparently sourced.','read'=>'Read story','recent'=>'Latest Health News','all'=>'View all','news'=>'Health News','topics'=>'Health Topics','tips'=>'Health Tips','tools'=>'Health Tools','newsletter'=>'Subscribe to the Health Newsletter','newsletter_help'=>'Receive newly published health education first.','email'=>'Your email address…','join'=>'Subscribe','empty'=>'No reviewed health story is published yet. The newsroom will show content here after approval.','education'=>'UBUZIMACARE provides general education and does not replace a health professional.'],
 'fr'=>['top'=>'À la une','welcome'=>'Des connaissances pour de meilleures décisions de santé','deck'=>'Actualités santé, éducation claire et outils numériques utiles—révisés et sourcés.','read'=>'Lire l’article','recent'=>'Actualités récentes','all'=>'Tout voir','news'=>'Actualités santé','topics'=>'Thèmes santé','tips'=>'Conseils santé','tools'=>'Outils santé','newsletter'=>'S’abonner à l’infolettre santé','newsletter_help'=>'Recevez les nouvelles publications de santé.','email'=>'Votre adresse e-mail…','join'=>'S’abonner','empty'=>'Aucun article santé révisé n’est encore publié.','education'=>'UBUZIMACARE informe et ne remplace pas un professionnel de santé.']
][$language];
$stories=array_map(static fn(array $a):array=>preferred_article_language($a,$language),published_articles(8,'ubuzima'));
$lead=$stories[0]??null;$recent=array_slice($stories,1,3);
render_header('', $copy['deck'],$language);
?>
<main id="main-content" class="uz-home" tabindex="-1">
 <section class="container uz-hero-grid" aria-label="<?=e($copy['top'])?>">
  <article class="uz-hero-card">
   <?php if($lead):$cover=media_url($lead['cover_image']??'');?><a href="<?=e(article_url($lead,$language))?>" class="uz-hero-media"><?php if($cover):?><img src="<?=e($cover)?>" alt="<?=e(($lead['image_alt']??'')?:$lead['title'])?>" fetchpriority="high"><?php endif;?><div class="uz-hero-copy"><span><?=e($copy['top'])?></span><h1><?=e($lead['title'])?></h1><p><?=e(excerpt((string)$lead['summary'],180))?></p><b><?=e($copy['read'])?> →</b><?php if(!empty($lead['is_demo'])):?><em>DEMO / URUGERO</em><?php endif;?></div></a>
   <?php else:?><div class="uz-hero-empty"><span><?=e($copy['top'])?></span><h1><?=e($copy['welcome'])?></h1><p><?=e($copy['deck'])?></p><a href="<?=e(localized_path('health-topics.php',$language))?>"><?=e($copy['topics'])?> →</a></div><?php endif;?>
  </article>
  <aside class="uz-recent-panel"><div class="uz-panel-title"><h2><?=e($copy['recent'])?></h2><a href="<?=e(localized_path('health-news.php',$language))?>"><?=e($copy['all'])?> →</a></div><?php if($recent):foreach($recent as $story):?><article><h3><a href="<?=e(article_url($story,$language))?>"><?=e($story['title'])?></a></h3><small><?=e(display_date($story['published_at']))?></small><?php if(!empty($story['is_demo'])):?><span class="demo-chip">DEMO</span><?php endif;?></article><?php endforeach;else:?><p><?=e($copy['empty'])?></p><?php endif;?></aside>
 </section>
 <nav class="container uz-quick-access" aria-label="<?=e($copy['tools'])?>">
  <a href="<?=e(localized_path('health-news.php',$language))?>"><span>♥</span><strong><?=e($copy['news'])?></strong></a>
  <a href="<?=e(localized_path('health-topics.php',$language))?>"><span>▤</span><strong><?=e($copy['topics'])?></strong></a>
  <a href="<?=e(localized_path('health-tips.php',$language))?>"><span>☀</span><strong><?=e($copy['tips'])?></strong></a>
  <?php foreach(health_tools() as $tool):?><a href="<?=e(localized_path($tool['slug'],$language))?>"><span><?=e($tool['icon'])?></span><strong><?=e(health_text($tool['name'],$language))?></strong></a><?php endforeach;?>
 </nav>
 <section class="container uz-home-columns">
  <div><div class="uz-section-heading"><h2><?=e($copy['news'])?></h2><a href="<?=e(localized_path('health-news.php',$language))?>"><?=e($copy['all'])?> →</a></div><?php if($stories):?><div class="uz-story-grid"><?php foreach(array_slice($stories,0,4) as $story)card($story);?></div><?php else:?><p class="empty-state"><?=e($copy['empty'])?></p><?php endif;?></div>
  <aside><div class="uz-section-heading"><h2><?=e($copy['topics'])?></h2><a href="<?=e(localized_path('health-topics.php',$language))?>"><?=e($copy['all'])?> →</a></div><div class="uz-topic-mini"><?php foreach(array_slice(health_topics(),0,8) as $topic):?><a href="<?=e(localized_path('health-topic.php?slug='.$topic['slug'],$language))?>"><span><?=e($topic['icon'])?></span><b><?=e(health_text($topic['name'],$language))?></b></a><?php endforeach;?></div></aside>
 </section>
 <section class="container uz-newsletter-band"><div><span>✉</span><div><h2><?=e($copy['newsletter'])?></h2><p><?=e($copy['newsletter_help'])?></p></div></div><form action="<?=e(localized_path('newsletter.php',$language))?>" method="post"><?=csrf_field()?><input type="hidden" name="locale" value="<?=e($language)?>"><input class="honeypot" type="text" name="website" tabindex="-1" autocomplete="off"><label class="sr-only" for="home-email"><?=e($copy['email'])?></label><input id="home-email" type="email" name="email" required placeholder="<?=e($copy['email'])?>"><label class="uz-consent"><input type="checkbox" name="consent" value="1" required> <?=e($copy['education'])?></label><button><?=e($copy['join'])?></button></form></section>
 <section class="container uz-disclaimer" role="note"><strong>ⓘ</strong><p><?=e($copy['education'])?></p></section>
</main>
<?php render_footer();
