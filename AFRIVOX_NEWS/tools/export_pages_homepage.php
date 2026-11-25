<?php
declare(strict_types=1);

if(PHP_SAPI!=='cli')exit(1);

$applicationRoot=dirname(__DIR__);
$repositoryRoot=dirname($applicationRoot);
$outputRoot=$repositoryRoot.'/public-pages';
$sourceBase=rtrim((string)(getenv('AFRIVOX_EXPORT_BASE_URL')?:'http://127.0.0.1:8765'),'/');
$pagesBase='https://rcngbr-lab.github.io/AFRIVOX-NEWS';

/** @return array{file:string,request:string}|null */
function export_route(string $url):?array
{
    $url=html_entity_decode(trim($url),ENT_QUOTES|ENT_HTML5,'UTF-8');
    if($url===''||str_starts_with($url,'#')||preg_match('~^(?:mailto:|tel:|javascript:)~i',$url))return null;
    $parts=parse_url($url);if($parts===false)return null;
    if(isset($parts['host'])&&!in_array(strtolower((string)$parts['host']),['127.0.0.1','localhost'],true))return null;
    $path=(string)($parts['path']??'/');parse_str((string)($parts['query']??''),$query);
    $safe=static fn(string $value):string=>preg_replace('~[^a-z0-9-]+~','-',strtolower($value))?:'page';
    return match($path){
        '/', '/index.php'=>['file'=>'index.html','request'=>'/?lang=en'],
        '/about.php'=>['file'=>'about.html','request'=>'/about.php?lang=en'],
        '/contact.php'=>['file'=>'contact.html','request'=>'/contact.php?lang=en'],
        '/newsletter.php'=>['file'=>'newsletter.html','request'=>'/newsletter.php?lang=en'],
        '/search.php'=>['file'=>'search.html','request'=>'/search.php?lang=en'],
        '/health-news.php'=>['file'=>'health-news.html','request'=>'/health-news.php?lang=en'],
        '/health-topics.php'=>['file'=>'health-topics.html','request'=>'/health-topics.php?lang=en'],
        '/health-tips.php'=>['file'=>'health-tips.html','request'=>'/health-tips.php?lang=en'],
        '/health-tools.php'=>['file'=>'health-tools.html','request'=>'/health-tools.php?lang=en'],
        '/bmi-calculator.php'=>['file'=>'bmi-calculator.html','request'=>'/bmi-calculator.php?lang=en'],
        '/pregnancy-calculator.php'=>['file'=>'pregnancy-calculator.html','request'=>'/pregnancy-calculator.php?lang=en'],
        '/child-growth-tracker.php'=>['file'=>'child-growth-tracker.html','request'=>'/child-growth-tracker.php?lang=en'],
        '/wellness.php'=>['file'=>'wellness.html','request'=>'/wellness.php?lang=en'],
        '/health-talks.php'=>['file'=>'health-talks.html','request'=>'/health-talks.php?lang=en'],
        '/partners.php'=>['file'=>'partners.html','request'=>'/partners.php?lang=en'],
        '/privacy.php'=>['file'=>'privacy.html','request'=>'/privacy.php?lang=en'],
        '/health-topic.php'=>empty($query['slug'])?null:['file'=>'health-topics/'.$safe((string)$query['slug']).'.html','request'=>'/health-topic.php?slug='.rawurlencode((string)$query['slug']).'&lang=en'],
        '/health-tip.php'=>empty($query['slug'])?null:['file'=>'health-tips/'.$safe((string)$query['slug']).'.html','request'=>'/health-tip.php?slug='.rawurlencode((string)$query['slug']).'&lang=en'],
        '/policies.php'=>['file'=>'policies/'.$safe((string)($query['p']??'editorial')).'.html','request'=>'/policies.php?p='.rawurlencode((string)($query['p']??'editorial')).'&lang=en'],
        '/feed.php'=>['file'=>'feed.xml','request'=>'/feed.php?lang=en'],
        default=>null,
    };
}

function fetch_export_source(string $url):string
{
    $context=stream_context_create(['http'=>['timeout'=>20,'ignore_errors'=>true,'header'=>"User-Agent: UBUZIMACARE-Pages-Exporter/2.0\r\n"]]);
    $content=@file_get_contents($url,false,$context);$statusLine=$http_response_header[0]??'';
    if($content===false||!preg_match('~\s200\s~',$statusLine))throw new RuntimeException('Unable to export '.$url.' ('.$statusLine.').');
    return $content;
}

