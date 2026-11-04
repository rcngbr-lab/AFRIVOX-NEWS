<?php
declare(strict_types=1);
require __DIR__.'/app/bootstrap.php';

header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store, max-age=0');
try {
    $database=(int)db()->query('SELECT 1')->fetchColumn()===1;
    if(!$database)throw new RuntimeException('Database probe failed.');
    echo json_encode(['status'=>'ok','service'=>'afrivox-news'],JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR);
} catch(Throwable $error) {
    error_log('Health check failed: '.$error->getMessage());
    http_response_code(503);
    echo json_encode(['status'=>'unavailable','service'=>'afrivox-news'],JSON_UNESCAPED_SLASHES);
}
