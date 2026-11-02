<?php
require __DIR__.'/app/bootstrap.php';
$key=(string)($_GET['p']??'editorial');
$pages=[
 'editorial'=>['Amahame y’ubwanditsi','Accuracy, fairness, independence and accountability','AFRIVOX NEWS ishyira imbere amakuru agenzuwe, itandukanya inkuru, opinion n’ibyamamaza, kandi ikosora amakosa mu mucyo. Inyandiko zifite ibyago byinshi zisaba fact-check na legal review mbere yo gutangazwa.'],
 'corrections'=>['Gukosora no gusubiza inyandiko','Corrections, clarifications and retractions','Ikosa rifatika rikosorwa vuba kandi rigahabwa correction notice igaragara. Impinduka zose zikomeye zibikwa muri revision history. Retraction isaba impamvu yanditse kandi ntisenya audit trail.'],
 'terms'=>['Amabwiriza yo gukoresha urubuga','Terms of use','Ibirimo bigenewe amakuru rusange. Uburenganzira bw’amafoto, amajwi n’inyandiko bugomba kubahirizwa. Aya mabwiriza ni draft ikeneye kwemezwa n’umunyamategeko n’ubuyobozi mbere ya production.'],
 'accessibility'=>['Accessibility','WCAG 2.2 AA target','Urubuga rwubatswe rukoresha semantic HTML, keyboard navigation, focus igaragara, alt text n’inyandiko zisomeka. Ukeneye ubufasha cyangwa wabonye ikibazo, twandikire.'],
 'complaints'=>['Ibibazo n’ibirego','Reader complaints process','Ohereza ikibazo ukoresheje contact form, uhitemo complaints. Ubwanditsi bugomba kukakira, kukandika muri audit trail no kukurikirana mu gihe cyagenwe na policy yemejwe n’ubuyobozi.'],
 'ai'=>['Imikoreshereze ya AI','Human accountability is mandatory','AI ishobora gufasha transcription, translation drafts, tagging n’ubushakashatsi. Ntishobora guhimba inkuru cyangwa gutangaza content itagenzuwe n’umunyamakuru ubifitiye ububasha.'],
];
if(!isset($pages[$key])){http_response_code(404);$key='editorial';}
[$title,$deck,$body]=$pages[$key];render_header($title,$deck);
?><main id="main-content" class="container about-page"><span class="overline">GOVERNANCE / <?=e(strtoupper($key))?></span><h1><?=e($title)?><span class="brand-accent">.</span></h1><p class="article-deck"><?=e($deck)?></p><div class="policy-copy"><p><?=e($body)?></p><p><strong>Status:</strong> Iri ni policy framework yashyizwe muri software; ownership, contacts, response deadlines n’ibisabwa n’amategeko bigomba kwemezwa n’ubuyobozi n’abajyanama babifitiye ububasha mbere yo gutangiza.</p></div><p><a href="<?=e(path('contact.php'))?>">Twandikire cyangwa utange complaint →</a></p></main><?php render_footer(); ?>
