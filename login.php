<?php
header('Content-Type: application/json; charset=utf-8');

$usersFile = __DIR__ . '/data/users.csv';
$data = json_decode(file_get_contents('php://input'), true);
$username = trim($data['username'] ?? '');
$password = trim($data['password'] ?? '');

if (!file_exists($usersFile)) {
    echo json_encode(['success' => false, 'message' => 'Пайдаланушылар базасы жоқ']);
    exit;
}

$lines = file($usersFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    [$user, $hash] = str_getcsv($line);
    if ($user === $username && password_verify($password, $hash)) {
        echo json_encode(['success' => true, 'message' => 'Кіру сәтті өтті!']);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Қате логин немесе пароль']);
?>
