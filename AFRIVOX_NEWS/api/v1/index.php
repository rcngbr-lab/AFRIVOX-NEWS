<?php
declare(strict_types=1);
require dirname(__DIR__,2).'/app/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=60, stale-while-revalidate=300');
function api_out(array $payload,int $status=200): never {http_response_code($status);echo json_encode($payload,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);exit;}
$resource=(string)($_GET['resource']??'articles');
try{
 if($resource==='categories')api_out(['data'=>categories()]);
 if($resource==='article'){
  $slug=substr((string)($_GET['slug']??''),0,255);$a=query_one(public_article_select().' WHERE '.published_where().' AND a.slug=?',[$slug]);
  if(!$a)api_out(['error'=>['code'=>'not_found','message'=>'Article not found']],404);
  api_out(['data'=>$a]);
 }
 if($resource==='live'){
  $events=query_all("SELECT id,title,slug,summary,status,started_at,closed_at FROM live_events WHERE status IN ('live','closed','archived') ORDER BY COALESCE(started_at,created_at) DESC LIMIT 30");api_out(['data'=>$events]);
 }
 if($resource==='articles'){
  $page=max(1,(int)($_GET['page']??1));$limit=max(1,min(50,(int)($_GET['limit']??20)));$category=trim((string)($_GET['category']??''))?:null;
  $items=published_articles($limit,$category,($page-1)*$limit);
  api_out(['data'=>$items,'meta'=>['page'=>$page,'limit'=>$limit,'count'=>count($items)]]);
 }
 api_out(['error'=>['code'=>'not_found','message'=>'Unknown API resource']],404);
}catch(Throwable $e){error_log('API: '.$e->getMessage());api_out(['error'=>['code'=>'server_error','message'=>'The request could not be completed']],500);}
