<?php
require __DIR__.'/../app/bootstrap.php';
$count=(int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
if($count!==0){http_response_code(404);exit('Setup is unavailable.');}
$secret=(string)($config['setup_key']??'');
if(strlen($secret)<32 || $secret==='REPLACE_WITH_RANDOM_SECRET_AT_LEAST_32_CHARACTERS') {http_response_code(503);exit('Configure a unique setup_key in config.php before using setup.');}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();
 $key=(string)($_POST['setup_key']??'');
 $username=trim((string)($_POST['username']??''));
 $password=(string)($_POST['password']??'');
 if(!hash_equals($secret,$key) || !preg_match('/^[A-Za-z0-9_-]{3,40}$/',$username) || strlen($password)<14 || strlen($password)>256){$error='Check setup key, username and password (minimum 14 characters).';}
 else {
  try{
   $pdo->beginTransaction();
   // Exclusive locking protects first-user creation against simultaneous installation requests.
   $pdo->query('SELECT id FROM users FOR UPDATE')->fetchAll();
   if((int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn()!==0) throw new RuntimeException('An account already exists');
   $s=$pdo->prepare("INSERT INTO users (username,password_hash,role) VALUES (?,?,'owner')");
   $s->execute([$username,password_hash($password,PASSWORD_DEFAULT)]);
   $pdo->commit();session_regenerate_id(true);
   $_SESSION['user']=['id'=>(int)$pdo->lastInsertId(),'username'=>$username,'role'=>'owner'];
   header('Location: dashboard.php');exit;
  }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();error_log($e->getMessage());$error='Unable to complete setup. Check server logs.';}
 }
}
?><!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="style.css"><title>Fansite setup</title><main class="login"><section class="panel"><div class="pixel">◆ ONE-TIME SETUP</div><h1>Create site owner</h1><p>Import the SQL schema and configure your private config.php first. This page closes once the owner exists.</p><?php if($error):?><p class="error"><?=h($error)?></p><?php endif;?><form method="post"><input type="hidden" name="csrf" value="<?=h(csrf())?>"><label>Setup key<input type="password" name="setup_key" required autocomplete="off"></label><label>Owner username<input name="username" required minlength="3" maxlength="40"></label><label>Strong password<input type="password" name="password" required minlength="14" autocomplete="new-password"></label><button>Create owner</button></form></section></main></html>