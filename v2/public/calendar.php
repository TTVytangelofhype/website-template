<?php
require __DIR__.'/../app/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=60');
$from=(string)($_GET['from']??date('Y-m-d'));
$to=(string)($_GET['to']??date('Y-m-d',strtotime('+30 days')));
if(!preg_match('/^\d{4}-\d{2}-\d{2}$/',$from) || !preg_match('/^\d{4}-\d{2}-\d{2}$/',$to) || $to<$from || strtotime($to)-strtotime($from)>93*86400) {http_response_code(422);echo json_encode(['error'=>'Invalid date range']);exit;}
$s=$pdo->prepare("SELECT b.id,b.kind,b.title,b.start_at,b.end_at,u.username AS host FROM bookings b JOIN users u ON b.host_id=u.id WHERE b.status='approved' AND b.start_at < DATE_ADD(?, INTERVAL 1 DAY) AND b.end_at >= ? ORDER BY b.start_at LIMIT 500");
$s->execute([$to,$from]);
echo json_encode(['timezone'=>'Europe/London','bookings'=>$s->fetchAll()],JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);