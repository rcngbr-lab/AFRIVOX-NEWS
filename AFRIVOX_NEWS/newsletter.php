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
$language=health_language();
$copy=$language==='en'?['title'=>'Health Newsletter','deck'=>'Subscribe using secure double opt-in.','heading'=>'Reliable health knowledge in your inbox.','text'=>'Enter your email. We will first send a confirmation message; nothing is sent until you confirm.','consent'=>'I agree to receive the UBUZIMACARE health newsletter and may unsubscribe at any time.','button'=>'SUBSCRIBE →']:($language==='fr'?['title'=>'Infolettre santé','deck'=>'Abonnement sécurisé avec double confirmation.','heading'=>'Des connaissances santé fiables par e-mail.','text'=>'Saisissez votre e-mail. Aucun contenu n’est envoyé avant votre confirmation.','consent'=>'J’accepte de recevoir l’infolettre UBUZIMACARE et peux me désabonner à tout moment.','button'=>'S’ABONNER →']:['title'=>'Kanyamakuru k’Ubuzima','deck'=>'Iyandikishe ukoresheje uburyo bwa double opt-in.','heading'=>'Ubumenyi bwizewe ku buzima kuri email yawe.','text'=>'Andika email yawe. Tuzabanza twohereze ubutumwa bwo kwemeza; nta nyandiko izoherezwa utabanje kwemeza.','consent'=>'Nemeye kwakira kanyamakuru k’ubuzima ka UBUZIMACARE kandi nshobora kwivana ku rutonde igihe cyose.','button'=>'IYANDIKISHE →']);
render_header($copy['title'],$copy['deck'],$language); ?>
<main id="main-content" class="container form-page uz-page"><span class="overline">UBUZIMACARE HEALTH NEWSLETTER</span><h1><?=e($copy['heading'])?></h1><p><?=e($copy['text'])?></p><?php if($error):?><div class="form-error" role="alert"><?=e($error)?></div><?php endif;?><form method="post" class="public-form"><?=csrf_field()?><label>Email address<input type="email" name="email" required maxlength="190" autocomplete="email" inputmode="email" placeholder="you@example.com"></label><label>Language<select name="locale"><option value="rw">Kinyarwanda</option><option value="en">English</option><option value="fr">Français</option></select></label><div class="honeypot" aria-hidden="true"><label>Website<input type="text" name="website" autocomplete="off" tabindex="-1"></label></div><label class="checkline"><input type="checkbox" name="consent" value="1" required> <?=e($copy['consent'])?> <a href="<?=e(path('privacy.php'))?>">Privacy Policy</a>.</label><button type="submit"><?=e($copy['button'])?></button></form></main><?php render_footer(); ?>
