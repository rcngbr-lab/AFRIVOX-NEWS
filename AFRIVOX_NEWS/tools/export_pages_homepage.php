<?php
declare(strict_types=1);

if(PHP_SAPI!=='cli')exit(1);

$applicationRoot=dirname(__DIR__);
$repositoryRoot=dirname($applicationRoot);
$outputRoot=$repositoryRoot.'/public-pages';
$sourceBase=rtrim((string)(getenv('AFRIVOX_EXPORT_BASE_URL')?:'http://127.0.0.1:8765'),'/');
$pagesBase='https://rcngbr-lab.github.io/AFRIVOX-NEWS';

/** @return array{file:string,request:string}|null */
function export_route(string $url):?array{
    $url=html_entity_decode(trim($url),ENT_QUOTES|ENT_HTML5,'UTF-8');
    if($url===''||str_starts_with($url,'#')||preg_match('~^(?:mailto:|tel:|javascript:)~i',$url))return null;
    $parts=parse_url($url);
    if($parts===false)return null;
    if(isset($parts['host'])&&!in_array(strtolower((string)$parts['host']),['127.0.0.1','localhost'],true))return null;
    $path=(string)($parts['path']??'/');
    parse_str((string)($parts['query']??''),$query);
    $safe=static fn(string $value):string=>preg_replace('~[^a-z0-9-]+~','-',strtolower($value))?:'preview';
    switch($path){
        case '/':
        case '/index.php': return ['file'=>'index.html','request'=>'/?lang=en'];
        case '/about.php': return ['file'=>'about.html','request'=>'/about.php?lang=en'];
        case '/contact.php': return ['file'=>'contact.html','request'=>'/contact.php?lang=en'];
        case '/media.php': return ['file'=>'media.html','request'=>'/media.php?lang=en'];
        case '/newsletter.php': return ['file'=>'newsletter.html','request'=>'/newsletter.php?lang=en'];
        case '/privacy.php': return ['file'=>'privacy.html','request'=>'/privacy.php?lang=en'];
        case '/live.php': return ['file'=>'live.html','request'=>'/live.php?lang=en'];
        case '/search.php': return ['file'=>'search.html','request'=>'/search.php?q=Africa&lang=en'];
        case '/article.php':
            if(empty($query['slug']))return null;
            $slug=$safe((string)$query['slug']);
            return ['file'=>'articles/'.$slug.'.html','request'=>'/article.php?slug='.rawurlencode((string)$query['slug']).'&lang=en'];
        case '/category.php':
            if(empty($query['slug']))return null;
            $slug=$safe((string)$query['slug']);
            return ['file'=>'categories/'.$slug.'.html','request'=>'/category.php?slug='.rawurlencode((string)$query['slug']).'&lang=en'];
        case '/section.php':
            $type=$safe((string)($query['type']??'news'));
            return ['file'=>'sections/'.$type.'.html','request'=>'/section.php?type='.rawurlencode((string)($query['type']??'news')).'&lang=en'];
        case '/author.php':
            $author=$safe((string)($query['slug']??$query['id']??'author'));
            $request='/author.php?'.http_build_query(array_filter(['slug'=>$query['slug']??null,'id'=>$query['id']??null,'lang'=>'en'],static fn($v)=>$v!==null));
            return ['file'=>'authors/'.$author.'.html','request'=>$request];
        case '/policies.php':
            $policy=$safe((string)($query['p']??'editorial'));
            return ['file'=>'policies/'.$policy.'.html','request'=>'/policies.php?p='.rawurlencode((string)($query['p']??'editorial')).'&lang=en'];
        case '/feed.php': return ['file'=>'feed.xml','request'=>'/feed.php?lang=en'];
        case '/sitemap.php': return ['file'=>'sitemap.xml','request'=>'/sitemap.php'];
        case '/news-sitemap.php': return ['file'=>'news-sitemap.xml','request'=>'/news-sitemap.php'];
        default: return null;
    }
}

