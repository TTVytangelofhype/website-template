<?php
require __DIR__.'/../app/bootstrap.php';
$me=require_login();$message='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();$action=(string)($_POST['action']??'');$id=filter_var($_POST['id']??null,FILTER_VALIDATE_INT);
 if(!$id){http_response_code(422);exit('Invalid request.');}
 try{
  $pdo->beginTransaction();
  if($action==='request'){
   $s=$pdo->prepare('SELECT * FROM bookings WHERE id=? FOR UPDATE');$s->execute([$id]);$b=$s->fetch();
   $reason=trim((string)($_POST['reason']??''));
   if(!$b||(int)$b['host_id']!==(int)$me['id']||$b['status']!=='approved'||mb_strlen($reason)<5||mb_strlen($reason)>500)throw new RuntimeException('Invalid cancellation request.');
   $s=$pdo->prepare("SELECT COUNT(*) FROM cancellation_requests WHERE booking_id=? AND status='pending'");$s->execute([$id]);
   if($s->fetchColumn())throw new RuntimeException('Cancellation already requested.');
   $pdo->prepare('INSERT INTO cancellation_requests(booking_id,requested_by,reason) VALUES (?,?,?)')->execute([$id,$me['id'],$reason]);
   notify_managers($pdo,$b['kind'],'Cancellation requested for booking #'.$id,'cancellations.php');
  }elseif(in_array($action,['approve','deny'],true)){
   $s=$pdo->prepare('SELECT cr.*,b.kind,b.host_id,b.status AS booking_status FROM cancellation_requests cr JOIN bookings b ON b.id=cr.booking_id WHERE cr.id=? FOR UPDATE');
   $s->execute([$id]);$c=$s->fetch();
   if(!$c||$c['status']!=='pending'||!can_manage($c['kind'],$me['role']))throw new RuntimeException('Cannot review this request.');
   $status=$action==='approve'?'approved':'denied';
   $pdo->prepare('UPDATE cancellation_requests SET status=?,reviewed_by=?,reviewed_at=NOW() WHERE id=?')->execute([$status,$me['id'],$id]);
   if($status==='approved'&&$c['booking_status']==='approved')$pdo->prepare("UPDATE bookings SET status='cancelled' WHERE id=?")->execute([$c['booking_id']]);
   notify_user($pdo,(int)$c['requested_by'],'Your cancellation request has been '.$status.'.','dashboard.php');
  }else throw new RuntimeException('Invalid action.');
  audit($pdo,$me['id'],'cancellation.'.$action,['id'=>$id]);$pdo->commit();$message='Request saved.';
 }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();$message=$e->getMessage();}
}
$mine=$pdo->prepare("SELECT b.id,b.title,b.kind,b.start_at FROM bookings b WHERE b.host_id=? AND b.status='approved' AND b.end_at>=NOW() ORDER BY b.start_at");
$mine->execute([$me['id']]);
$requests=$pdo->query("SELECT c.id,c.reason,c.status,b.title,b.kind,b.start_at,u.username FROM cancellation_requests c JOIN bookings b ON b.id=c.booking_id JOIN users u ON u.id=c.requested_by WHERE c.status='pending' ORDER BY c.created_at LIMIT 100")->fetchAll();
?><!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Shift Cancellations</title><link rel="stylesheet" href="style.css"><main><a href="dashboard.php">← Dashboard</a><h1>Shift cancellation requests</h1><?php if($message):?><p class="notice"><?=h($message)?></p><?php endif;?><section class="panel"><h2>Request to cancel your shift</h2><form method="post"><input type="hidden" name="csrf" value="<?=h(csrf())?>"><label>Confirmed booking<select name="id" required><?php foreach($mine->fetchAll() as $b):?><option value="<?=(int)$b['id']?>"><?=h($b['title'])?> — <?=h($b['start_at'])?></option><?php endforeach;?></select></label><label>Reason<textarea name="reason" minlength="5" maxlength="500" required></textarea></label><button name="action" value="request">Request cancellation</button></form></section><section class="panel"><h2>Pending review</h2><?php foreach($requests as $c):if(!can_manage($c['kind'],$me['role']))continue;?><article class="notice-item"><strong><?=h($c['title'])?></strong> — <?=h($c['username'])?><p><?=h($c['reason'])?></p><form method="post" class="inline"><input type="hidden" name="csrf" value="<?=h(csrf())?>"><input type="hidden" name="id" value="<?=(int)$c['id']?>"><button name="action" value="approve">Approve</button><button class="muted" name="action" value="deny">Deny</button></form></article><?php endforeach;?></section></main></html>