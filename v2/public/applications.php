<?php
require __DIR__.'/../app/bootstrap.php';
$me=require_login();
if(!in_array($me['role'],['owner','admin','radio_manager','events_manager'],true)){http_response_code(403);exit('Not authorised.');}
function allowed_review(string $role,string $position):bool{return in_array($role,['owner','admin'],true)||($role==='radio_manager'&&$position==='dj')||($role==='events_manager'&&$position==='event_host');}
$message='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();
 $id=filter_var($_POST['id']??null,FILTER_VALIDATE_INT);
 $status=(string)($_POST['status']??'');
 if(!$id||!in_array($status,['accepted','declined'],true)){http_response_code(422);exit('Invalid review.');}
 $s=$pdo->prepare('SELECT role_requested,status FROM staff_applications WHERE id=?');$s->execute([$id]);$target=$s->fetch();
 if(!$target||!allowed_review($me['role'],$target['role_requested'])){http_response_code(403);exit('Not authorised for this application.');}
 $s=$pdo->prepare("UPDATE staff_applications SET status=?,reviewer_id=?,reviewed_at=NOW() WHERE id=? AND status='pending'");
 $s->execute([$status,$me['id'],$id]);
 if($s->rowCount()){audit($pdo,$me['id'],'application.'.$status,['id'=>$id]);$message='Application reviewed. An accepted application does not create a login account.';}
}
$filter=in_array($me['role'],['owner','admin'],true)?'':($me['role']==='radio_manager'?'dj':'event_host');
$s=$pdo->prepare("SELECT id,habbo_name,role_requested,experience,availability,motivation,submitted_at FROM staff_applications WHERE status='pending' AND (?='' OR role_requested=?) ORDER BY submitted_at LIMIT 100");
$s->execute([$filter,$filter]);$applications=$s->fetchAll();
?><!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Review Applications</title><link rel="stylesheet" href="style.css"><main><a href="dashboard.php">← Staff dashboard</a><h1>Staff Applications</h1><p>Review new team applications. Only an owner can create a staff login from Staff Management.</p><?php if($message):?><p class="notice"><?=h($message)?></p><?php endif;?><?php if(!$applications):?><p class="panel">No pending applications.</p><?php endif;?><?php foreach($applications as $a):?><section class="panel"><span class="tag"><?=h($a['role_requested'])?></span><h2><?=h($a['habbo_name'])?></h2><small>Submitted <?=h($a['submitted_at'])?></small><h3>Experience</h3><p><?=nl2br(h($a['experience']))?></p><h3>Availability</h3><p><?=h($a['availability'])?></p><h3>Why join?</h3><p><?=nl2br(h($a['motivation']))?></p><form method="post" class="inline"><input type="hidden" name="csrf" value="<?=h(csrf())?>"><input type="hidden" name="id" value="<?=(int)$a['id']?>"><button name="status" value="accepted">Accept</button><button class="muted" name="status" value="declined">Decline</button></form></section><?php endforeach;?></main></html>