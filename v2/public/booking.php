<?php
require __DIR__.'/../app/bootstrap.php';
$me=require_login();
if ($_SERVER['REQUEST_METHOD']!=='POST') {http_response_code(405);exit;}
verify_csrf();
function back(string $message):never {header('Location: dashboard.php?message='.rawurlencode($message));exit;}
$action=(string)($_POST['action']??'');
if($action==='create'){
 $kind=(string)($_POST['kind']??''); if(!in_array($kind,['dj','event'],true) || !can_book($kind,$me['role'])) {http_response_code(403);exit('Not permitted');}
 $title=trim((string)($_POST['title']??''));$details=trim((string)($_POST['details']??''));
 $start=DateTimeImmutable::createFromFormat('!Y-m-d\\TH:i',(string)($_POST['start_at']??''),new DateTimeZone('Europe/London'));
 $end=DateTimeImmutable::createFromFormat('!Y-m-d\\TH:i',(string)($_POST['end_at']??''),new DateTimeZone('Europe/London'));
 if(!$start || !$end || $title==='' || mb_strlen($title)>120 || mb_strlen($details)>2000 || $start<=new DateTimeImmutable('now',new DateTimeZone('Europe/London')) || $end<=$start || $end->getTimestamp()-$start->getTimestamp()>21600) back('Invalid booking details (maximum duration 6 hours).');
 $s=$pdo->prepare("INSERT INTO bookings (title,kind,host_id,start_at,end_at,details) VALUES (?,?,?,?,?,?)");
 $s->execute([$title,$kind,$me['id'],$start->format('Y-m-d H:i:s'),$end->format('Y-m-d H:i:s'),$details]);
 audit($pdo,$me['id'],'booking.created',['id'=>(int)$pdo->lastInsertId(),'kind'=>$kind]);notify_managers($pdo,$kind,'New booking awaiting review','dashboard.php');back('Booking submitted for approval.');
}
$id=filter_var($_POST['id']??null,FILTER_VALIDATE_INT);
if(!$id) back('Invalid booking.');
try {
 $pdo->beginTransaction();
 $s=$pdo->prepare('SELECT * FROM bookings WHERE id=? FOR UPDATE');$s->execute([$id]);$booking=$s->fetch();
 if(!$booking) { $pdo->rollBack(); back('Booking not found.'); }
 $manage=can_manage($booking['kind'],$me['role']);
 if(in_array($action,['approve','reject'],true) && !$manage) { $pdo->rollBack();http_response_code(403);exit('Not permitted'); }
 if($action==='cancel' && !$manage && (int)$booking['host_id']!==$me['id']) { $pdo->rollBack();http_response_code(403);exit('Not permitted'); }
 if(!in_array($action,['approve','reject','cancel'],true)) {$pdo->rollBack();back('Invalid action');}
 if($action==='approve' && $booking['status']==='pending') {
  // Serialises approval of a booking kind to prevent concurrent approvals of overlapping slots.
  $pdo->query("SELECT id FROM users WHERE role='owner' LIMIT 1 FOR UPDATE")->fetch();
  $s=$pdo->prepare("SELECT COUNT(*) FROM bookings WHERE id<>? AND kind=? AND status='approved' AND start_at<? AND end_at>?");
  $s->execute([$id,$booking['kind'],$booking['end_at'],$booking['start_at']]);
  if((int)$s->fetchColumn()>0) {$pdo->rollBack();back('Time slot already approved; choose another.');}
  $pdo->prepare("UPDATE bookings SET status='approved' WHERE id=?")->execute([$id]);
 } elseif($action==='reject' && $booking['status']==='pending') {$pdo->prepare("UPDATE bookings SET status='rejected' WHERE id=?")->execute([$id]);}
 elseif($action==='cancel' && in_array($booking['status'],['pending','approved'],true)) {$pdo->prepare("UPDATE bookings SET status='cancelled' WHERE id=?")->execute([$id]);}
 else {$pdo->rollBack();back('Booking cannot be changed from its current status.');}
 audit($pdo,$me['id'],'booking.'.$action,['id'=>$id]);notify_user($pdo,(int)$booking['host_id'],'Your booking '.$booking['title'].' was '.(['approve'=>'approved','reject'=>'rejected','cancel'=>'cancelled'][$action]??'updated').'.','dashboard.php');
 $pdo->commit();back('Booking updated.');
} catch(Throwable $e) {if($pdo->inTransaction())$pdo->rollBack();error_log($e->getMessage());http_response_code(500);exit('Booking update failed.');}
