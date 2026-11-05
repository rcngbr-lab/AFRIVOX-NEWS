<?php
require dirname(__DIR__).'/app/bootstrap.php';
require_login();
if(!can('advertising.manage')&&!can('advertising.approve')){http_response_code(403);exit;}
$creative=query_one('SELECT private_derivative_path FROM ad_creatives WHERE id=?',[(int)($_GET['id']??0)]);$file=$creative?AdService::resolvePrivateCreativePath((string)$creative['private_derivative_path']):null;
if(!$file){http_response_code(404);exit;}
header('Content-Type: image/jpeg');header('Content-Length: '.filesize($file));header('Cache-Control: no-store, private');header('Content-Disposition: inline; filename="creative-preview.jpg"');readfile($file);
