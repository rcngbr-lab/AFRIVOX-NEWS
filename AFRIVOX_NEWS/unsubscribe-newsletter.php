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
        PublicFormService::check('newsletter_unsubscribe',20,3600);
        $result=NewsletterService::unsubscribe($token)?'success':'invalid';
    } catch(DomainException) {
        http_response_code(429); $result='limited';
    } catch(Throwable $e) {
        error_log('Newsletter unsubscribe failed: '.$e->getMessage());
        http_response_code(503); $result='unavailable';
    }
}
render_header('Va kuri newsletter','Iva ku rutonde rwa AFRIVOX NEWS Briefing.'); ?>
<main id="main-content" class="container form-page"><span class="overline">UNSUBSCRIBE</span><h1>Va kuri newsletter<span class="brand-accent">.</span></h1><?php if($result==='success'):?><div class="notice" role="status">Wakuwe ku rutonde rwa newsletter.</div><?php elseif($result==='invalid'||!$validFormat):?><div class="form-error" role="alert">Iyi link ntiyemewe cyangwa yarakoreshejwe.</div><?php elseif($result==='limited'):?><div class="form-error" role="alert">Wagerageje kenshi. Ongera ugerageze nyuma.</div><?php elseif($result==='unavailable'):?><div class="form-error" role="alert">Kwivana ku rutonde ntibishobotse ubu. Ongera ugerageze nyuma.</div><?php else:?><p>Kanda hepfo wemeze ko ushaka kuva kuri AFRIVOX NEWS Briefing.</p><form method="post" class="public-form"><?=csrf_field()?><input type="hidden" name="token" value="<?=e($token)?>"><button type="submit">VANA EMAIL YANJYE KU RUTONDE →</button></form><?php endif;?></main><?php render_footer(); ?>
