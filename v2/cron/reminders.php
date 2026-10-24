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
$options=site_settings($pdo,['discord_enabled'=>'0','discord_webhook'=>'']);
foreach($query->fetchAll() as $booking){
 try {
  $pdo->beginTransaction();
  $insert=$pdo->prepare("INSERT IGNORE INTO reminder_deliveries (booking_id,reminder_type) VALUES (?,'30m')");
  $insert->execute([(int)$booking['id']]);
  if($insert->rowCount()===1){
   notify_user($pdo,(int)$booking['host_id'],'Your upcoming shift starts in about 30 minutes: '.$booking['title'],'rota.php');
  }
  $pdo->commit();
  if($insert->rowCount()===1 && $options['discord_enabled']==='1' && extension_loaded('curl')){
   $url=$options['discord_webhook'];$parts=parse_url($url);
   if(is_array($parts) && ($parts['scheme']??'')==='https' && in_array($parts['host']??'', ['discord.com','discordapp.com'],true) && str_starts_with($parts['path']??'','/api/webhooks/')){
    $msg='Upcoming '.($booking['kind']==='dj'?'DJ show':'event').': '.str_replace('@','＠',mb_substr($booking['title'],0,120)).' in about 30 minutes.';
    $ch=curl_init($url);
    curl_setopt_array($ch,[CURLOPT_POST=>true,CURLOPT_HTTPHEADER=>['Content-Type: application/json'],CURLOPT_POSTFIELDS=>json_encode(['content'=>$msg,'allowed_mentions'=>['parse'=>[]]]),CURLOPT_RETURNTRANSFER=>true,CURLOPT_CONNECTTIMEOUT=>3,CURLOPT_TIMEOUT=>7]);
    curl_exec($ch);
    $status=curl_getinfo($ch,CURLINFO_HTTP_CODE);
    curl_close($ch);
    if($status<200 || $status>=300)error_log('Discord reminder delivery failed, status '.$status);
   }
  }
 }catch(Throwable $error){
  if($pdo->inTransaction())$pdo->rollBack();
  error_log('Reminder task error: '.$error->getMessage());
 }
}
