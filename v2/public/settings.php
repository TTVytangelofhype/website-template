<?php
require __DIR__.'/../app/bootstrap.php';
$me=require_login();
if($me['role']!=='owner'){http_response_code(403);exit('Only the owner can edit fansite settings.');}
$defaults=[
 'site_name'=>(string)($config['site']['name']??'Hotel Wave'),
 'site_tagline'=>(string)($config['site']['tagline']??'Your Habbo community. Your music. Your events.'),
 'radio_url'=>(string)($config['site']['stream_url']??''),
 'theme'=>'hotel',
 'logo_url'=>'',
 'discord_enabled'=>'0',
 'discord_webhook'=>''
];
$fields=array_keys($defaults);
$values=site_settings($pdo,$defaults);
$error='';$success=false;
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();
 $name=trim((string)($_POST['site_name']??''));
 $tagline=trim((string)($_POST['site_tagline']??''));
 $radio=trim((string)($_POST['radio_url']??''));
 $logo=trim((string)($_POST['logo_url']??''));
 $theme=(string)($_POST['theme']??'hotel');
 $discordEnabled=isset($_POST['discord_enabled'])?'1':'0';
 $discordUrl=trim((string)($_POST['discord_webhook']??''));
 $discordHost=parse_url($discordUrl,PHP_URL_HOST);
 $discordValid=$discordUrl==='' || (filter_var($discordUrl,FILTER_VALIDATE_URL) && parse_url($discordUrl,PHP_URL_SCHEME)==='https' && in_array($discordHost,['discord.com','discordapp.com'],true) && str_starts_with((string)parse_url($discordUrl,PHP_URL_PATH),'/api/webhooks/'));
 if(mb_strlen($name)<3||mb_strlen($name)>70){$error='The fansite name must contain 3–70 characters.';}
 elseif(mb_strlen($tagline)>170){$error='The tagline must be under 171 characters.';}
 elseif(!$discordValid || ($discordEnabled==='1' && $discordUrl==='')){$error='Enter a valid Discord HTTPS webhook URL before enabling notifications.';}
 elseif(!in_array($theme,['hotel','neon','midnight'],true)){$error='Please select a valid theme.';}
 elseif(($radio!==''&&(!filter_var($radio,FILTER_VALIDATE_URL)||parse_url($radio,PHP_URL_SCHEME)!=='https'))||($logo!==''&&(!filter_var($logo,FILTER_VALIDATE_URL)||parse_url($logo,PHP_URL_SCHEME)!=='https'))){$error='Radio and logo URLs must be valid HTTPS addresses.';}
 else{
  $values=['site_name'=>$name,'site_tagline'=>$tagline,'radio_url'=>$radio,'logo_url'=>$logo,'theme'=>$theme,'discord_enabled'=>$discordEnabled,'discord_webhook'=>$discordUrl];
  try{
   $pdo->beginTransaction();
   $s=$pdo->prepare('INSERT INTO site_settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)');
   foreach($values as $key=>$value)$s->execute([$key,$value]);
   audit($pdo,(int)$me['id'],'site.settings.updated',['keys'=>$fields]);
   $pdo->commit();$success=true;
  }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();error_log($e->getMessage());$error='Could not save settings. Confirm migration 003_settings.sql has run.';}
 }
}
?><!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Fansite Settings</title><link rel="stylesheet" href="style.css">
<div class="layout"><aside><div class="pixel">◆ FANSITE V2</div><h2>Owner controls</h2><nav><a href="dashboard.php">← Staff dashboard</a><a href="index.php">⌂ Public fansite</a></nav><p>Signed in as <?=h($me['username'])?></p></aside>
<main><header><span class="tag">Site administration</span><h1>Fansite Settings</h1><p>Customise your community without editing PHP files. Changes appear on the public homepage.</p></header>
<section class="panel"><h2>Branding and radio</h2><?php if($success):?><p class="notice">Settings saved successfully.</p><?php endif;?><?php if($error):?><p class="error"><?=h($error)?></p><?php endif;?>
<form method="post"><input type="hidden" name="csrf" value="<?=h(csrf())?>">
<label>Fansite name<input name="site_name" required maxlength="70" minlength="3" value="<?=h($values['site_name'])?>"></label>
<label>Fansite tagline<input name="site_tagline" maxlength="170" value="<?=h($values['site_tagline'])?>"></label>
<label>Logo image URL (HTTPS, optional)<input type="url" name="logo_url" placeholder="https://example.com/logo.png" value="<?=h($values['logo_url'])?>"></label>
<label>Radio stream URL (HTTPS, optional)<input type="url" name="radio_url" placeholder="https://example.com/stream" value="<?=h($values['radio_url'])?>"></label>
<label>Colour theme<select name="theme"><option value="hotel" <?=$values['theme']==='hotel'?'selected':''?>>Classic Hotel (gold and blue)</option><option value="neon" <?=$values['theme']==='neon'?'selected':''?>>Neon Radio (pink and cyan)</option><option value="midnight" <?=$values['theme']==='midnight'?'selected':''?>>Midnight (violet and blue)</option></select></label>
<h2>Discord notifications (optional)</h2><p>Only scheduled shift reminders use the webhook. The webhook is a secret; protect access to your database and owner account.</p><label>Discord webhook URL<input name="discord_webhook" type="password" autocomplete="off" value="<?=h($values['discord_webhook'])?>" placeholder="https://discord.com/api/webhooks/..."></label><label><input type="checkbox" name="discord_enabled" value="1" <?=$values['discord_enabled']==='1'?'checked':''?>> Enable Discord shift reminders</label><button type="submit">Save fansite settings</button></form><p class="soft-note">Images must be hosted over HTTPS. SVG logos and remote images should come from sources you trust.</p></section>
</main></div></html>