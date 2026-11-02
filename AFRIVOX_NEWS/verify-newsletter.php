<?php
require __DIR__.'/app/bootstrap.php';
require_once __DIR__.'/app/PublicFormService.php';
require_once __DIR__.'/app/NewsletterService.php';
$token=(string)($_POST['token']??$_GET['token']??'');
$validFormat=(bool)preg_match('/^[a-f0-9]{64}$/',$token);
$result='';
if(($_SERVER['REQUEST_METHOD']??'GET')==='POST') {
    csrf_verify();
    try {
        PublicFormService::check('newsletter_verify',20,3600);
        $result=NewsletterService::confirm($token)?'success':'invalid';
    } catch(DomainException) {
        http_response_code(429); $result='limited';
    } catch(Throwable $e) {
        error_log('Newsletter verification failed: '.$e->getMessage());
        http_response_code(503); $result='unavailable';
    }
}
render_header('Emeza newsletter','Emeza ko ushaka kwakira AFRIVOX NEWS Briefing.'); ?>
<main id="main-content" class="container form-page"><span class="overline">EMAIL CONFIRMATION</span><h1>Emeza newsletter<span class="brand-accent">.</span></h1><?php if($result==='success'):?><div class="notice" role="status">Email yawe yemejwe. Ushobora kwivana ku rutonde igihe cyose ukoresheje link izajya iba muri newsletter.</div><?php elseif($result==='invalid'||!$validFormat):?><div class="form-error" role="alert">Iyi link ntiyemewe, yarakoreshejwe cyangwa yararengeje igihe.</div><?php elseif($result==='limited'):?><div class="form-error" role="alert">Wagerageje kenshi. Ongera ugerageze nyuma.</div><?php elseif($result==='unavailable'):?><div class="form-error" role="alert">Kwemeza ntibishobotse ubu. Ongera ugerageze nyuma.</div><?php else:?><p>Kanda hepfo kugira ngo wemeze ko ushaka kwakira AFRIVOX NEWS Briefing.</p><form method="post" class="public-form"><?=csrf_field()?><input type="hidden" name="token" value="<?=e($token)?>"><button type="submit">EMEZA EMAIL →</button></form><?php endif;?></main><?php render_footer(); ?>
