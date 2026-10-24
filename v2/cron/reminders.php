<?php
declare(strict_types=1);
// Run every five minutes from the host's PHP CLI scheduler.
if (PHP_SAPI !== 'cli') { exit; }
require dirname(__DIR__).'/app/bootstrap.php';
$now=new DateTimeImmutable('now',new DateTimeZone('Europe/London'));
$from=$now->modify('+25 minutes')->format('Y-m-d H:i:s');
$until=$now->modify('+35 minutes')->format('Y-m-d H:i:s');
$query=$pdo->prepare("SELECT id,host_id,title,kind FROM bookings WHERE status='approved' AND start_at>=? AND start_at<?");
$query->execute([$from,$until]);
foreach($query->fetchAll() as $booking){
 try {
  $pdo->beginTransaction();
  $insert=$pdo->prepare("INSERT IGNORE INTO reminder_deliveries (booking_id,reminder_type) VALUES (?,'30m')");
  $insert->execute([(int)$booking['id']]);
  if($insert->rowCount()===1){
   notify_user($pdo,(int)$booking['host_id'],'Your upcoming shift starts in about 30 minutes: '.$booking['title'],'rota.php');
  }
  $pdo->commit();
 }catch(Throwable $error){
  if($pdo->inTransaction())$pdo->rollBack();
  error_log('Reminder task error: '.$error->getMessage());
 }
}
