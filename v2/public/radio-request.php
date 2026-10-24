<?php
require __DIR__.'/../app/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit;}
verify_csrf();
if(!empty($_SESSION['last_request']) && time()-$_SESSION['last_request']<60){http_response_code(429);echo json_encode(['error'=>'Please wait before sending another request.']);exit;}
$name=trim((string)($_POST['name']??''));
$song=trim((string)($_POST['song']??''));
if($name===''||$song===''||mb_strlen($name)>40||mb_strlen($song)>180){http_response_code(422);echo json_encode(['error'=>'Enter a name and song within the limits.']);exit;}
$stmt=$pdo->prepare("INSERT INTO song_requests (requester,song,status) VALUES (?,?,'waiting')");
$stmt->execute([$name,$song]);
$_SESSION['last_request']=time();
echo json_encode(['success'=>true]);