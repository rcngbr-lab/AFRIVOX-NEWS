<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit(1);
require dirname(__DIR__).'/app/bootstrap.php';
$checks=[];$blocked=false;
try{Security::encryptionKey((string)(encryption_keys()[active_encryption_key_id()]??''));$checks['active_encryption_key']='ok';}catch(Throwable $e){$checks['active_encryption_key']=$e->getMessage();$blocked=true;}
try{Security::encryptionKey((string)cfg('audit_key',''));$checks['audit_key']='ok';}catch(Throwable $e){$checks['audit_key']=$e->getMessage();$blocked=true;}
try{$allowed=['new','in_progress','resolved','archived','in_review','deleted'];$placeholders=implode(',',array_fill(0,count($allowed),'?'));$n=(int)(query_one("SELECT COUNT(*) AS n FROM contact_messages WHERE status NOT IN ($placeholders)",$allowed)['n']??0);$checks['unknown_contact_statuses']=$n;if($n)$blocked=true;}catch(Throwable $e){$checks['unknown_contact_statuses']='check unavailable: '.$e->getMessage();$blocked=true;}
try{$n=(int)(query_one("SELECT COUNT(*) AS n FROM articles WHERE internal_source_notes IS NOT NULL AND internal_source_notes<>'' AND internal_source_notes NOT LIKE 'v1.%' AND internal_source_notes NOT LIKE 'v2.%'")['n']??0);$checks['plaintext_internal_source_notes']=$n;if($n)$blocked=true;}catch(Throwable $e){$checks['plaintext_internal_source_notes']='check unavailable: '.$e->getMessage();$blocked=true;}
try{$legacy=false;if(db_is_sqlite()){foreach(query_all('PRAGMA table_info(articles)') as $column)if($column['name']==='source_note')$legacy=true;}else{$legacy=(bool)query_one("SELECT 1 ok FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='articles' AND column_name='source_note'");}$n=$legacy?(int)(query_one("SELECT COUNT(*) AS n FROM articles WHERE source_note IS NOT NULL AND source_note<>''")['n']??0):0;$checks['legacy_source_notes_requiring_encryption']=$n;if($n)$blocked=true;}catch(Throwable $e){$checks['legacy_source_notes_requiring_encryption']='check unavailable: '.$e->getMessage();$blocked=true;}
$checks['result']=$blocked?'BLOCKED':'READY';echo json_encode($checks,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES).PHP_EOL;exit($blocked?2:0);
