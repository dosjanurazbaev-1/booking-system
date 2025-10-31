<?php
header('Content-Type: application/json');

$file = __DIR__ . '/data/users.csv';
if (!file_exists($file)) {
    echo json_encode(['success' => false, 'message' => 'Пайдаланушылар базасы жоқ']);
    exit;
}

$post = json_decode(file_get_contents('php://input'), true);
$username = trim($post['username'] ?? '');
$password = trim($post['password'] ?? '');

$users = array_map('str_getcsv', file($file));

foreach ($users as $user) {
    if ($user[0] === $username && password_verify($password, $user[1])) {
        echo json_encode(['success' => true, 'message' => 'Кіру сәтті өтті']);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Қате логин немесе пароль']);
