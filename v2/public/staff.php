<?php
require __DIR__.'/../app/bootstrap.php';
$me=require_login();
if($me['role']!=='owner'){http_response_code(403);exit('Owner access required.');}
$roles=['admin','radio_manager','events_manager','dj','event_host','member'];
$error='';$success='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();
 $action=(string)($_POST['action']??'');
 $id=filter_var($_POST['id']??null,FILTER_VALIDATE_INT);
 $role=(string)($_POST['role']??'');
 $pw=(string)($_POST['password']??'');
 try{
  if($action==='create'){
   $username=trim((string)($_POST['username']??''));
   if(!preg_match('/^[A-Za-z0-9_-]{3,40}$/',$username)||!in_array($role,$roles,true)||strlen($pw)<14||strlen($pw)>256)throw new InvalidArgumentException('Username, role or password is invalid. Passwords must be 14–256 characters.');
   $stmt=$pdo->prepare("INSERT INTO users(username,password_hash,role,is_active) VALUES(?,?,?,1)");
   $stmt->execute([$username,password_hash($pw,PASSWORD_DEFAULT),$role]);
   audit($pdo,(int)$me['id'],'staff.created',['id'=>(int)$pdo->lastInsertId(),'role'=>$role]);
   $success='Staff account created.';
  }else{
   if(!$id||$id===(int)$me['id'])throw new InvalidArgumentException('Cannot modify your own owner account.');
   $stmt=$pdo->prepare('SELECT id,role,is_active FROM users WHERE id=?');$stmt->execute([$id]);$target=$stmt->fetch();
   if(!$target||$target['role']==='owner')throw new InvalidArgumentException('Account unavailable for modification.');
   if($action==='role'){
    if(!in_array($role,$roles,true))throw new InvalidArgumentException('Invalid role.');
    $pdo->prepare('UPDATE users SET role=?,session_version=session_version+1 WHERE id=?')->execute([$role,$id]);
   }elseif($action==='toggle'){
    $pdo->prepare('UPDATE users SET is_active=IF(is_active=1,0,1),session_version=session_version+1 WHERE id=?')->execute([$id]);
   }elseif($action==='password'){
    if(strlen($pw)<14||strlen($pw)>256)throw new InvalidArgumentException('Replacement password must be 14–256 characters.');
    $pdo->prepare('UPDATE users SET password_hash=?,session_version=session_version+1 WHERE id=?')->execute([password_hash($pw,PASSWORD_DEFAULT),$id]);
   }else throw new InvalidArgumentException('Unknown action.');
   audit($pdo,(int)$me['id'],'staff.'.$action,['id'=>$id,'role'=>$action==='role'?$role:null]);
   $success='Staff account updated; any previous login session has been revoked.';
  }
 }catch(PDOException $e){error_log($e->getMessage());$error=$e->getCode()==='23000'?'That username already exists.':'Database update failed. Verify migration 004_staff.sql.';}
 catch(InvalidArgumentException $e){$error=$e->getMessage();}
}
$staff=$pdo->query('SELECT id,username,role,is_active,created_at FROM users ORDER BY FIELD(role,"owner","admin","radio_manager","events_manager","dj","event_host","member"),username')->fetchAll();
?><!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Staff Management</title><link rel="stylesheet" href="style.css">
<div class="layout"><aside><div class="pixel">◆ HOTEL STAFF</div><h2>Owner tools</h2><nav><a href="dashboard.php">← Dashboard</a><a href="settings.php">⚙ Site settings</a><a href="index.php">⌂ Public site</a></nav></aside><main><header><span class="tag">Owner access</span><h1>Staff Management</h1><p>Invite staff by creating an account, assign ranks and control access.</p></header>
<?php if($error):?><p class="error"><?=h($error)?></p><?php endif;?><?php if($success):?><p class="notice"><?=h($success)?></p><?php endif;?>
<section class="panel"><h2>Create a staff account</h2><form method="post"><input type="hidden" name="csrf" value="<?=h(csrf())?>"><input type="hidden" name="action" value="create"><label>Username<input name="username" pattern="[A-Za-z0-9_-]{3,40}" required></label><label>Rank<select name="role"><?php foreach($roles as $r):?><option value="<?=h($r)?>"><?=h(ucwords(str_replace('_',' ',$r)))?></option><?php endforeach;?></select></label><label>Temporary password (share privately, 14+ characters)<input name="password" type="password" minlength="14" required autocomplete="new-password"></label><button>Create account</button></form></section>
<section class="panel"><h2>Staff accounts</h2><div class="tablewrap"><table><thead><tr><th>Staff member</th><th>Role</th><th>Status</th><th>Permissions</th><th>Access</th><th>Password reset</th></tr></thead><tbody>
<?php foreach($staff as $s):?><tr><td><?=h($s['username'])?></td><td><?=h($s['role'])?></td><td><?=((int)$s['is_active'])?'Active':'Suspended'?></td>
<?php if($s['role']==='owner'):?><td colspan="3">Protected owner account</td><?php else:?>
<td><form method="post" class="inline"><input type="hidden" name="csrf" value="<?=h(csrf())?>"><input type="hidden" name="id" value="<?=(int)$s['id']?>"><select name="role" aria-label="Select role"><?php foreach($roles as $r):?><option value="<?=h($r)?>" <?=$s['role']===$r?'selected':''?>><?=h($r)?></option><?php endforeach;?></select><button name="action" value="role">Save</button></form></td>
<td><form method="post"><input type="hidden" name="csrf" value="<?=h(csrf())?>"><input type="hidden" name="id" value="<?=(int)$s['id']?>"><button class="muted" name="action" value="toggle"><?=((int)$s['is_active'])?'Suspend':'Reactivate'?></button></form></td>
<td><form method="post" class="inline"><input type="hidden" name="csrf" value="<?=h(csrf())?>"><input type="hidden" name="id" value="<?=(int)$s['id']?>"><input type="password" minlength="14" name="password" required placeholder="New password" aria-label="New password for <?=h($s['username'])?>"><button name="action" value="password">Reset</button></form></td><?php endif;?></tr><?php endforeach;?></tbody></table></div></section></main></div></html>