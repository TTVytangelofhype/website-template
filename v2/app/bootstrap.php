<?php
declare(strict_types=1);
ini_set('display_errors', '0');
date_default_timezone_set('Europe/London');
session_set_cookie_params(['httponly'=>true,'secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off','samesite'=>'Lax']);
session_start();
$config = require __DIR__ . '/../config.php';
$pdo = new PDO('mysql:host='.$config['db_host'].';dbname='.$config['db_name'].';charset=utf8mb4', $config['db_user'], $config['db_pass'], [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES=>false]);
function h(?string $v):string { return htmlspecialchars($v ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function csrf():string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(32)); }
function verify_csrf():void { if (!hash_equals(csrf(),(string)($_POST['csrf']??''))) { http_response_code(403); exit('Invalid CSRF token'); } }
function user():?array { return $_SESSION['user'] ?? null; }
function require_login():array { if (!user()) { header('Location: login.php'); exit; } return user(); }
function can_manage(string $kind, string $role):bool {
 return in_array($role,['owner','admin'],true) || ($kind==='dj' && $role==='radio_manager') || ($kind==='event' && $role==='events_manager');
}
function can_book(string $kind,string $role):bool { return in_array($role,['owner','admin'],true) || ($kind==='dj' && in_array($role,['radio_manager','dj'],true)) || ($kind==='event' && in_array($role,['events_manager','event_host'],true)); }
function audit(PDO $db,int $id,string $action,array $context=[]):void { $s=$db->prepare('INSERT INTO audit_logs (actor_id,action,context_json) VALUES (?,?,?)'); $s->execute([$id,$action,json_encode($context)]); }
