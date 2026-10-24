<?php
declare(strict_types=1);
function discord_authorised(PDO $pdo,array $config,string $discordId):bool {
 $s=site_settings($pdo,['discord_guild_id'=>'','discord_role_id'=>'','discord_bot_token'=>'']);
 foreach(['discord_guild_id','discord_role_id'] as $key){if(!preg_match('/^[0-9]{15,24}$/',$s[$key]))return false;}
 if(empty($s['discord_bot_token'])||!function_exists('curl_init'))return false;
 $url='https://discord.com/api/v10/guilds/'.$s['discord_guild_id'].'/members/'.$discordId;
 $ch=curl_init($url);
 curl_setopt_array($ch,[CURLOPT_HTTPHEADER=>['Authorization: Bot '.$s['discord_bot_token'],'Accept: application/json'],CURLOPT_RETURNTRANSFER=>true,CURLOPT_CONNECTTIMEOUT=>3,CURLOPT_TIMEOUT=>6,CURLOPT_FOLLOWLOCATION=>false]);
 $raw=curl_exec($ch);$status=curl_getinfo($ch,CURLINFO_HTTP_CODE);curl_close($ch);
 if($status!==200||!is_string($raw))return false;
 $member=json_decode($raw,true);
 return is_array($member)&&in_array($s['discord_role_id'],$member['roles']??[],true);
}
function discord_enabled(PDO $pdo):bool {
 $s=site_settings($pdo,['discord_access_enabled'=>'0']);return $s['discord_access_enabled']==='1';
}
function discord_settings(PDO $pdo):array {
 return site_settings($pdo,['discord_client_id'=>'','discord_client_secret'=>'','discord_redirect_uri'=>'','discord_bot_token'=>'','discord_guild_id'=>'','discord_role_id'=>'','discord_access_enabled'=>'0']);
}
