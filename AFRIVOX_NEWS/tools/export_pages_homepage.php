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
$html=(string)preg_replace_callback('~<form\b[^>]*>.*?</form>~s',static function(array $match):string{
    if(str_contains($match[0],'class="av-search"'))return '<div class="av-search av-preview-control" role="note"><a href="/AFRIVOX-NEWS/preview-info.html#search" aria-label="Search requires the production site">Search: production site ↗</a></div>';
    return '<div class="av-preview-form-note" role="note">Email is not collected on this static preview. <a href="/AFRIVOX-NEWS/preview-info.html#newsletter">Newsletter details →</a></div>';
},$html);

// Serve public assets from the repository subpath and map PHP-only routes to an explicit capability page.
$html=str_replace(['href="/assets/','src="/assets/'],['href="/AFRIVOX-NEWS/assets/','src="/AFRIVOX-NEWS/assets/'],$html);
$html=(string)preg_replace('~<link rel="alternate" type="application/rss\+xml"[^>]*>~','',$html);
$html=(string)preg_replace('~<link rel="alternate" hreflang="[^"]+"[^>]*>~','',$html);
$routeMap=[
 '~href="/article\.php\?[^"]*"~'=>'href="/AFRIVOX-NEWS/preview-info.html#articles"',
 '~href="/category\.php\?[^"]*"~'=>'href="/AFRIVOX-NEWS/preview-info.html#articles"',
 '~href="/search\.php(?:\?[^"]*)?"~'=>'href="/AFRIVOX-NEWS/preview-info.html#search"',
 '~href="/media\.php(?:\?[^"]*)?"~'=>'href="/AFRIVOX-NEWS/preview-info.html#media"',
 '~href="/about\.php(?:\?[^"]*)?"~'=>'href="/AFRIVOX-NEWS/preview-info.html#about"',
 '~href="/contact\.php(?:\?[^"]*)?"~'=>'href="/AFRIVOX-NEWS/preview-info.html#contact"',
 '~href="/newsletter\.php(?:\?[^"]*)?"~'=>'href="/AFRIVOX-NEWS/preview-info.html#newsletter"',
 '~href="/privacy\.php(?:\?[^"]*)?"~'=>'href="/AFRIVOX-NEWS/preview-info.html#privacy"',
 '~href="/policies\.php\?p=corrections[^"]*"~'=>'href="/AFRIVOX-NEWS/preview-info.html#corrections"',
 '~href="/policies\.php\?p=accessibility[^"]*"~'=>'href="/AFRIVOX-NEWS/preview-info.html#accessibility"',
 '~href="/policies\.php(?:\?[^"]*)?"~'=>'href="/AFRIVOX-NEWS/preview-info.html#editorial"',
 '~href="/(?:feed|sitemap|news-sitemap)\.php(?:\?[^"]*)?"~'=>'href="/AFRIVOX-NEWS/preview-info.html#feeds"',
 '~href="/(?:section|author|live)\.php(?:\?[^"]*)?"~'=>'href="/AFRIVOX-NEWS/preview-info.html#articles"',
 '~href="/\?lang=(?:rw|en)"~'=>'href="/AFRIVOX-NEWS/preview-info.html#language"',
];
foreach($routeMap as $pattern=>$replacement)$html=(string)preg_replace($pattern,$replacement,$html);
$html=(string)preg_replace('~href="/(?:[^"#]*\.php[^"#]*)"~','href="/AFRIVOX-NEWS/preview-info.html#production"',$html);
$html=str_replace('href="#"','href="/AFRIVOX-NEWS/preview-info.html"',$html);
$html=str_replace('href="/"','href="/AFRIVOX-NEWS/"',$html);
$html=str_replace('http://127.0.0.1:8765','https://rcngbr-lab.github.io/AFRIVOX-NEWS',$html);
$html=(string)preg_replace('~https://rcngbr-lab\.github\.io/AFRIVOX-NEWS/?\?lang=en~','https://rcngbr-lab.github.io/AFRIVOX-NEWS/',$html);
$previewHead='<meta name="robots" content="noindex,nofollow"><style>.av-preview-control{justify-content:center;padding:0 14px}.av-preview-control a{font-size:10px;font-weight:700;text-align:center}.av-preview-form-note{clear:both;border:1px solid #ffffff55;padding:10px 12px;font-size:9px;line-height:1.45;color:#d8e4ed}.av-preview-form-note a{color:#fff;text-decoration:underline}@media(max-width:680px){.av-preview-control{display:none}}</style>';
$html=str_replace('</head>',$previewHead.'</head>',$html);

$badge='<div style="position:fixed;z-index:999;right:14px;bottom:14px;background:#062747;color:#fff;padding:8px 11px;border-radius:4px;font:700 9px/1.2 Arial,sans-serif;letter-spacing:.08em;box-shadow:0 4px 16px #001a2f38">STATIC DESIGN PREVIEW · DEMO</div>';
$html=str_replace('</body>',$badge.'</body>',$html);

if(!is_dir(dirname($target))&&!mkdir(dirname($target),0775,true)&&!is_dir(dirname($target)))throw new RuntimeException('Unable to create Pages output directory.');
if(file_put_contents($target,$html)===false)throw new RuntimeException('Unable to write Pages homepage.');

echo 'Exported '.strlen($html).' bytes to '.$target.PHP_EOL;
