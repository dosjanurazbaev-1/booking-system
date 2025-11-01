<?php
// Разрешаем запросы с Tilda
header("Access-Control-Allow-Origin: https://jailasu.tilda.ws");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");

// Обрабатываем preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

header('Content-Type: application/json; charset=utf-8');

$usersFile = __DIR__ . 'users.csv';
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


