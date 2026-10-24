<?php
require __DIR__.'/../app/bootstrap.php';$me=require_login();
if(!in_array($me['role'],['owner','admin','radio_manager','dj'],true)){http_response_code(403);exit('Not permitted');}
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();$id=filter_var($_POST['id']??null,FILTER_VALIDATE_INT);
 $status=(string)($_POST['status']??'');
 if(!$id||!in_array($status,['played','rejected'],true)){http_response_code(422);exit('Invalid request');}
 $s=$pdo->prepare("UPDATE song_requests SET status=? WHERE id=? AND status='waiting'");$s->execute([$status,$id]);
 audit($pdo,$me['id'],'song_request.'.$status,['id'=>$id]);
 header('Location: staff-requests.php');exit;
}
$requests=$pdo->query("SELECT id,requester,song,created_at FROM song_requests WHERE status='waiting' ORDER BY created_at ASC LIMIT 100")->fetchAll();
?><!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Song requests</title><link rel="stylesheet" href="style.css"><main><a href="dashboard.php">← Dashboard</a><h1>DJ Song Requests</h1><section class="panel"><div class="tablewrap"><table><tr><th>Requested</th><th>Name</th><th>Song</th><th>Actions</th></tr><?php foreach($requests as $r):?><tr><td><?=h($r['created_at'])?></td><td><?=h($r['requester'])?></td><td><?=h($r['song'])?></td><td><form method="post" class="inline"><input type="hidden" name="csrf" value="<?=h(csrf())?>"><input type="hidden" name="id" value="<?=(int)$r['id']?>"><button name="status" value="played">Played</button><button class="muted" name="status" value="rejected">Reject</button></form></td></tr><?php endforeach;?></table></div></section></main></html>