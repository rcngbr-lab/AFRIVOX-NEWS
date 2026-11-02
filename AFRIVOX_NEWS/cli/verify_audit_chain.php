<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit(1);
require dirname(__DIR__).'/app/bootstrap.php';
try{$key=Security::encryptionKey((string)cfg('audit_key',''));}catch(Throwable $e){fwrite(STDERR,$e->getMessage().PHP_EOL);exit(2);}
$prev=str_repeat('0',64);$count=0;
foreach(query_all('SELECT id,user_id,action,entity_type,entity_id,detail,request_id,prev_hash,event_hash FROM audit_logs WHERE event_hash IS NOT NULL ORDER BY id') as $row){$count++;$payload=json_encode([$prev,$row['user_id']===null?null:(string)$row['user_id'],(string)$row['action'],(string)$row['entity_type'],$row['entity_id']===null?null:(string)$row['entity_id'],(string)($row['detail']??''),(string)($row['request_id']??'')],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);$expected=hash_hmac('sha256',(string)$payload,$key);if(!hash_equals($prev,(string)$row['prev_hash'])||!hash_equals($expected,(string)$row['event_hash'])){fwrite(STDERR,'Audit chain failed at event '.$row['id'].PHP_EOL);exit(3);}$prev=(string)$row['event_hash'];}
echo "Audit chain verified: $count signed events.\n";
