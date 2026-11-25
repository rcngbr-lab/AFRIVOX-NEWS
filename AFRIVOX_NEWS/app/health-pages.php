<?php
declare(strict_types=1);

function health_page_copy(string $key,string $language): string
{
    $copy=[
      'rw'=>['news'=>'Amakuru y’Ubuzima','news_deck'=>'Inkuru z’ubuzima zatangajwe nyuma yo kugenzurwa n’ubwanditsi.','topics'=>'Ibyiciro by’Ubuzima','topics_deck'=>'Hitamo ingingo ushaka gusobanukirwa.','tips'=>'Inama z’Ubuzima','tips_deck'=>'Inama rusange, zoroshye kandi zidatanga isuzuma ry’indwara.','tools'=>'Ibikoresho by’Ubuzima','tools_deck'=>'Ibipimo byo kwiyigisha bikorera muri browser kandi ntibibika amakuru yawe.','empty'=>'Nta nkuru yemejwe iboneka muri iki gice.','disclaimer'=>'Ibi ni inyigisho rusange gusa. Ntibisimbura umuganga, isuzuma cyangwa ubuvuzi. Mu kibazo cyihutirwa, hamagara serivisi z’ubutabazi aho uri.','open'=>'Fungura','related'=>'Inkuru zijyanye n’iyi ngingo'],
      'en'=>['news'=>'Health News','news_deck'=>'Health reporting published after editorial review.','topics'=>'Health Topics','topics_deck'=>'Choose a subject you want to understand.','tips'=>'Health Tips','tips_deck'=>'Practical general education that does not diagnose illness.','tools'=>'Health Tools','tools_deck'=>'Educational screening tools that run in your browser and do not store your health data.','empty'=>'No approved article is available in this section.','disclaimer'=>'This is general education only. It does not replace professional advice, diagnosis or treatment. In an emergency, contact your local emergency service.','open'=>'Open','related'=>'Related health stories'],
      'fr'=>['news'=>'Actualités santé','news_deck'=>'Informations santé publiées après révision éditoriale.','topics'=>'Thèmes santé','topics_deck'=>'Choisissez un sujet à mieux comprendre.','tips'=>'Conseils santé','tips_deck'=>'Éducation générale pratique, sans diagnostic.','tools'=>'Outils santé','tools_deck'=>'Outils éducatifs exécutés dans votre navigateur, sans stockage de données de santé.','empty'=>'Aucun article approuvé dans cette section.','disclaimer'=>'Information générale uniquement. Elle ne remplace pas un avis, diagnostic ou traitement professionnel. En cas d’urgence, contactez les services locaux.','open'=>'Ouvrir','related'=>'Articles santé associés']
    ];
    return $copy[$language][$key]??$key;
}

function render_health_listing_header(string $title,string $deck,string $language): void
{
    render_header($title,$deck,$language);?>
    <main id="main-content" class="container uz-page"><header class="uz-page-header"><span>AFRIVOX NEWS</span><h1><?=e($title)?></h1><p><?=e($deck)?></p></header>
<?php }

function render_health_disclaimer(string $language): void
{ ?><div class="uz-disclaimer" role="note"><strong>ⓘ</strong><p><?=e(health_page_copy('disclaimer',$language))?></p></div><?php }

function render_health_tiles(array $items,string $language,string $type): void
{ ?><div class="uz-health-grid"><?php foreach($items as $item):$href=$type==='topic'?'health-topic.php?slug='.$item['slug']:($type==='tip'?'health-tip.php?slug='.$item['slug']:$item['slug']);?><article class="uz-health-tile"><span aria-hidden="true"><?=e($item['icon'])?></span><h2><?=e(health_text($item['name'],$language))?></h2><p><?=e(health_text($item['summary'],$language))?></p><a href="<?=e(localized_path($href,$language))?>"><?=e(health_page_copy('open',$language))?> →</a></article><?php endforeach;?></div><?php }
