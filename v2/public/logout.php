<?php
require __DIR__.'/../app/bootstrap.php';
if ($_SERVER['REQUEST_METHOD']!=='POST') { http_response_code(405);exit; }
verify_csrf(); $pdo->prepare('UPDATE staff_sessions SET revoked_at=NOW() WHERE session_hash=?')->execute([hash('sha256',session_id())]); $_SESSION=[]; session_regenerate_id(true); session_destroy(); header('Location: login.php');
