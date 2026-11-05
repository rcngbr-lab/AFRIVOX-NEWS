<?php
declare(strict_types=1);
require __DIR__.'/app/bootstrap.php';

if(($_SERVER['REQUEST_METHOD']??'GET')!=='POST'){http_response_code(405);header('Allow: POST');exit;}
if((int)($_SERVER['CONTENT_LENGTH']??0)>2048){http_response_code(413);exit;}

$placementId=filter_var($_POST['placement_id']??null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
$event=(string)($_POST['event_type']??'');$token=(string)($_POST['token']??'');
if(!$placementId||!in_array($event,['served','viewable','click'],true)||!preg_match('/^[A-Za-z0-9._:-]{16,160}$/',$token)){http_response_code(400);exit;}

$window=$_SESSION['ad_event_window']??['started'=>time(),'count'=>0];
if(time()-(int)$window['started']>=60)$window=['started'=>time(),'count'=>0];
if((int)$window['count']>=60){http_response_code(429);exit;}
$window['count']++;$_SESSION['ad_event_window']=$window;

$now=date('Y-m-d H:i:s');
$placement=query_one("SELECT p.id FROM ad_placements p JOIN ad_creatives cr ON cr.id=p.creative_id JOIN ad_campaigns c ON c.id=cr.campaign_id WHERE p.id=? AND p.status='active' AND cr.status='approved' AND c.status='active' AND p.starts_at<=? AND p.ends_at>? AND c.starts_at<=? AND c.ends_at>?",[(int)$placementId,$now,$now,$now,$now]);
if(!$placement){http_response_code(204);exit;}

$eventToken=hash_hmac('sha256',$event.'|'.$placementId.'|'.$token,(string)cfg('app_key','afrivox-local-event-key'));
try{db()->prepare('INSERT INTO ad_events(placement_id,event_type,event_token) VALUES (?,?,?)')->execute([(int)$placementId,$event,$eventToken]);}catch(PDOException $e){if(!in_array((string)$e->getCode(),['23000','19'],true))error_log('Ad event insert failed: '.$e->getMessage());}
http_response_code(204);
