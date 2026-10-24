<?php
declare(strict_types=1);
function deny_panel_access(PDO $db, ?int $userId, string $reason): never {
 try {
  $stmt=$db->prepare('INSERT INTO panel_access_denials (user_id,ip_address,user_agent,reason,requested_path) VALUES (?,?,?,?,?)');
  $stmt->execute([$userId,substr((string)($_SERVER['REMOTE_ADDR']??'unknown'),0,45),mb_substr((string)($_SERVER['HTTP_USER_AGENT']??''),0,255),substr($reason,0,40),mb_substr((string)($_SERVER['SCRIPT_NAME']??''),0,160)]);
 }catch(Throwable $e){error_log('Access denial log failed: '.$e->getMessage());}
 header('Location: access-denied.php',true,303);exit;
}
