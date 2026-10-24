<?php
require __DIR__.'/../app/bootstrap.php';
$me=require_login();
$bookings=$pdo->query("SELECT b.*,u.username FROM bookings b JOIN users u ON u.id=b.host_id WHERE b.start_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY b.start_at ASC LIMIT 100")->fetchAll();
$message=(string)($_GET['message']??'');
?><!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Staff Hub — Bookings</title><link rel="stylesheet" href="style.css">
<div class="layout"><aside><div class="pixel">◆ FANSiTE V2</div><h2>Staff Hub</h2><nav><a href="dashboard.php">▦ Dashboard</a><a href="#bookings">♫ Bookings</a><a href="#schedule">▤ Schedule</a><a href="rota.php">▦ Weekly Rota</a><?php if(in_array($me['role'],['owner','admin','radio_manager','events_manager'],true)):?><a href="applications.php">✉ Applications</a><?php endif;?><?php if(in_array($me['role'],['owner','admin','radio_manager','dj'],true)):?><a href="staff-requests.php">♫ Song Requests</a><?php endif;?><?php if($me['role']==='owner'):?><a href="settings.php">⚙ Fansite Settings</a><a href="staff.php">♙ Staff Management</a><?php endif;?><a href="index.php">⌂ Public Site</a></nav><small>Signed in as <?=h($me['username'])?> (<?=h($me['role'])?>)</small><form action="logout.php" method="post"><input type="hidden" name="csrf" value="<?=h(csrf())?>"><button class="muted">Log out</button></form></aside>
<main><header><span class="tag">Habbo-inspired community hub</span><h1>Welcome back, <?=h($me['username'])?></h1><p>Manage radio shows and hotel events from one calendar.</p></header>
<?php if($message):?><p class="notice"><?=h($message)?></p><?php endif;?>
<section class="cards"><div class="panel"><span>Upcoming bookings</span><h2><?=count($bookings)?></h2></div><div class="panel"><span>Your role</span><h2><?=h(ucwords(str_replace('_',' ',$me['role'])))?></h2></div></section>
<section id="bookings" class="panel"><h2>Request a booking</h2><p>All requested slots require approval. Times use the Europe/London timezone.</p><form method="post" action="booking.php">
<input type="hidden" name="csrf" value="<?=h(csrf())?>"><input type="hidden" name="action" value="create">
<label>Booking type<select name="kind"><option value="dj">DJ show</option><option value="event">Habbo event</option></select></label>
<label>Title<input name="title" required maxlength="120" placeholder="Saturday Night Requests"></label>
<label>Start (UK local time)<input type="datetime-local" name="start_at" required></label><label>End (UK local time)<input type="datetime-local" name="end_at" required></label>
<label>Room / show details<textarea name="details" rows="3" maxlength="2000"></textarea></label><button>Send booking request</button></form></section>
<section id="schedule" class="panel"><h2>Upcoming schedule</h2><div class="tablewrap"><table><thead><tr><th>Date (UK)</th><th>Type</th><th>Title</th><th>Host</th><th>Status</th><th>Manage</th></tr></thead><tbody>
<?php foreach($bookings as $b):?><tr><td><?=h($b['start_at'])?> – <?=h(substr($b['end_at'],11,5))?></td><td><?=h($b['kind'])?></td><td><?=h($b['title'])?></td><td><?=h($b['username'])?></td><td><span class="tag"><?=h($b['status'])?></span></td><td><?php if($b['status']==='pending' && can_manage($b['kind'],$me['role'])): ?>
<form class="inline" action="booking.php" method="post"><input type="hidden" name="csrf" value="<?=h(csrf())?>"><input type="hidden" name="id" value="<?=(int)$b['id']?>"><button name="action" value="approve">Approve</button><button class="muted" name="action" value="reject">Reject</button></form>
<?php elseif(in_array($b['status'],['pending','approved'],true) && ((int)$b['host_id']===$me['id'] || can_manage($b['kind'],$me['role']))):?>
<form action="booking.php" method="post"><input type="hidden" name="csrf" value="<?=h(csrf())?>"><input type="hidden" name="id" value="<?=(int)$b['id']?>"><button class="muted" name="action" value="cancel">Cancel</button></form>
<?php endif;?></td></tr><?php endforeach;?></tbody></table></div></section></main></div></html>