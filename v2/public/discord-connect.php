<?php
require __DIR__.'/../app/bootstrap.php';
$me=require_login();
$s=discord_settings($pdo);
if(!preg_match('/^[0-9]{15,24}$/',$s['discord_client_id'])||!filter_var($s['discord_redirect_uri'],FILTER_VALIDATE_URL)||parse_url($s['discord_redirect_uri'],PHP_URL_SCHEME)!=='https'){http_response_code(503);exit('Discord OAuth is not configured.');}
$state=bin2hex(random_bytes(24));$_SESSION['oauth_state']=$state;$_SESSION['oauth_created']=time();
$query=http_build_query(['client_id'=>$s['discord_client_id'],'redirect_uri'=>$s['discord_redirect_uri'],'response_type'=>'code','scope'=>'identify','state'=>$state]);
header('Location: https://discord.com/oauth2/authorize?'.$query);exit;
