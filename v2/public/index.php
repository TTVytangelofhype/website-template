<?php
require __DIR__.'/../app/bootstrap.php';
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 verify_csrf();
 $username=trim((string)($_POST['username']??''));
 $s=$pdo->prepare('SELECT id,username,password_hash,role FROM users WHERE username=?');
 $s->execute([$username]); $found=$s->fetch();
 if ($found && password_verify((string)($_POST['password']??''), $found['password_hash'])) {
  session_regenerate_id(true);
  $_SESSION['user']=['id'=>(int)$found['id'],'username'=>$found['username'],'role'=>$found['role']];
  header('Location: dashboard.php');exit;
 }
 $error='Invalid username or password';
}
if (user()) { header('Location: dashboard.php');exit; }
?><!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Fansite Staff Login</title><link rel="stylesheet" href="style.css"><main class="login"><section class="panel"><div class="pixel">◆ HOTEL RADIO ◆</div><h1>Fansite Staff Hub</h1><p>DJ shows, Habbo events and staff tools under one roof.</p><?php if($error):?><p class="error"><?=h($error)?></p><?php endif;?><form method="post"><input type="hidden" name="csrf" value="<?=h(csrf())?>"><label>Username<input name="username" autocomplete="username" required></label><label>Password<input name="password" type="password" autocomplete="current-password" required></label><button>Sign in</button></form></section></main></html>