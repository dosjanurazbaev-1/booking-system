<?php
header('Content-Type: application/json');

$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) mkdir($dataDir, 0777, true);

$file = $dataDir . '/users.csv';
if (!file_exists($file)) {
    $fp = fopen($file, 'w');
    fputcsv($fp, ['username', 'password']);
    fclose($fp);
}

$post = json_decode(file_get_contents('php://input'), true);
$username = trim($post['username'] ?? '');
$password = trim($post['password'] ?? '');

if (!$username || !$password) {
    echo json_encode(['success' => false, 'message' => 'Толық толтырыңыз']);
    exit;
}

$users = array_map('str_getcsv', file($file));
foreach ($users as $user) {
    if ($user[0] === $username) {
        echo json_encode(['success' => false, 'message' => 'Бұл логин бар']);
        exit;
    }
}

$fp = fopen($file, 'a');
fputcsv($fp, [$username, password_hash($password, PASSWORD_DEFAULT)]);
fclose($fp);

echo json_encode(['success' => true, 'message' => 'Тіркелу сәтті өтті']);
