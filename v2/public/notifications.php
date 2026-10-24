<?php
require __DIR__.'/../app/bootstrap.php';
$me=require_login();
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();$id=filter_var($_POST['id']??null,FILTER_VALIDATE_INT);
 if(!$id){http_response_code(422);exit('Invalid notification.');}
 $s=$pdo->prepare('UPDATE notifications SET is_read=1 WHERE id=? AND user_id=?');
 $s->execute([$id,$me['id']]);header('Location: notifications.php');exit;
}
$s=$pdo->prepare('SELECT id,message,link,is_read,created_at FROM notifications WHERE user_id=? ORDER BY created_at DESC LIMIT 100');
$s->execute([$me['id']]);$items=$s->fetchAll();
?><!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Notifications</title><link rel="stylesheet" href="style.css"><main><a href="dashboard.php">← Dashboard</a><h1>Notifications</h1><section class="panel"><?php if(!$items):?><p>No notifications yet.</p><?php endif;?><?php foreach($items as $n):?><article class="notice-item"><p><?=h($n['message'])?></p><small><?=h($n['created_at'])?></small> · <a href="<?=h($n['link'])?>">Open</a><?php if(!(int)$n['is_read']):?><form method="post"><input type="hidden" name="csrf" value="<?=h(csrf())?>"><input type="hidden" name="id" value="<?=(int)$n['id']?>"><button class="muted">Mark read</button></form><?php endif;?></article><?php endforeach;?></section></main></html>