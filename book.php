<?php
header('Content-Type: application/json');

$dataDir = __DIR__ . '/data';
$file = $dataDir . '/bookings.csv';
if (!file_exists($file)) {
    $fp = fopen($file, 'w');
    fputcsv($fp, ['username', 'date', 'place']);
    fclose($fp);
}

$post = json_decode(file_get_contents('php://input'), true);
$username = trim($post['username'] ?? '');
$date = trim($post['date'] ?? '');
$place = trim($post['place'] ?? '');

if (!$username || !$date || !$place) {
    echo json_encode(['success' => false, 'message' => 'Барлық өрісті толтырыңыз']);
    exit;
}

$fp = fopen($file, 'a');
fputcsv($fp, [$username, $date, $place]);
fclose($fp);

echo json_encode(['success' => true, 'message' => 'Бронь сәтті жасалды']);
