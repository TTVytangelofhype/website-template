<?php
header('Content-Type: application/json');

$file = __DIR__ . '/song_requests.json';

$name = trim($_POST['name'] ?? '');
$song = trim($_POST['song'] ?? '');

if ($name === '' || $song === '') {
    echo json_encode(['success' => false]);
    exit;
}

$requests = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
if (!is_array($requests)) $requests = [];

array_unshift($requests, [
    'id' => uniqid('req_', true),
    'name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
    'song' => htmlspecialchars($song, ENT_QUOTES, 'UTF-8'),
    'status' => 'waiting',
    'time' => date('H:i d/m/Y')
]);

$requests = array_slice($requests, 0, 40);

file_put_contents($file, json_encode($requests, JSON_PRETTY_PRINT));

echo json_encode(['success' => true]);