<?php
declare(strict_types=1);
require __DIR__.'/app/bootstrap.php';
$language=health_language();$key=(string)($_GET['p']??'editorial');
$pages=[
 'editorial'=>[
   'title'=>'Health Editorial Policy','deck'=>'Accuracy, evidence, independence and medical review.','sections'=>[
    ['Purpose and scope','UBUZIMACARE publishes health news and education for general information. Content does not provide an individual diagnosis, prescription or emergency service.'],
    ['Evidence and sources','Writers must identify primary and authoritative sources, distinguish evidence from opinion, link public sources when safe, state material uncertainty and record confidential evidence in protected newsroom fields.'],
    ['Medical review gate','Health content marked as requiring medical review cannot be approved or published until a different authorized medical reviewer records a passed review. The reviewer assesses clinical accuracy, harm risk, scope, urgent-care wording and consistency with cited evidence; the editor retains responsibility for the final publication.'],
    ['Author, reviewer and dates','Published clinical education must identify its author, publication or update date and, when medical review is required, the reviewer and review date. Material changes create a retained revision and may require a new review.'],
    ['Safety and emergencies','Articles must not promise cures, encourage unsafe self-treatment or delay urgent care. Emergency and high-risk symptoms must direct readers to qualified local services.'],
    ['Independence and advertising','Advertising, sponsorship and partnerships are visibly disclosed and handled separately from editorial approval. Payment never guarantees coverage or a favourable conclusion.'],
    ['AI use','Automation may assist transcription, tagging, translation drafts or research organisation. A named human remains accountable; fabricated sources, unreviewed medical claims and automated publication are prohibited.'],
   ]],
 'corrections'=>['title'=>'Corrections Policy','deck'=>'Visible, timely and traceable corrections.','sections'=>[
    ['How corrections work','Material errors are corrected promptly and a visible correction note explains what changed and when. Clarifications preserve the original meaning while removing ambiguity.'],
    ['Retractions','Content that is fundamentally unreliable or unsafe may be retracted with a public explanation. The record and audit trail are retained rather than silently deleted.'],
    ['Medical updates','A changed guideline or new evidence is not automatically an error. The article is reviewed, dated and updated; high-impact clinical changes require renewed medical review.'],
   ]],
 'accessibility'=>['title'=>'Accessibility Commitment','deck'=>'A WCAG 2.2 AA target for public and newsroom interfaces.','sections'=>[
    ['Current approach','Pages use semantic structure, keyboard navigation, visible focus, labelled forms, alternative text, responsive layouts and reduced-motion support.'],
    ['Report a barrier','Contact UBUZIMACARE with the page, device, browser and problem. Accessibility reports are logged, prioritised and tested before closure.'],
   ]],
 'complaints'=>['title'=>'Editorial Complaints','deck'=>'A documented route for accuracy, harm and fairness concerns.','sections'=>[
    ['Submit a complaint','Use the contact form and identify the page, disputed statement, supporting evidence and requested resolution. Do not submit confidential medical records through the public form.'],
    ['Review','An editor records the complaint, preserves the relevant version, assigns the appropriate specialist review and communicates the outcome. Appeals must be handled by someone not responsible for the initial decision where practicable.'],
   ]],
 'ai'=>['title'=>'Responsible AI Policy','deck'=>'Human accountability is mandatory.','sections'=>[
    ['Permitted assistance','AI may support transcription, translation drafts, tagging, summarisation for internal review and research organisation when confidentiality and licensing permit.'],
    ['Prohibited uses','No fabricated source, synthetic patient testimony, unverified medical recommendation, undisclosed deceptive media or unattended publication is allowed. Sensitive source material must not be sent to an unapproved external model.'],
   ]],
];
if(!isset($pages[$key])){http_response_code(404);$key='editorial';}
$page=$pages[$key];render_header($page['title'],$page['deck'],$language);
?>
<main id="main-content" class="container about-page uz-page"><span class="overline">UBUZIMACARE GOVERNANCE / <?=e(strtoupper($key))?></span><h1><?=e($page['title'])?><span class="brand-accent">.</span></h1><p class="article-deck"><?=e($page['deck'])?></p><p><strong>Version 1.0 · 12 November 2026.</strong> This public framework describes the implemented editorial controls. Named clinical reviewers, legal operator details and local regulatory approval must never be implied until configured and verified.</p><?php foreach($page['sections'] as [$heading,$body]):?><section class="policy-copy"><h2><?=e($heading)?></h2><p><?=e($body)?></p></section><?php endforeach;?><p><a href="<?=e(localized_path('contact.php',$language))?>">Submit feedback or a complaint →</a></p></main><?php render_footer(); ?>