function ensure_parent_directory(string $path):void
{
    $directory=dirname($path);
    if(!is_dir($directory)&&!mkdir($directory,0775,true)&&!is_dir($directory))throw new RuntimeException('Unable to create '.$directory.'.');
}

function public_shell(string $title,string $heading,string $body,string $robots='noindex,nofollow'):string
{
    return '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="'.$robots.'"><title>'.htmlspecialchars($title,ENT_QUOTES,'UTF-8').'</title><link rel="stylesheet" href="/AFRIVOX-NEWS/assets/style.css"><link rel="icon" href="/AFRIVOX-NEWS/assets/favicon.svg"></head><body class="ubuzima-site"><header class="uz-brand-header"><div class="container"><a class="uz-logo" href="/AFRIVOX-NEWS/"><img src="/AFRIVOX-NEWS/assets/ubuzimacare-logo.svg" alt="UBUZIMACARE — Knowledge for Better Health"></a></div></header><main id="main-content" class="container uz-page"><header class="uz-page-header"><span>UBUZIMACARE</span><h1>'.htmlspecialchars($heading,ENT_QUOTES,'UTF-8').'</h1></header><article class="uz-education-card">'.$body.'</article><p><a href="/AFRIVOX-NEWS/">← Return to UBUZIMACARE</a></p></main></body></html>';
}

$seed=['/','/about.php','/contact.php','/newsletter.php','/search.php','/health-news.php','/health-topics.php','/health-tips.php','/health-tools.php','/bmi-calculator.php','/pregnancy-calculator.php','/child-growth-tracker.php','/wellness.php','/health-talks.php','/partners.php','/privacy.php','/policies.php?p=editorial','/policies.php?p=corrections','/policies.php?p=accessibility','/policies.php?p=complaints','/policies.php?p=ai','/feed.php'];
$queue=[];foreach($seed as $url)$queue[]=export_route($url);
$rawPages=[];
while($queue){
    $route=array_shift($queue);if($route===null||isset($rawPages[$route['file']]))continue;
    $body=fetch_export_source($sourceBase.$route['request']);$rawPages[$route['file']]=$body;
    if(!str_ends_with($route['file'],'.html'))continue;
    preg_match_all('~\bhref=["\']([^"\']+)["\']~i',$body,$links);
    foreach($links[1]??[] as $link){$candidate=export_route((string)$link);if($candidate!==null&&!isset($rawPages[$candidate['file']]))$queue[]=$candidate;}
}

$formNotice='<div class="av-preview-form-note" role="note"><strong>Secure service required.</strong> This form is available on the PHP/MySQL production deployment; the public Pages edition does not collect personal information. <a href="/AFRIVOX-NEWS/preview-info.html#production">Deployment status →</a></div>';
$siteBadge='<div style="position:fixed;z-index:999;right:14px;bottom:14px;background:#073b75;color:#fff;padding:8px 11px;border-radius:4px;font:700 9px/1.2 Arial,sans-serif;letter-spacing:.08em;box-shadow:0 4px 16px #001a2f38">UBUZIMACARE PUBLIC SITE</div>';
$previewCss='<style>.av-preview-form-note{border:1px solid #b9d9c7;background:#f2fbf6;color:#073b75;padding:14px 16px;font:600 12px/1.55 Arial,sans-serif}.av-preview-form-note a{color:#08743b;text-decoration:underline}</style>';

