<?php
require __DIR__.'/app/bootstrap.php';
$language=site_language();
$category=trim((string)($_GET['category']??''));
$where=VideoService::publicWhere('v');
$params=[];
if($category!==''){
 $where.=' AND c.slug=?';$params[]=$category;
}
$videos=query_all("SELECT v.id,v.slug,v.title,v.description,v.provider_video_id,v.creator,v.source_organization,v.published_at,v.language,v.article_id,c.name AS category_name,c.slug AS category_slug,a.slug AS article_slug,a.status AS article_status,a.published_at AS article_published,a.is_demo AS article_demo FROM video_clips v JOIN categories c ON c.id=v.category_id LEFT JOIN articles a ON a.id=v.article_id WHERE ".$where." ORDER BY v.published_at DESC LIMIT 48",$params);
$categories=query_all('SELECT DISTINCT c.name,c.slug FROM categories c JOIN video_clips v ON v.category_id=c.id WHERE '.VideoService::publicWhere('v').' ORDER BY c.name');
render_header($language==='en'?'Videos & Visual Journalism':'Video n’Amakuru mu Mashusho','',$language);
?>
<main id="main-content" class="container video-hub">
<header class="video-hub-heading"><span class="overline">AFRIVOX NEWS / VIDEO DESK</span><h1><?=$language==='en'?'Stories in':'Inkuru mu'?> <em><?=$language==='en'?'motion.':'mashusho.'?></em></h1><p><?=$language==='en'?'Verified video journalism, interviews, documentaries and visual reports.':'Amakuru ya video, ibiganiro, documentaire n’inkuru mu mashusho byasuzumwe.'?></p></header>
<nav class="video-filters" aria-label="Video categories"><a href="<?=e(localized_path('media.php',$language))?>" <?=$category===''?'aria-current="page"':''?>><?=$language==='en'?'All videos':'Video zose'?></a><?php foreach($categories as $cat):?><a href="<?=e(localized_path('media.php?category='.rawurlencode($cat['slug']),$language))?>" <?=$category===$cat['slug']?'aria-current="page"':''?>><?=e($cat['name'])?></a><?php endforeach;?></nav>
<?php if($videos):?><div class="video-grid"><?php foreach($videos as $v):
$linked=$v['article_slug']&&$v['article_status']==='published'&&$v['article_published']&&strtotime($v['article_published'])<=time()&&((string)cfg('environment','production')!=='production'||!(int)$v['article_demo']);
?>
<article class="video-card"><div class="video-player" data-video="<?=e($v['provider_video_id'])?>">
<button class="video-play" type="button" aria-label="<?=e(($language==='en'?'Play ':'Kina ').$v['title'])?>" data-video-play><img loading="lazy" src="<?=e(VideoService::thumbnailUrl($v['provider_video_id']))?>" alt=""><span aria-hidden="true" class="video-play-icon">▶</span></button>
</div><div class="video-card-copy"><span class="overline"><?=e($v['category_name'])?> · <?=e(display_date($v['published_at']))?></span><h2><?=e($v['title'])?></h2><p><?=e(excerpt($v['description'],175))?></p><small><?=e($v['creator'])?> / <?=e($v['source_organization'])?></small><?php if($linked):?><p><a href="<?=e(localized_path('article.php?slug='.rawurlencode($v['article_slug']),$language))?>"><?=$language==='en'?'Read full report →':'Soma inkuru yose →'?></a></p><?php endif;?></div></article>
<?php endforeach;?></div>
<?php else:?><p class="empty-state"><?=$language==='en'?'No approved and published videos are available.':'Nta video zemejwe kandi zatangajwe ziraboneka.'?></p><?php endif;?>
</main>
<?php render_footer(); ?>