function fetch_export_source(string $url):string{
    $context=stream_context_create(['http'=>[
        'timeout'=>20,
        'ignore_errors'=>true,
        'header'=>"User-Agent: AFRIVOX-GitHub-Pages-Exporter/1.0\r\n",
    ]]);
    $content=@file_get_contents($url,false,$context);
    $statusLine=$http_response_header[0]??'';
    if($content===false||!preg_match('~\s200\s~',$statusLine))throw new RuntimeException('Unable to export '.$url.' ('.$statusLine.').');
    return $content;
}

function ensure_parent_directory(string $path):void{
    $directory=dirname($path);
    if(!is_dir($directory)&&!mkdir($directory,0775,true)&&!is_dir($directory))throw new RuntimeException('Unable to create '.$directory.'.');
}

$queue=[export_route('/')];
$rawPages=[];
while($queue){
    $route=array_shift($queue);
    if($route===null||isset($rawPages[$route['file']]))continue;
    $body=fetch_export_source($sourceBase.$route['request']);
    $rawPages[$route['file']]=$body;
    if(!str_ends_with($route['file'],'.html'))continue;
    preg_match_all('~\bhref=["\']([^"\']+)["\']~i',$body,$links);
    foreach($links[1]??[] as $link){
        $candidate=export_route((string)$link);
        if($candidate!==null&&!isset($rawPages[$candidate['file']]))$queue[]=$candidate;
    }
}

$previewStyle='<meta name="robots" content="noindex,nofollow,noarchive"><style>.av-preview-form-note{border:1px solid #d5dde5;background:#f5f8fb;color:#17324d;padding:14px 16px;font:600 12px/1.55 Arial,sans-serif}.av-preview-form-note a{color:#b4232a;text-decoration:underline}</style>';
$previewBadge='<div style="position:fixed;z-index:999;right:14px;bottom:14px;background:#062747;color:#fff;padding:8px 11px;border-radius:4px;font:700 9px/1.2 Arial,sans-serif;letter-spacing:.08em;box-shadow:0 4px 16px #001a2f38">CLIENT DESIGN PREVIEW · READ ONLY</div>';

foreach($rawPages as $file=>$body){
    $target=$outputRoot.'/'.$file;
    ensure_parent_directory($target);
    if(!str_ends_with($file,'.html')){
        if($file==='sitemap.xml'){
            $locations=[];
            foreach(array_keys($rawPages) as $exportedFile){
                if(!str_ends_with($exportedFile,'.html')||$exportedFile==='404.html')continue;
                $locations[]='<url><loc>'.htmlspecialchars($pagesBase.'/'.$exportedFile,ENT_XML1|ENT_QUOTES,'UTF-8').'</loc></url>';
            }
            $body='<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.implode('',$locations).'</urlset>';
        }else{
            $body=(string)preg_replace_callback('~https?://(?:127\.0\.0\.1|localhost)(?::\d+)?[^<\s]*~i',static function(array $match)use($pagesBase):string{
                $url=html_entity_decode($match[0],ENT_QUOTES|ENT_XML1,'UTF-8');
                $route=export_route($url);
                $target=$route!==null?$pagesBase.'/'.$route['file']:$pagesBase.'/preview-info.html#production';
                return htmlspecialchars($target,ENT_XML1|ENT_QUOTES,'UTF-8');
            },$body);
        }
        if(file_put_contents($target,$body)===false)throw new RuntimeException('Unable to write '.$target.'.');
        continue;
    }
    $html=(string)preg_replace_callback('~<form\b[^>]*>.*?</form>~is',static function():string{
        return '<div class="av-preview-form-note" role="note"><strong>Read-only client preview.</strong> This form becomes operational on the secure production site. <a href="/AFRIVOX-NEWS/preview-info.html#production">Deployment details →</a></div>';
    },$body);
    $html=(string)preg_replace_callback('~\b(href|src)=("|\')([^"\']+)(\2)~i',static function(array $match):string{
        $attribute=$match[1];$quote=$match[2];$value=html_entity_decode($match[3],ENT_QUOTES|ENT_HTML5,'UTF-8');
        if(str_starts_with($value,'/assets/'))$value='/AFRIVOX-NEWS'.$value;
        elseif(str_starts_with($value,'http://127.0.0.1:8765/assets/'))$value='/AFRIVOX-NEWS'.substr($value,strlen('http://127.0.0.1:8765'));
        else{
            $route=export_route($value);
            if($route!==null)$value='/AFRIVOX-NEWS/'.$route['file'];
            elseif(str_starts_with($value,'/'))$value='/AFRIVOX-NEWS/preview-info.html#production';
        }
        return $attribute.'='.$quote.htmlspecialchars($value,ENT_QUOTES|ENT_HTML5,'UTF-8').$quote;
    },$html);
    $html=(string)preg_replace('~<link rel="alternate" hreflang="[^"]+"[^>]*>~i','',$html);
    $html=str_replace($sourceBase,$pagesBase,$html);
    if(str_contains($html,'name="robots"'))$html=(string)preg_replace('~<meta\s+name="robots"[^>]*>~i',$previewStyle,$html,1);
    else $html=str_replace('</head>',$previewStyle.'</head>',$html);
    $html=str_replace('</body>',$previewBadge.'</body>',$html);
    if(file_put_contents($target,$html)===false)throw new RuntimeException('Unable to write '.$target.'.');
}

