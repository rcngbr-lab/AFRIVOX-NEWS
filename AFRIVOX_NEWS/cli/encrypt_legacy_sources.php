<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require dirname(__DIR__).'/app/bootstrap.php';
$legacy=false;if(db_is_sqlite()){foreach(query_all('PRAGMA table_info(articles)') as $c)if($c['name']==='source_note')$legacy=true;}else{$legacy=(bool)query_one("SELECT 1 ok FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='articles' AND column_name='source_note'");}
$apply=in_array('--apply',$argv,true);$select=$legacy?"SELECT id,internal_source_notes,source_note FROM articles WHERE (internal_source_notes IS NOT NULL AND internal_source_notes<>'') OR (source_note IS NOT NULL AND source_note<>'')":"SELECT id,internal_source_notes,NULL source_note FROM articles WHERE internal_source_notes IS NOT NULL AND internal_source_notes<>''";$rows=query_all($select);$changed=0;
foreach($rows as $row){$value=(string)($row['internal_source_notes']?:$row['source_note']);if(str_starts_with($value,'v1.')||str_starts_with($value,'v2.'))continue;$changed++;if($apply){$cipher=Security::encrypt($value,encryption_keys(),active_encryption_key_id());$sql=$legacy?'UPDATE articles SET internal_source_notes=?,source_note=NULL,source_confidentiality_level=\'restricted\' WHERE id=?':'UPDATE articles SET internal_source_notes=?,source_confidentiality_level=\'restricted\' WHERE id=?';db()->prepare($sql)->execute([$cipher,(int)$row['id']]);}}
echo ($apply?'Encrypted ':'Would encrypt ').$changed." legacy confidential source record(s).\n";
if(!$apply)echo "Run again with --apply after verifying the app key and database backup.\n";
