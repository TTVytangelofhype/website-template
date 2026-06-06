/*

* Designed and owned by TTVytangelofhype
* Copyright (c) 2026 TTVytangelofhype
*
* You may edit and use this code for personal/private use.
* You are not allowed to redistribute, re-upload, resell, or claim this code as your own.
*
* Full licence terms are available in the LICENSE file.
  */


<?php
header('Content-Type: application/json');

$file = __DIR__ . '/song_requests.json';

if (!file_exists($file)) {
    echo json_encode([]);
    exit;
}

echo file_get_contents($file);