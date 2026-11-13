<?php
declare(strict_types=1);

/** Central, server-enforced authorization and editorial lifecycle policy. */
final class EditorialWorkflow
{
    public const STATUSES=['draft','submitted','under_review','revision_requested','fact_check_pending','legal_review_pending','approved','scheduled','published','unpublished','retracted','archived'];

    private const ROLE_PERMISSIONS=[
        // Technical administrators deliberately have no editorial approve/publish authority.
        'system_administrator'=>['staff.create','staff.manage_roles','staff.suspend','staff.revoke_sessions','system.configure','security.manage','backup.manage','audit.view','analytics.system'],
        'admin'=>['staff.create','staff.manage_roles','staff.suspend','staff.revoke_sessions','system.configure','security.manage','backup.manage','audit.view','analytics.system'],
        'publisher'=>['analytics.view','commercial.reports','advertising.approve','finance.view','finance.reconcile','settings.view'],
        'editor_in_chief'=>['article.*','workflow.*','pitch.create','pitch.review','assignment.manage','assignment.work','live.manage','live.update','live.approve','checks.sources','checks.fairness','checks.right_of_reply','checks.copy','checks.accessibility','source.view_confidential','source.manage_confidential','media.approve','media.manage_rights','staff.view','audit.view','homepage.manage','category.manage','analytics.view','inbox.manage'],
        'managing_editor'=>['article.*','workflow.*','pitch.create','pitch.review','assignment.manage','assignment.work','live.manage','live.update','live.approve','checks.sources','checks.fairness','checks.right_of_reply','checks.copy','checks.accessibility','source.view_confidential','source.manage_confidential','media.approve','media.manage_rights','audit.view','homepage.manage','category.manage','analytics.view','inbox.manage','newsletter.view'],
        'section_editor'=>['article.view_all','article.edit_all','workflow.review','workflow.approve','workflow.publish','pitch.create','pitch.review','assignment.manage','assignment.work','live.manage','live.update','live.approve','checks.sources','checks.fairness','checks.right_of_reply','checks.accessibility','source.view_confidential','media.use','homepage.manage'],
        'editor'=>['article.view_all','article.edit_all','workflow.review','workflow.approve','workflow.publish','pitch.create','pitch.review','assignment.manage','assignment.work','live.manage','live.update','live.approve','checks.sources','checks.fairness','checks.right_of_reply','checks.accessibility','source.view_confidential','media.use','inbox.manage','newsletter.view','audit.view'],
        'reporter'=>['article.create','article.edit_own','workflow.submit','pitch.create','assignment.work','live.update','source.manage_own','media.upload','media.use'],
        'investigative_reporter'=>['article.create','article.edit_own','workflow.submit','pitch.create','assignment.work','live.update','source.manage_own','media.upload','media.use'],
        'fact_checker'=>['article.view_all','workflow.fact_check','checks.fact_check','source.view_confidential'],
        'copy_editor'=>['article.view_all','article.edit_all','workflow.review','checks.copy'],
        'photographer'=>['media.upload','media.edit_own'],
        'video_producer'=>['media.upload','media.edit_own'],
        'multimedia_editor'=>['media.upload','media.edit_all','media.approve','media.manage_rights','checks.media_rights'],
        'legal_reviewer'=>['article.view_all','workflow.legal_review','checks.legal','source.view_confidential','source.manage_confidential','inbox.manage'],
        'medical_reviewer'=>['article.view_all','checks.medical'],
        'seo_audience_editor'=>['article.view_all','article.seo','analytics.view','checks.seo','newsletter.manage','audience.view'],
        'social_media_manager'=>['article.view_published','social.publish'],
        'advertising_manager'=>['advertising.manage','commercial.reports','analytics.commercial'],
        'finance_officer'=>['finance.view','finance.invoice.create','finance.payment.record','commercial.reports'],
        'subscriber_support'=>['audience.view','audience.manage','subscriber.manage','inbox.manage'],
        'external_contributor'=>['article.create','article.edit_own','workflow.submit','pitch.create','assignment.work','source.manage_own'],
        'registered_reader'=>['reader.preferences','reader.bookmarks'],
    ];

