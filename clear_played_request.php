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

$nowPlaying = trim($_POST['song'] ?? '');

if ($nowPlaying === '' || !file_exists($file)) {
    echo json_encode(['success' => false, 'message' => 'Missing song or file']);
    exit;
}

$requests = json_decode(file_get_contents($file), true);
if (!is_array($requests)) $requests = [];

function clean_song($text) {
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    $text = strtolower($text);
    $text = preg_replace('/\([^)]*\)/', ' ', $text);
    $text = preg_replace('/\[[^\]]*\]/', ' ', $text);
    $text = preg_replace('/[^a-z0-9]+/', ' ', $text);
    $text = preg_replace('/\s+/', ' ', $text);
    return trim($text);
}

$current = clean_song($nowPlaying);
$removed = 0;

$requests = array_filter($requests, function($request) use ($current, &$removed) {
    $requested = clean_song($request['song'] ?? '');

    if ($requested === '') {
        return true;
    }

    if ($requested === $current || str_contains($current, $requested) || str_contains($requested, $current)) {
        $removed++;
        return false;
    }

    similar_text($requested, $current, $percent);

    if ($percent >= 82) {
        $removed++;
        return false;
    }

    return true;
});

file_put_contents($file, json_encode(array_values($requests), JSON_PRETTY_PRINT));

echo json_encode([
    'success' => true,
    'now_playing' => $nowPlaying,
    'clean_now_playing' => $current,
    'removed' => $removed
]);