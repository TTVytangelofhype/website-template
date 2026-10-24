<?php
require __DIR__.'/../app/bootstrap.php';
$me=require_login();
$s=discord_settings($pdo);
$state=(string)($_GET['state']??'');$code=(string)($_GET['code']??'');
if(!isset($_SESSION['oauth_state'],$_SESSION['oauth_created'])||!hash_equals($_SESSION['oauth_state'],$state)||time()-$_SESSION['oauth_created']>600||strlen($code)<5){http_response_code(403);exit('Invalid or expired Discord authorisation.');}
unset($_SESSION['oauth_state'],$_SESSION['oauth_created']);
if(!function_exists('curl_init')){http_response_code(503);exit('cURL is required.');}
$ch=curl_init('https://discord.com/api/v10/oauth2/token');
curl_setopt_array($ch,[CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>http_build_query(['client_id'=>$s['discord_client_id'],'client_secret'=>$s['discord_client_secret'],'grant_type'=>'authorization_code','code'=>$code,'redirect_uri'=>$s['discord_redirect_uri']]),CURLOPT_HTTPHEADER=>['Content-Type: application/x-www-form-urlencoded'],CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>8,CURLOPT_FOLLOWLOCATION=>false]);
$raw=curl_exec($ch);$status=curl_getinfo($ch,CURLINFO_HTTP_CODE);curl_close($ch);$token=is_string($raw)?json_decode($raw,true):null;
if($status!==200||!is_array($token)||empty($token['access_token'])){http_response_code(403);exit('Discord OAuth failed.');}
$ch=curl_init('https://discord.com/api/v10/users/@me');
curl_setopt_array($ch,[CURLOPT_HTTPHEADER=>['Authorization: Bearer '.$token['access_token']],CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>8,CURLOPT_FOLLOWLOCATION=>false]);
$raw=curl_exec($ch);$status=curl_getinfo($ch,CURLINFO_HTTP_CODE);curl_close($ch);$identity=is_string($raw)?json_decode($raw,true):null;
if($status!==200||!is_array($identity)||!preg_match('/^[0-9]{15,24}$/',(string)($identity['id']??''))){http_response_code(403);exit('Discord identity verification failed.');}
$discordId=(string)$identity['id'];
if(!discord_authorised($pdo,$config,$discordId)){http_response_code(403);exit('The required Discord server role was not found.');}
try{
 $update=$pdo->prepare('UPDATE users SET discord_id=? WHERE id=?');$update->execute([$discordId,$me['id']]);
 $_SESSION['discord_verified_at']=time();
 audit($pdo,(int)$me['id'],'discord.linked',['discord_id'=>$discordId]);
 header('Location: dashboard.php');exit;
}catch(PDOException $e){http_response_code(409);exit('This Discord account is already linked to another fansite account.');}