    private const TRANSITIONS=[
        'draft'=>['submitted'],
        'submitted'=>['under_review','revision_requested'],
        'under_review'=>['revision_requested','fact_check_pending','legal_review_pending','approved'],
        'revision_requested'=>['submitted'],
        'fact_check_pending'=>['under_review','legal_review_pending','approved','revision_requested'],
        'legal_review_pending'=>['under_review','fact_check_pending','approved','revision_requested'],
        'approved'=>['scheduled','published','under_review'],
        'scheduled'=>['published','approved','archived'],
        'published'=>['unpublished','retracted'],
        'unpublished'=>['under_review','archived'],
        'archived'=>['draft'],
        'retracted'=>[],
    ];

    public static function permissionsFor(string $role): array{return self::ROLE_PERMISSIONS[$role]??[];}

    public static function allows(string $role,string $permission): bool
    {
        foreach(self::permissionsFor($role) as $granted){if($granted===$permission)return true;if(str_ends_with($granted,'.*')&&str_starts_with($permission,substr($granted,0,-1)))return true;}return false;
    }

    public static function allowsRoles(array $roles,string $permission): bool
    {foreach(array_unique($roles) as $role)if(self::allows((string)$role,$permission))return true;return false;}

    public static function permissionForCheck(string $checkType): ?string
    {
        return in_array($checkType,['sources','fairness','right_of_reply','fact_check','legal','medical','copy','media_rights','seo','accessibility'],true)?'checks.'.$checkType:null;
    }

    public static function canCompleteCheck(string|array $roles,string $checkType): bool
    {
        $permission=self::permissionForCheck($checkType);
        if($permission===null)return false;
        if(self::allowsRoles((array)$roles,$permission))return true;
        return $checkType==='media_rights'&&self::allowsRoles((array)$roles,'media.manage_rights');
    }

    private static function checkFailed(array $article): bool
    {
        foreach(['sources','fairness','right_of_reply','fact_check','legal','medical','copy','media_rights','seo','accessibility'] as $type){if(($article[$type.'_outcome']??null)==='failed')return true;}
        return false;
    }

    private static function publicationGatesPass(array $article): bool
    {
        return self::publicationGateFailures($article)===[];
    }

    public static function canTransition(string|array $roles,string $from,string $to,array $article=[]): bool
    {
        $roles=(array)$roles;if(!in_array($to,self::TRANSITIONS[$from]??[],true))return false;
        $allows=fn(string $permission):bool=>self::allowsRoles($roles,$permission)||self::allowsRoles($roles,'workflow.*');
        if($to==='submitted')return $allows('workflow.submit');
        if($to==='revision_requested')return $allows('workflow.review')||$allows('workflow.fact_check')||$allows('workflow.legal_review');
        if($to==='under_review')return $allows('workflow.review')||($from==='fact_check_pending'&&$allows('workflow.fact_check'))||($from==='legal_review_pending'&&$allows('workflow.legal_review'))||($from==='unpublished'&&$allows('workflow.publish'));
        if($to==='fact_check_pending'||$to==='legal_review_pending')return $allows('workflow.review');
        if($from==='fact_check_pending'&&!$allows('workflow.fact_check'))return false;
        if($from==='legal_review_pending'&&!$allows('workflow.legal_review'))return false;
        if($to==='approved'){
            if(!$allows('workflow.approve'))return false;
            if(isset($article['actor_id'],$article['author_id'])&&(int)$article['actor_id']===(int)$article['author_id'])return false;
            return self::publicationGatesPass($article);
        }
        if(in_array($to,['scheduled','published'],true)){
            if(!$allows('workflow.publish'))return false;
            if(empty($article['approved_by']))return false;
            if((string)($article['risk_level']??'')==='critical'&&isset($article['actor_id'])&&in_array((int)$article['actor_id'],[(int)($article['author_id']??0),(int)$article['approved_by']],true))return false;
            return self::publicationGatesPass($article);
        }
        if(in_array($to,['unpublished','retracted','archived'],true))return $allows('workflow.publish');
        return $allows('workflow.review');
    }

