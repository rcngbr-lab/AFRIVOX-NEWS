<?php
require __DIR__.'/app/bootstrap.php';
// One-time installer. Remove this file immediately after installation.
$exists=false;
try {
 $s=db()->query("SHOW TABLES LIKE 'users'");
 $exists=$s->fetch()!==false && (int)db()->query('SELECT COUNT(*) FROM users')->fetchColumn()>0;
} catch (PDOException $ignored) { }
if($exists || is_file(__DIR__.'/app/installed.lock')){http_response_code(403);exit('Already initialized. Delete install.php from the public web root.');}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST') {
 csrf_verify();
 $name=trim((string)($_POST['name']??''));$email=strtolower(trim((string)($_POST['email']??'')));$pwd=(string)($_POST['password']??'');
 if(mb_strlen($name)<2||!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($pwd)<14) $error='Administrator name, valid email, and password with at least 14 characters are required.';
 else {
  try {
   $sql=file_get_contents(__DIR__.'/schema.sql');
   foreach(preg_split('/;\s*\n/',(string)$sql) as $stmt) if(trim($stmt))db()->exec($stmt);
   $roles=['system_administrator'=>'System Administrator','publisher'=>'Publisher / Executive Management','editor_in_chief'=>'Editor-in-Chief','managing_editor'=>'Managing Editor','section_editor'=>'Section Editor','reporter'=>'Reporter / Journalist','investigative_reporter'=>'Investigative Reporter','fact_checker'=>'Fact-Checker','copy_editor'=>'Copy Editor','photographer'=>'Photographer','video_producer'=>'Video Producer','multimedia_editor'=>'Multimedia Editor','legal_reviewer'=>'Legal Reviewer','seo_audience_editor'=>'SEO and Audience Editor','social_media_manager'=>'Social Media Manager','advertising_manager'=>'Advertising Manager','finance_officer'=>'Finance Officer','subscriber_support'=>'Subscriber Support Officer','external_contributor'=>'External Contributor'];
   foreach($roles as $key=>$label)db()->prepare('INSERT INTO roles(role_key,name,system_role) VALUES (?,?,1)')->execute([$key,$label]);
    db()->prepare("INSERT INTO users(full_name,email,password_hash,role,mfa_enabled,mfa_required,force_password_change) VALUES (?,?,?,'system_administrator',0,1,0)")->execute([$name,$email,password_hash($pwd,PASSWORD_DEFAULT)]);
    $adminId=(int)db()->lastInsertId();db()->prepare("INSERT INTO user_roles(user_id,role_key,assigned_by) VALUES (?,'system_administrator',?)")->execute([$adminId,$adminId]);
   $categories=[['Amakuru','amakuru'],['Politiki','politiki'],['Ubukungu','ubukungu'],['Ikoranabuhanga','ikoranabuhanga'],['Ubuzima','ubuzima'],['Siporo','siporo'],['Umuco','umuco'],['Isi','isi'],['Iperereza','iperereza'],['Isesengura','isesengura']];
   foreach($categories as $i=>$c)db()->prepare('INSERT IGNORE INTO categories(name,slug,sort_order) VALUES (?,?,?)')->execute([$c[0],$c[1],$i+1]);
   $author=(int)db()->lastInsertId(); // unused; first account is selected below
   $author=(int)query_one('SELECT id FROM users WHERE email=?',[$email])['id'];
   $demo=[
    ['Umujyi w’ejo: uko gutegura ibikorwa remezo bishobora guhindura imibereho','amakuru','Ibisobanuro ku igenamigambi ry’imijyi, ubwikorezi n’imiterere y’ahazaza h’abaturage.','Iyi ni inkuru y’icyitegererezo igamije kwerekana uko igice cy’inkuru nyamukuru giteye. Ntabwo ivuga ko hari gahunda yemejwe cyangwa ibikorwa byabayeho.\n\nMu nkuru nyayo, umunyamakuru agomba gusobanura ikibazo, kwerekana ibimenyetso n’imibare, kubaza impuguke n’inzego bireba, no guha umusomyi amakuru asobanutse.','photo-1519501025264-65ba15a82390','news',1],
    ['Ubusobanuro bw’ibipimo bitanu by’ingenzi mu bukungu','ubukungu','Menya uburyo gusoma inflation, interest rates, GDP, exchange rate n’akazi bifasha kumva ubukungu.','Urugero rw’inkuru yigisha abasomyi gusobanukirwa ibipimo by’ubukungu. Iyi nyandiko ntitanga imibare y’ubu.\n\nUmunyamakuru w’ubukungu agomba kwifashisha amakuru yemewe y’ibigo bishinzwe imibare, Banki Nkuru n’izindi nzego, akerekana igihe imibare yakusanyirijwe.','photo-1611974789855-9c2a0a7236a3','analysis',0],
    ['Ikoranabuhanga rya AI: icyo ibigo bikeneye kumenya','ikoranabuhanga','AI ishobora gufasha imirimo imwe n’imwe, ariko hakenewe igenzura ry’ukuri n’ubwirinzi bw’amakuru.','Iyi ni inkuru ya demo ku mikoreshereze y’ikoranabuhanga. Ntabwo ari raporo y’ubushakashatsi bwakozwe.\n\nMu nkuru yizewe, dusuzuma ibishoboka, ikiguzi, ingaruka z’ubuzima bwite n’ibyemezo bifatwa n’abantu.','photo-1518770660439-4636190af475','news',0],
    ['Ishoramari rito: kubanza gupima ikibazo mbere y’umuti','ubukungu','Isoko, amafaranga, ibikorwa bya buri munsi n’amategeko bigomba gusuzumwa mbere yo gushora imari.','Inkuru y’icyitegererezo ku gusesengura umushinga.\n\nKugira igitekerezo cyiza ntibihagije. Hakenewe kumenya abakiriya, ikiguzi cyo kubageraho n’uko ubucuruzi buzakora.','photo-1460925895917-afdab827c52f','analysis',0],
    ['Amafoto n’inkuru: uko ifoto imwe ishobora gusobanura ibyabaye','umuco','Photojournalism isaba igihe, ahantu, caption, credit n’uruhushya rwo gukoresha amafoto.','Urugero rw’inkuru y’amafoto. Amafoto y’icyitegererezo si ibimenyetso by’ibikorwa bivugwa.\n\nKu nkuru nyazo, ubwanditsi bwandika uwafashe ifoto, aho yafatiwe n’itariki y’ifatwa.','photo-1492691527719-9d1e07e534b4','photo',0],
    ['Ubuzima rusange: uko gusoma ubushakashatsi bwa siyansi','ubuzima','Ibyavuye mu bushakashatsi ntibigomba gushyirwa mu nkuru hatasobanuwe uburyo bwakoreshejwe.','Iyi nyandiko ni iy’icyitegererezo, ntabwo ari inama y’ubuvuzi.\n\nInkuru y’ubuzima ikwiye kuba ifite inkomoko zizewe n’igenzura ry’impuguke.','photo-1576091160399-112ba8d25d1d','news',0],
    ['Imikino n’abafana: inkuru irenze amanota y’umukino','siporo','Inkuru y’imikino irushaho kugira ireme iyo isobanuye ibyagezweho n’impamvu byabaye.','Inkuru y’icyitegererezo ku mikino. Ntihavugwa umukino wabayeho.\n\nMu makuru nyayo, igihe, amanota n’abakina bigenzurwa mbere yo gutangaza.','photo-1461896836934-ffe607ba8211','news',0],
    ['Kugenzura amakuru kuri internet: inzira zifasha kwirinda ibihuha','isesengura','Inkomoko, itariki, amafoto n’ibimenyetso by’ibyavuzwe ni byo bibanza kugenzurwa.','Iyi ni inkuru yigisha uko igenzura ry’amakuru rikorwa.\n\nKugenzura inkomoko imwe ntibihagije; hakenewe izindi nkomoko zigenga n’uburyo bwo kwemeza ibimenyetso.','photo-1498050108023-c5249f4df085','factcheck',0],
    ['Umurage w’umuco n’ubuhanzi mu itangazamakuru rigezweho','umuco','Inkuru z’umuco zungukira ku majwi y’abahanzi, abashakashatsi n’abaturage.','Inkuru y’icyitegererezo. Nta muhanzi cyangwa igikorwa cyihariye gitangazwa hano.\n\nUbwanditsi bukwiye gusobanura inkomoko n’igihe cy’ibikorwa byavuzwe.','photo-1531058020387-3be344556be6','photo',0],
    ['Uburezi n’akazi: ubuhanga bukenewe mu gihe cya digitale','amakuru','Kumenya ingaruka z’ikoranabuhanga ku burezi bisaba ibimenyetso n’ibitekerezo bitandukanye.','Urugero rwo gutegura inkuru y’uburezi mu buryo busesengura.\n\nByaba ngombwa gukusanya amakuru yo mu mashuri, ibigo n’inzego bireba.','photo-1523050854058-8df90110c9f1','analysis',0],
    ['Video journalism: uburyo gutegura inkuru y’amashusho','ikoranabuhanga','Inkuru ya video ikenera script, shot list, consent, captions n’uburenganzira bw’amajwi.','Iyi ni demo article yo kwerekana imiterere y’inkuru ya video.\n\nMbere yo gutangaza video, hakenewe kwemeza uburenganzira bw’amashusho, isuzuma ry’amajwi, subtitles n’umutekano w’abantu bagaragara.','photo-1574717024653-61fd2cf4d44d','video',0],
    ['Ibimenyetso n’imibare: AFRIVOX_NEWS gisobanura imibereho','isesengura','Data journalism itanga ibisobanuro byimbitse iyo ifite amakuru agenzuwe.','Inkuru y’icyitegererezo. Imibare nyayo igomba kuba ifite source, method n’itariki.\n\nInkuru isobanuye neza itandukanya correlation na causation.','photo-1551288049-bebda4e38f71','analysis',0]
   ];
   foreach($demo as $i=>$d){
    $cat=(int)query_one('SELECT id FROM categories WHERE slug=?',[$d[1]])['id'];
    $art=['city','economy','technology','finance','photo','health','sports','fact','culture','education','video','data'];
    $im='assets/editorial/'.$art[$i].'.svg';
    db()->prepare("INSERT INTO articles(title,slug,summary,body,category_id,author_id,type,status,cover_image,image_caption,image_credit,is_featured,is_demo,published_at) VALUES (?,?,?,?,?,?,?,'published',?,?,?,?,1,?)")
      ->execute([$d[0],unique_slug($d[0]),$d[2],str_replace('\\n',"\n",$d[3]),$cat,$author,$d[5],$im,'Illustration y’icyitegererezo, ntabwo ari ifoto y’ibyabaye','AFRIVOX NEWS / Demo Illustration',$d[6],date('Y-m-d H:i:s',time()-$i*7200)]);
   }
   file_put_contents(__DIR__.'/app/installed.lock',date(DATE_ATOM));
   header('Location: '.path('admin/login.php?setup=complete'));exit;
  }catch(Throwable $e){error_log('Install: '.$e->getMessage());$error='Installation failed. Check server error log, DB privileges and PDO MySQL.';}
 }
}
?><!doctype html><html lang="rw"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Install · AFRIVOX NEWS</title><link rel="stylesheet" href="<?=e(path('assets/style.css'))?>"></head><body><main class="container form-page"><span class="overline">ONE-TIME SERVER INSTALLATION</span><h1>AFRIVOX NEWS setup<span class="brand-accent">.</span></h1><p>Create your first administrator. Database must already exist and config.local.php must be configured. Remove <code>install.php</code> after successful setup.</p><?php if($error):?><div class="form-error"><?=e($error)?></div><?php endif;?><form method="post" class="public-form"><?=csrf_field()?><label>ADMIN FULL NAME<input name="name" required></label><label>ADMIN EMAIL<input name="email" type="email" required></label><label>STRONG PASSWORD (MIN 14 CHARACTERS)<input name="password" type="password" minlength="14" autocomplete="new-password" required></label><button type="submit">CREATE NEWSROOM →</button></form></main></body></html>
