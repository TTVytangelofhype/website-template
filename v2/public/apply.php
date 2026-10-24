<?php
require __DIR__.'/../app/bootstrap.php';
$error='';$sent=false;
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();
 $name=trim((string)($_POST['habbo_name']??''));
 $role=(string)($_POST['role_requested']??'');
 $experience=trim((string)($_POST['experience']??''));
 $availability=trim((string)($_POST['availability']??''));
 $motivation=trim((string)($_POST['motivation']??''));
 $honeypot=(string)($_POST['website']??'');
 if($honeypot!==''){$sent=true;}
 elseif(isset($_SESSION['last_application']) && time()-(int)$_SESSION['last_application']<3600){$error='Please wait before submitting another application.';}
 elseif(!preg_match('/^[A-Za-z0-9_.-]{2,40}$/',$name)||!in_array($role,['dj','event_host'],true)||mb_strlen($experience)<15||mb_strlen($experience)>2000||mb_strlen($availability)<3||mb_strlen($availability)>250||mb_strlen($motivation)<15||mb_strlen($motivation)>2000){$error='Please complete all fields within the stated limits.';}
 else{
  $s=$pdo->prepare("INSERT INTO staff_applications(habbo_name,role_requested,experience,availability,motivation) VALUES (?,?,?,?,?)");
  $s->execute([$name,$role,$experience,$availability,$motivation]);
  $_SESSION['last_application']=time();$sent=true;
 }
}
?><!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Join Our Hotel Team</title><link rel="stylesheet" href="style.css"><main class="login"><section class="panel installer"><a href="index.php">← Fansite home</a><div class="pixel">◆ HOTEL CAREERS</div><h1>Join our fansite team</h1><p>Apply to become a DJ or an event host. Applying does not create a staff account.</p><?php if($sent):?><p class="notice">Application submitted. Our managers will review it!</p><?php else:?><?php if($error):?><p class="error"><?=h($error)?></p><?php endif;?><form method="post"><input type="hidden" name="csrf" value="<?=h(csrf())?>"><div class="honeypot" aria-hidden="true"><label>Website<input name="website" tabindex="-1" autocomplete="off"></label></div><label>Habbo username<input name="habbo_name" minlength="2" maxlength="40" required></label><label>Position<select name="role_requested"><option value="dj">Radio DJ</option><option value="event_host">Event Host</option></select></label><label>What relevant experience do you have? (15–2000 characters)<textarea name="experience" required minlength="15" maxlength="2000" rows="4"></textarea></label><label>When are you available?<input name="availability" required minlength="3" maxlength="250" placeholder="Friday evenings, weekends"></label><label>Why would you like to join? (15–2000 characters)<textarea name="motivation" required minlength="15" maxlength="2000" rows="4"></textarea></label><button>Submit application</button></form><?php endif;?><small>Only share information you are comfortable providing. Do not send your Habbo password, email login or personal contact details.</small></section></main></html>