    public static function transitionRequiresExplanation(string $from,string $to): bool
    {return in_array($to,['revision_requested','unpublished','retracted','archived'],true);}

    public static function contentEditableStatus(string $status): bool
    {
        return in_array($status,['draft','submitted','revision_requested','under_review','fact_check_pending','legal_review_pending'],true);
    }

    public static function checkSubmissionError(string $status,string $outcome,string $notes): ?string
    {
        if(!in_array($status,['submitted','revision_requested','under_review','fact_check_pending','legal_review_pending'],true))return 'Editorial checks are locked in this workflow state. Return the story to review first.';
        if(!in_array($outcome,['pending','passed','failed','not_applicable'],true))return 'Invalid editorial-check outcome.';
        if($outcome!=='pending'&&mb_strlen(trim($notes))<10)return 'Passed, failed and not-applicable checks require an evidence note of at least 10 characters.';
        return null;
    }

    public static function republishCorrectionError(array $article,string $to): ?string
    {
        if($to==='published'&&!empty($article['published_at'])&&mb_strlen(trim((string)($article['correction_note']??'')))<10)return 'Republishing a corrected story requires a public correction/update notice of at least 10 characters.';
        return null;
    }

    public static function publicationGateFailures(array $article): array
    {
        $failures=[];
        foreach(['sources','fairness','right_of_reply','fact_check','legal','medical','copy','media_rights','seo','accessibility'] as $type){
            if(($article[$type.'_outcome']??null)==='failed')$failures[]='The '.str_replace('_',' ',$type).' check has failed.';
        }
        if(array_key_exists('source_classification_complete',$article)&&empty($article['source_classification_complete']))$failures[]='Source confidentiality classification is incomplete.';
        if(array_key_exists('confidential_data_protected',$article)&&empty($article['confidential_data_protected']))$failures[]='Confidential source material is not protected by the approved encryption envelope.';
        $risk=(string)($article['risk_level']??'standard');$high=in_array($risk,['high','critical'],true);
        if(!empty($article['fact_check_required'])&&($article['fact_check_outcome']??null)!=='passed')$failures[]='Required fact-check has not passed.';
        if(!empty($article['legal_review_required'])&&($article['legal_outcome']??null)!=='passed')$failures[]='Required legal review has not passed.';
        if(!empty($article['medical_review_required'])){
            if(($article['medical_outcome']??null)!=='passed')$failures[]='Required medical review has not passed.';
            elseif(!empty($article['medical_checked_by'])&&!empty($article['author_id'])&&(int)$article['medical_checked_by']===(int)$article['author_id'])$failures[]='Medical review must be completed by someone other than the author.';
        }
        if($high){foreach(['sources','fairness','fact_check','legal'] as $required)if(($article[$required.'_outcome']??null)!=='passed')$failures[]='High-risk story requires a passed '.str_replace('_',' ',$required).' check.';}
        $reply=(string)($article['right_of_reply_status']??'not_applicable');
        if($high||$reply!=='not_applicable'){
            $outcome=$article['right_of_reply_outcome']??null;
            if($reply==='not_applicable'){
                if(!in_array($outcome,['passed','not_applicable'],true))$failures[]='Right-of-reply handling has not been recorded.';
            }elseif(!in_array($reply,['received','declined','published_response'],true)||$outcome!=='passed')$failures[]='Right-of-reply process is incomplete.';
        }
        if(!empty($article['media_count'])&&(empty($article['media_all_approved'])||($article['media_rights_outcome']??null)!=='passed'))$failures[]='Attached media or its rights check is not approved.';
        if(!empty($article['untracked_upload_media']))$failures[]='A legacy upload is not governed by the media library.';
        return array_values(array_unique($failures));
    }

    public static function nextStatuses(string|array $roles,string $from,array $article=[]): array
    {return array_values(array_filter(self::TRANSITIONS[$from]??[],fn(string $to):bool=>self::canTransition($roles,$from,$to,$article)));}
}
