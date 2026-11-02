<?php
require __DIR__.'/app/bootstrap.php';
require_once __DIR__.'/app/PublicFormService.php';

$error='';
$allowedTypes=['general','correction','complaint','news_tip','rights','privacy'];
if(($_SERVER['REQUEST_METHOD']??'GET')==='POST') {
    csrf_verify();
    if(!empty($_POST['website'])) { http_response_code(200); exit('Thank you.'); }
    $name=trim((string)($_POST['name']??''));
    $email=trim((string)($_POST['email']??''));
    $subject=trim((string)($_POST['subject']??''));
    $message=trim((string)($_POST['message']??''));
    $messageType=(string)($_POST['message_type']??'general');
    if(!in_array($messageType,$allowedTypes,true)) $messageType='general';
    if(mb_strlen($name)<2||mb_strlen($name)>160||!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($email)>190||mb_strlen($subject)<3||mb_strlen($subject)>160||mb_strlen($message)<10||mb_strlen($message)>8000) {
        $error='Suzuma amakuru winjije. Ubutumwa bugomba kugira nibura inyuguti 10.';
    } else {
        try {
            PublicFormService::check('contact',5,3600);
            db()->prepare('INSERT INTO contact_messages (name,email,subject,message,message_type) VALUES (?,?,?,?,?)')->execute([$name,$email,$subject,$message,$messageType]);
            flash('Ubutumwa bwakiriwe n’itsinda rya AFRIVOX NEWS. Tuzabusuzuma dukurikije ubwoko bwabwo.');
            header('Location: '.path('contact.php')); exit;
        } catch(DomainException) {
            http_response_code(429);
            $error='Wohereje ubutumwa bwinshi. Ongera ugerageze nyuma y’isaha.';
        } catch(Throwable $e) {
            error_log('Public contact form failed: '.$e->getMessage());
            http_response_code(503);
            $error='Ubutumwa ntibushoboye kwakirwa ubu. Ongera ugerageze nyuma.';
        }
    }
}
render_header('Twandikire','Ohereza ikosora, ikirego, amakuru cyangwa ubundi butumwa kuri AFRIVOX NEWS.'); ?>
<main id="main-content" class="container form-page"><span class="overline">READER DESK / CONTACT</span><h1>Twandikire<span class="brand-accent">.</span></h1><p>Ufite igitekerezo, ikosora, ikirego cyangwa amakuru? Hitamo ubwoko bw’ubutumwa. Iyi form si uburyo bwizewe bwo gutanga amabanga: ntushyiremo amazina y’amasoko y’ibanga cyangwa ibindi byatuma umuntu amenyekana.</p><?php if($error):?><div class="form-error" role="alert"><?=e($error)?></div><?php endif;?><form method="post" class="public-form"><?=csrf_field()?><label>Ubwoko bw’ubutumwa<select name="message_type" required><option value="general">Rusange</option><option value="correction">Gusaba ikosora</option><option value="complaint">Ikirego</option><option value="news_tip">Igitekerezo cy’inkuru (nta banga)</option><option value="rights">Uburenganzira / copyright</option><option value="privacy">Privacy / amakuru bwite</option></select></label><label>Amazina<input name="name" required maxlength="160" autocomplete="name"></label><label>Email<input name="email" type="email" required maxlength="190" autocomplete="email" inputmode="email"></label><label>Ingingo<input name="subject" required maxlength="160" placeholder="Vuga muri make icyo ubutumwa bureba"></label><label>Ubutumwa<textarea name="message" required rows="6" minlength="10" maxlength="8000"></textarea></label><div class="honeypot" aria-hidden="true"><label>Website<input type="text" name="website" autocomplete="off" tabindex="-1"></label></div><button type="submit">OHEREZA UBUTUMWA →</button></form></main><?php render_footer(); ?>
