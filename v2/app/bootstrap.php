<?php
declare(strict_types=1);
ini_set('display_errors','0');
date_default_timezone_set('Europe/London');
session_set_cookie_params(['httponly'=>true,'secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off','samesite'=>'Lax']);
session_start();
$configFile=dirname(__DIR__).'/config.php';
if(!is_file($configFile)){header('Location: setup.php');exit;}
$config=require $configFile;
$pdo=new PDO('mysql:host='.$config['db_host'].';dbname='.$config['db_name'].';charset=utf8mb4',$config['db_user'],$config['db_pass'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);
function h(?string $v):string {return htmlspecialchars($v??'',ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
function csrf():string {return $_SESSION['csrf']??=bin2hex(random_bytes(32));}
function verify_csrf():void {if(!hash_equals(csrf(),(string)($_POST['csrf']??''))){http_response_code(403);exit('Invalid CSRF token');}}
function user():?array{global $pdo;
 if(empty($_SESSION['user']['id']))return null;
 $s=$pdo->prepare('SELECT id,username,role,is_active,session_version FROM users WHERE id=?');
 $s->execute([(int)$_SESSION['user']['id']]);$dbUser=$s->fetch();
 if(!$dbUser || !(int)$dbUser['is_active'] || (int)$dbUser['session_version'] !== (int)($_SESSION['user']['session_version']??0)) {unset($_SESSION['user']);return null;}
 $_SESSION['user']['role']=$dbUser['role'];$_SESSION['user']['username']=$dbUser['username'];return $_SESSION['user'];
}
require_once __DIR__.'/discord.php';
function require_login():array{
 global $pdo,$config;
 $me=user();if(!$me){header('Location: login.php');exit;}
 $hash=hash('sha256',session_id());
 $s=$pdo->prepare('SELECT id,revoked_at FROM staff_sessions WHERE session_hash=? AND user_id=?');
 $s->execute([$hash,$me['id']]);$record=$s->fetch();
 if(!$record || $record['revoked_at']){unset($_SESSION['user']);header('Location: login.php');exit;}
 if(empty($_SESSION['session_touch'])||time()-$_SESSION['session_touch']>=60){$pdo->prepare('UPDATE staff_sessions SET last_seen_at=NOW() WHERE id=?')->execute([$record['id']]);$_SESSION['session_touch']=time();}
 if(discord_enabled($pdo) && !in_array(basename($_SERVER['SCRIPT_NAME']??''),['discord-connect.php','discord-callback.php'],true)){
  $s=$pdo->prepare('SELECT discord_id FROM users WHERE id=?');$s->execute([$me['id']]);$did=(string)$s->fetchColumn();
  if(!$did){header('Location: discord-connect.php');exit;}
  if(empty($_SESSION['discord_verified_at'])||time()-$_SESSION['discord_verified_at']>300){
   if(!discord_authorised($pdo,$config,$did)){unset($_SESSION['discord_verified_at']);http_response_code(403);exit('Discord role access denied. Contact the fansite owner.');}
   $_SESSION['discord_verified_at']=time();
  }
 }
 return $me;
}
function can_manage(string $kind,string $role):bool{return in_array($role,['owner','admin'],true)||($kind==='dj'&&$role==='radio_manager')||($kind==='event'&&$role==='events_manager');}
function can_book(string $kind,string $role):bool{return in_array($role,['owner','admin'],true)||($kind==='dj'&&in_array($role,['radio_manager','dj'],true))||($kind==='event'&&in_array($role,['events_manager','event_host'],true));}
function audit(PDO $db,int $id,string $action,array $context=[]):void{$s=$db->prepare('INSERT INTO audit_logs (actor_id,action,context_json) VALUES (?,?,?)');$s->execute([$id,$action,json_encode($context)]);}

function site_settings(PDO $db,array $defaults=[]):array {
 $rows=$db->query('SELECT setting_key,setting_value FROM site_settings')->fetchAll(PDO::FETCH_KEY_PAIR);
 return array_replace($defaults,$rows);
}

function notify_user(PDO $db,int $id,string $message,string $link='dashboard.php'):void { $s=$db->prepare('INSERT INTO notifications(user_id,message,link) VALUES (?,?,?)');$s->execute([$id,mb_substr($message,0,255),$link]); }
function notify_managers(PDO $db,string $kind,string $message,string $link='dashboard.php'):void {
 $role=$kind==='dj'?'radio_manager':'events_manager';$s=$db->prepare("SELECT id FROM users WHERE is_active=1 AND role IN ('owner','admin',?)");$s->execute([$role]);foreach($s->fetchAll() as $u)notify_user($db,(int)$u['id'],$message,$link);
}
