<?php
declare(strict_types=1);
require __DIR__.'/app/bootstrap.php';

header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store, max-age=0');
try {
    $database=(int)db()->query('SELECT 1')->fetchColumn()===1;
    if(!$database)throw new RuntimeException('Database probe failed.');
    $environment=(string)cfg('environment','production');
    if($environment==='production'){
        $required=[
            'HTTPS application URL'=>preg_match('~^https://~',(string)cfg('app_url',''))===1&&!str_contains((string)cfg('app_url',''),'example.'),
            'legal operator'=>trim((string)cfg('legal_operator_name',''))!=='',
            'privacy contact'=>filter_var((string)cfg('privacy_contact_email',''),FILTER_VALIDATE_EMAIL)!==false,
            'application encryption key'=>trim((string)(encryption_keys()[active_encryption_key_id()]??''))!=='',
            'audit integrity key'=>trim((string)cfg('audit_key',''))!=='',
        ];
        $missing=array_keys(array_filter($required,static fn(bool $ready):bool=>!$ready));
        if($missing)throw new RuntimeException('Production readiness missing: '.implode(', ',$missing).'.');
    }
    echo json_encode(['status'=>'ok','service'=>'AFRIVOX NEWS','environment'=>$environment],JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR);
} catch(Throwable $error) {
    error_log('Health check failed: '.$error->getMessage());
    http_response_code(503);
    echo json_encode(['status'=>'unavailable','service'=>'AFRIVOX NEWS'],JSON_UNESCAPED_SLASHES);
}