// Keep the public newsroom mock-up navigable without pretending that privileged
// actions execute on GitHub Pages.
$newsroomPath=$outputRoot.'/newsroom-preview.html';
if(is_file($newsroomPath)){
    $newsroom=(string)file_get_contents($newsroomPath);
    $newsroom=str_replace(
        "<style>500;600;700;800&family=Manrope:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;1,500;1,600;1,700&display=swap');",
        "<style>@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;1,500;1,600;1,700&display=swap');",
        $newsroom
    );
    if(!str_contains($newsroom,'name="robots"'))$newsroom=str_replace('</title>','</title><meta name="robots" content="noindex,nofollow,noarchive">',$newsroom);
    $newsroom=str_replace('href="admin-preview.html"','href="/AFRIVOX-NEWS/newsroom-preview.html"',$newsroom);
    $newsroom=str_replace('href="preview.html"','href="/AFRIVOX-NEWS/"',$newsroom);
    $newsroom=str_replace('<a href="#demo" class="active">▦ &nbsp; Overview</a>','<a href="/AFRIVOX-NEWS/newsroom-preview.html" class="active" aria-current="page">▦ &nbsp; Overview</a>',$newsroom);
    $newsroom=str_replace('<a href="#demo">▤ &nbsp; Articles</a>','<a href="/AFRIVOX-NEWS/search.html">▤ &nbsp; Articles</a>',$newsroom);
    foreach(['✎ &nbsp; New Article','▧ &nbsp; Reader Inbox','✉ &nbsp; Subscribers','◷ &nbsp; Audit Log','♙ &nbsp; Staff & Roles'] as $label){
        $newsroom=str_replace('<a href="#demo">'.$label.'</a>','<a href="/AFRIVOX-NEWS/preview-info.html#production">'.$label.'</a>',$newsroom);
    }
    $newsroom=str_replace('<a class="admin-public logout-link" href="#demo">','<a class="admin-public logout-link" href="/AFRIVOX-NEWS/preview-info.html#production">',$newsroom);
    $newsroom=str_replace('<a href="#demo" class="admin-action">','<a href="/AFRIVOX-NEWS/preview-info.html#production" class="admin-action">',$newsroom);
    if(file_put_contents($newsroomPath,$newsroom)===false)throw new RuntimeException('Unable to update '.$newsroomPath.'.');
}

ksort($rawPages);
foreach(array_keys($rawPages) as $file)echo 'Exported '.$file.PHP_EOL;
echo 'Exported '.count($rawPages).' public preview resources to '.$outputRoot.PHP_EOL;
