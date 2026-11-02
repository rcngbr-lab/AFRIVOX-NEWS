<?php
require __DIR__.'/app/bootstrap.php';
require_once __DIR__.'/app/PublicFormService.php';
require_once __DIR__.'/app/NewsletterService.php';

$error='';
if(($_SERVER['REQUEST_METHOD']??'GET')==='POST') {
    csrf_verify();
    if(!empty($_POST['website'])) { http_response_code(200); exit('Thank you.'); }
    $email=trim((string)($_POST['email']??''));
    $locale=(string)($_POST['locale']??'rw');
    if(!filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($email)>190) {
        $error='Andika email yemewe.';
    } elseif(empty($_POST['consent'])) {
        $error='Ugomba kubanza kwemera ko email yawe ikoreshwa mu kukwoherereza newsletter.';
    } else {
        try {
            PublicFormService::check('newsletter',6,3600);
            $result=NewsletterService::requestSubscription($email,$locale);
            $sent=false;
            if($result['confirmation_token']!==null && $result['unsubscribe_token']!==null) {
                try {
                    $sent=NewsletterService::dispatchConfirmation($email,$locale,$result['confirmation_token'],$result['unsubscribe_token']);
                } catch(Throwable $dispatchError) {
                    error_log('Newsletter confirmation dispatch failed: '.$dispatchError->getMessage());
                }
            }
            if($sent) {
                flash('Reba email yawe maze wemeze ko ushaka newsletter. Ntuzayihabwa utabanje kwemeza.');
            } else {
                flash('Ubusabe bwakiriwe. Uburyo bwo kohereza email yo kwemeza ntiburashyirwa ku murongo; nta newsletter izoherezwa utabanje kwemeza.');
            }
            header('Location: '.path('newsletter.php')); exit;
        } catch(DomainException) {
            http_response_code(429);
            $error='Wagerageje kenshi. Ongera ugerageze nyuma y’isaha.';
        } catch(Throwable $e) {
            error_log('Newsletter subscription failed: '.$e->getMessage());
            http_response_code(503);
            $error='Kwiyandikisha ntibishobotse ubu. Ongera ugerageze nyuma.';
        }
    }
}
render_header('Newsletter','Iyandikishe kuri AFRIVOX NEWS Briefing ukoresheje uburyo bwa double opt-in.'); ?>
<main id="main-content" class="container form-page"><span class="overline">THE AFRIVOX NEWS BRIEFING</span><h1>Amakuru akwiye<br><i>kugeraho.</i></h1><p>Andika email yawe. Tuzabanza tukohereze ubutumwa bwo kwemeza; nta newsletter izoherezwa utabanje gukanda kuri ubwo butumwa.</p><?php if($error):?><div class="form-error" role="alert"><?=e($error)?></div><?php endif;?><form method="post" class="public-form"><?=csrf_field()?><label>Email address<input type="email" name="email" required maxlength="190" autocomplete="email" inputmode="email" placeholder="you@example.com"></label><label>Ururimi<select name="locale"><option value="rw">Kinyarwanda</option><option value="en">English</option><option value="fr">Français</option></select></label><div class="honeypot" aria-hidden="true"><label>Website<input type="text" name="website" autocomplete="off" tabindex="-1"></label></div><label class="checkline"><input type="checkbox" name="consent" value="1" required> Nemeye ko email yanjye ikoreshwa mu kunyoherereza AFRIVOX NEWS Briefing. Nshobora kwivana ku rutonde igihe cyose. <a href="<?=e(path('privacy.php'))?>">Privacy Policy</a>.</label><button type="submit">IYANDIKISHE →</button></form></main><?php render_footer(); ?>
