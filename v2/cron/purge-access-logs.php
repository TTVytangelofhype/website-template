<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(403);exit;}
require dirname(__DIR__).'/app/bootstrap.php';
$pdo->exec('DELETE FROM panel_access_denials WHERE occurred_at < NOW() - INTERVAL 30 DAY');
