<?php
declare(strict_types=1);
// Standalone installer: must not load app/bootstrap.php until database exists.
ini_set('display_errors','0');
$root=dirname(__DIR__);
$configFile=$root.'/config.php';
$lockFile=$root.'/.installed';
$tokenFile=$root.'/install-token.txt';
if(is_file($configFile)||is_file($lockFile)){http_response_code(403);exit('This fansite is already configured. Installation is disabled.');}
if(!extension_loaded('pdo_mysql')||!extension_loaded('mbstring')){http_response_code(503);exit('Your host needs PHP PDO MySQL and mbstring enabled.');}
if(!is_dir($root)||!is_writable($root)){http_response_code(503);exit('The v2 directory must be temporarily writable for installation.');}
if(!is_file($tokenFile)){
 $token=bin2hex(random_bytes(24));
 if(file_put_contents($tokenFile,$token."\n",LOCK_EX)===false){http_response_code(503);exit('Unable to create installation token. Check directory permissions.');}
 @chmod($tokenFile,0600);
}
session_set_cookie_params(['httponly'=>true,'secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off','samesite'=>'Lax']);
session_start();
function e(string $s):string{return htmlspecialchars($s,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
$_SESSION['install_csrf']??=bin2hex(random_bytes(32));
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $csrf=(string)($_POST['csrf']??'');
 $submittedToken=trim((string)($_POST['install_token']??''));
 $realToken=trim((string)file_get_contents($tokenFile));
 $dbHost=trim((string)($_POST['db_host']??'localhost'));
 $dbName=trim((string)($_POST['db_name']??''));
 $dbUser=trim((string)($_POST['db_user']??''));
 $dbPass=(string)($_POST['db_pass']??'');
 $name=trim((string)($_POST['site_name']??''));
 $tagline=trim((string)($_POST['tagline']??''));
 $stream=trim((string)($_POST['stream_url']??''));
 $owner=trim((string)($_POST['owner']??''));
 $password=(string)($_POST['password']??'');
 if(!hash_equals($_SESSION['install_csrf'],$csrf)||!hash_equals($realToken,$submittedToken)){$error='Installation key is incorrect. Find it in v2/install-token.txt with File Manager.';}
 elseif(!preg_match('/^[A-Za-z0-9._:-]{1,120}$/',$dbHost)||!preg_match('/^[A-Za-z0-9_]{1,64}$/',$dbName)||$dbUser===''||strlen($dbUser)>128){$error='Check the database connection details.';}
 elseif(mb_strlen($name)<3||mb_strlen($name)>70||mb_strlen($tagline)>170||($stream!==''&&(!filter_var($stream,FILTER_VALIDATE_URL)||parse_url($stream,PHP_URL_SCHEME)!=='https'))){$error='Check the fansite name, tagline and HTTPS radio stream URL.';}
 elseif(!preg_match('/^[A-Za-z0-9_-]{3,40}$/',$owner)||strlen($password)<14||strlen($password)>256){$error='Use a valid owner username and a password of 14–256 characters.';}
 else{
  try{
   $db=new PDO("mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4",$dbUser,$dbPass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_EMULATE_PREPARES=>false]);
   $tableExists=$db->query("SHOW TABLES LIKE 'users'")->fetchColumn();
   if($tableExists){throw new RuntimeException('This database already has a users table. Use a clean database for a new installation.');}
   foreach(['schema.sql','002_radio.sql','003_settings.sql','005_applications.sql','006_notifications.sql','007_reminders.sql','008_security.sql','009_access_denials.sql'] as $sqlFile){
    $sql=file_get_contents($root.'/database/'.$sqlFile);
    foreach(explode(';',$sql) as $statement){if(trim($statement)!=='')$db->exec($statement);}
   }
   $s=$db->prepare("INSERT INTO users(username,password_hash,role) VALUES (?,?,'owner')");
   $s->execute([$owner,password_hash($password,PASSWORD_DEFAULT)]);
   $settings=['db_host'=>$dbHost,'db_name'=>$dbName,'db_user'=>$dbUser,'db_pass'=>$dbPass,'site'=>['name'=>$name,'tagline'=>$tagline,'stream_url'=>$stream]];
   $php="<?php\nreturn ".var_export($settings,true).";\n";
   // Exclusive create prevents accidental overwrite of existing setup.
   $handle=@fopen($configFile,'x');
   if(!$handle)throw new RuntimeException('Unable to create config.php. Check directory permissions.');
   if(fwrite($handle,$php)!==strlen($php)){fclose($handle);@unlink($configFile);throw new RuntimeException('Could not write complete configuration.');}
   fclose($handle);@chmod($configFile,0600);
   file_put_contents($lockFile,'installed',LOCK_EX);@chmod($lockFile,0600);
   @unlink($tokenFile);
   session_regenerate_id(true);
   $_SESSION['user']=['id'=>(int)$db->lastInsertId(),'username'=>$owner,'role'=>'owner','session_version'=>1];
   $db->prepare('INSERT INTO staff_sessions(user_id,session_hash,ip_address,user_agent) VALUES (?,?,?,?)')->execute([$_SESSION['user']['id'],hash('sha256',session_id()),(string)($_SERVER['REMOTE_ADDR']??'unknown'),mb_substr((string)($_SERVER['HTTP_USER_AGENT']??''),0,255)]);
   header('Location: dashboard.php');exit;
  }catch(Throwable $ex){error_log('Fansite installation error: '.$ex->getMessage());$error='Installation stopped: '.e($ex->getMessage()).' Check database and directory permissions. If tables were created, use a fresh empty database before retrying.';}
 }
}
?><!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="style.css"><title>Hotel Fansite Installer</title><main class="login"><section class="panel installer"><div class="pixel">◆ WELCOME TO YOUR HOTEL ◆</div><h1>Set up your fansite</h1><p>Everything is configured here—no PHP editing needed. You only need a new MySQL database from your host's control panel.</p><?php if($error):?><p class="error"><?=e($error)?></p><?php endif;?><form method="post"><input type="hidden" name="csrf" value="<?=e($_SESSION['install_csrf'])?>"><h2>1. Installation security</h2><p>Open your hosting File Manager and copy the key from <code>v2/install-token.txt</code>. This prevents strangers from claiming your new site.</p><label>Installation key<input type="password" name="install_token" autocomplete="off" required></label><h2>2. Database</h2><label>Database host<input name="db_host" value="<?=e((string)($_POST['db_host']??'localhost'))?>" required></label><label>Database name<input name="db_name" value="<?=e((string)($_POST['db_name']??''))?>" required></label><label>Database username<input name="db_user" value="<?=e((string)($_POST['db_user']??''))?>" required></label><label>Database password<input type="password" name="db_pass" autocomplete="new-password"></label><h2>3. Your Habbo fansite</h2><label>Fansite name<input name="site_name" maxlength="70" placeholder="Hotel Wave" value="<?=e((string)($_POST['site_name']??''))?>" required></label><label>Tagline<input name="tagline" maxlength="170" placeholder="Your community, your radio" value="<?=e((string)($_POST['tagline']??''))?>"></label><label>Radio stream HTTPS URL (optional)<input type="url" name="stream_url" placeholder="https://example.com/radio" value="<?=e((string)($_POST['stream_url']??''))?>"></label><h2>4. Owner account</h2><label>Owner username<input name="owner" pattern="[A-Za-z0-9_-]{3,40}" minlength="3" maxlength="40" required value="<?=e((string)($_POST['owner']??''))?>"></label><label>Strong owner password (14+ characters)<input type="password" name="password" minlength="14" autocomplete="new-password" required></label><button type="submit">Create my fansite →</button></form><p class="soft-note">Only install on HTTPS. After setup this installer locks automatically; restrict directory write permissions once finished.</p></section></main></html>