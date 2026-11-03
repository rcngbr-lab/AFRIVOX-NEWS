<?php
declare(strict_types=1);

if(PHP_SAPI!=='cli')exit(1);

$root=dirname(__DIR__);
$repositoryRoot=dirname($root);
$target=$repositoryRoot.'/public-pages/index.html';

$_GET['lang']='en';
$_SERVER['REQUEST_URI']='/?lang=en';
$_SERVER['SCRIPT_NAME']='/index.php';
$_SERVER['HTTP_HOST']='127.0.0.1:8765';
$_SERVER['SERVER_PORT']='8765';

ob_start();
require $root.'/index.php';
$html=(string)ob_get_clean();

// GitHub Pages is a visual preview only: remove live form behavior and local CSRF state.
$html=(string)preg_replace('~<input type="hidden" name="csrf" value="[^"]*">~','',$html);
$html=(string)preg_replace('~action="[^"]*"~','action="#" onsubmit="return false"',$html);

// Serve public assets from the repository subpath and neutralize PHP-only routes.
$html=str_replace(['href="/assets/','src="/assets/'],['href="/AFRIVOX-NEWS/assets/','src="/AFRIVOX-NEWS/assets/'],$html);
$html=(string)preg_replace('~href="/(?:[^"#]*\.php[^"#]*)"~','href="#"',$html);
$html=(string)preg_replace('~href="/\?lang=(?:rw|en)"~','href="#"',$html);
$html=str_replace('href="/"','href="/AFRIVOX-NEWS/"',$html);
$html=str_replace('http://127.0.0.1:8765','https://rcngbr-lab.github.io/AFRIVOX-NEWS',$html);

$badge='<div style="position:fixed;z-index:999;right:14px;bottom:14px;background:#062747;color:#fff;padding:8px 11px;border-radius:4px;font:700 9px/1.2 Arial,sans-serif;letter-spacing:.08em;box-shadow:0 4px 16px #001a2f38">STATIC DESIGN PREVIEW · DEMO</div>';
$html=str_replace('</body>',$badge.'</body>',$html);

if(!is_dir(dirname($target))&&!mkdir(dirname($target),0775,true)&&!is_dir(dirname($target)))throw new RuntimeException('Unable to create Pages output directory.');
if(file_put_contents($target,$html)===false)throw new RuntimeException('Unable to write Pages homepage.');

echo 'Exported '.strlen($html).' bytes to '.$target.PHP_EOL;
