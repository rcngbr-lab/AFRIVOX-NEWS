<?php
require dirname(__DIR__).'/app/bootstrap.php';
require_login();
if(!can('advertising.manage')&&!can('advertising.approve')){http_response_code(403);exit;}
$creative=query_one('SELECT private_derivative_path,mime_type FROM ad_creatives WHERE id=?',[(int)($_GET['id']??0)]);$file=$creative?AdService::resolvePrivateCreativePath((string)$creative['private_derivative_path']):null;
if(!$file){http_response_code(404);exit;}
$mime=in_array((string)$creative['mime_type'],['image/jpeg','video/mp4','video/webm'],true)?(string)$creative['mime_type']:'application/octet-stream';$extension=$mime==='video/mp4'?'mp4':($mime==='video/webm'?'webm':'jpg');
header('Content-Type: '.$mime);header('Content-Length: '.filesize($file));header('Cache-Control: no-store, private');header('X-Content-Type-Options: nosniff');header('Content-Disposition: inline; filename="creative-preview.'.$extension.'"');readfile($file);
