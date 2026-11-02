<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require dirname(__DIR__).'/app/bootstrap.php';
$items=query_all("SELECT id,editor_id FROM articles WHERE status='scheduled' AND scheduled_at IS NOT NULL AND scheduled_at<=".sql_now()." ORDER BY scheduled_at LIMIT 100");
$count=0;
foreach($items as $item){
 try{
  $editor=query_one('SELECT role,active FROM users WHERE id=?',[(int)$item['editor_id']]);if(!$editor||!(int)$editor['active'])throw new RuntimeException('Scheduling editor is inactive.');
  transition_article((int)$item['id'],'published',(int)$item['editor_id'],load_user_roles((int)$item['editor_id'],(string)$editor['role']),'Published by scheduled job after workflow revalidation');audit_log('scheduled_publish','article',(int)$item['id']);$count++;
 }catch(Throwable $e){audit_log('scheduled_publish_blocked','article',(int)$item['id']);error_log('Scheduled publication failed for article '.$item['id'].': '.$e->getMessage());}
}
echo "Published $count scheduled article(s).\n";