foreach($rawPages as $file=>$body){
    $target=$outputRoot.'/'.$file;ensure_parent_directory($target);
    if(!str_ends_with($file,'.html')){
        $body=(string)preg_replace_callback('~https?://(?:127\.0\.0\.1|localhost)(?::\d+)?[^<\s]*~i',static function(array $match)use($pagesBase):string{$route=export_route(html_entity_decode($match[0],ENT_QUOTES|ENT_XML1,'UTF-8'));return htmlspecialchars($route?$pagesBase.'/'.$route['file']:$pagesBase.'/preview-info.html#production',ENT_XML1|ENT_QUOTES,'UTF-8');},$body);
        if(file_put_contents($target,$body)===false)throw new RuntimeException('Unable to write '.$target.'.');continue;
    }
    $html=(string)preg_replace_callback('~<form\b[^>]*>.*?</form>~is',static function(array $match)use($formNotice):string{
        if(str_contains($match[0],'uz-tool-form')||str_contains($match[0],'uz-client-search')||str_contains($match[0],'uz-top-search'))return $match[0];
        return $formNotice;
    },$body);
    $html=(string)preg_replace_callback('~\b(href|src|action|data-source)=("|\')([^"\']+)(\2)~i',static function(array $match):string{
        $attribute=$match[1];$quote=$match[2];$value=html_entity_decode($match[3],ENT_QUOTES|ENT_HTML5,'UTF-8');
        if(str_starts_with($value,'/assets/'))$value='/AFRIVOX-NEWS'.$value;
        elseif(str_starts_with($value,'http://127.0.0.1:8765/assets/'))$value='/AFRIVOX-NEWS'.substr($value,strlen('http://127.0.0.1:8765'));
        else{$route=export_route($value);if($route!==null)$value='/AFRIVOX-NEWS/'.$route['file'];elseif(str_starts_with($value,'/'))$value='/AFRIVOX-NEWS/preview-info.html#production';}
        return $attribute.'='.$quote.htmlspecialchars($value,ENT_QUOTES|ENT_HTML5,'UTF-8').$quote;
    },$html);
    $html=(string)preg_replace('~<link rel="alternate" hreflang="[^"]+"[^>]*>~i','',$html);
    $html=(string)preg_replace('~<div class="av-language"[^>]*>.*?</div>~is','',$html);
    $html=(string)preg_replace('~<meta\s+name="robots"[^>]*>~i','',$html);
    $html=str_replace('</head>','<meta name="robots" content="index,follow,max-image-preview:large">'.$previewCss.'</head>',$html);
    $html=str_replace($sourceBase,$pagesBase,$html);
    $canonical=$pagesBase.'/'.$file;
    $html=(string)preg_replace('~<link rel="canonical" href="[^"]*">~i','<link rel="canonical" href="'.htmlspecialchars($canonical,ENT_QUOTES,'UTF-8').'">',$html,1);
    $html=(string)preg_replace('~<meta property="og:url" content="[^"]*">~i','<meta property="og:url" content="'.htmlspecialchars($canonical,ENT_QUOTES,'UTF-8').'">',$html,1);
    $html=str_replace('</body>',$siteBadge.'</body>',$html);
    $html=rtrim($html).PHP_EOL;
    if(file_put_contents($target,$html)===false)throw new RuntimeException('Unable to write '.$target.'.');
}

$locations=[];foreach(array_keys($rawPages) as $file)if(str_ends_with($file,'.html'))$locations[]='<url><loc>'.htmlspecialchars($pagesBase.'/'.$file,ENT_XML1|ENT_QUOTES,'UTF-8').'</loc></url>';
$sitemap='<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.implode('',$locations).'</urlset>';
file_put_contents($outputRoot.'/sitemap.xml',$sitemap);
file_put_contents($outputRoot.'/robots.txt',"User-agent: *\nAllow: /AFRIVOX-NEWS/\nDisallow: /AFRIVOX-NEWS/newsroom-preview.html\nDisallow: /AFRIVOX-NEWS/preview-info.html\nSitemap: {$pagesBase}/sitemap.xml\n");
file_put_contents($outputRoot.'/404.html',public_shell('Page not found | UBUZIMACARE','Page not found','<p>The requested health resource is unavailable or has moved.</p><p>Use the health search or return to the homepage.</p>','noindex,follow'));
file_put_contents($outputRoot.'/preview-info.html',public_shell('Service status | UBUZIMACARE','Public and production services','<p>The GitHub Pages edition provides the approved public health design, educational pages, browser-based calculators and client-side search. It does not collect newsletter or contact data.</p><p id="production"><strong>Secure production:</strong> staff login, CMS publishing, database search, newsletter delivery and private workflows require the separately deployed PHP/MySQL application. Production is not represented as live until hosting, secrets, mail and health checks are configured.</p>'));
file_put_contents($outputRoot.'/newsroom-preview.html',public_shell('Newsroom access | UBUZIMACARE','Secure newsroom access','<p>The UBUZIMACARE newsroom is a private operational service, not a public demo dashboard. Authorized staff sign in only on the secure PHP/MySQL production host with role-based permissions and MFA where required.</p><p>GitHub Pages cannot authenticate staff or access the newsroom database.</p>'));

ksort($rawPages);foreach(array_keys($rawPages) as $file)echo 'Exported '.$file.PHP_EOL;
echo 'Generated sitemap.xml, robots.txt, 404 and production-status pages.'.PHP_EOL;
echo 'Exported '.count($rawPages).' public resources to '.$outputRoot.PHP_EOL;
