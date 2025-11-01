<?php
// Рұқсат беру (CORS)
header("Access-Control-Allow-Origin: https://jailasu.tilda.ws");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");

// OPTIONS сұрауын өңдеу (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

// Пайдаланушылар базасы
$usersFile = __DIR__ . '/users.csv';

// Кіріс деректерін оқу
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Егер JSON бос болса — POST деректері арқылы алуға тырысамыз
if (!$data) {
    $data = $_POST;
}

$username = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');

// Егер өрістер бос болса
if ($username === '' || $password === '') {
    echo json_encode(['success' => false, 'message' => 'Барлық өрістерді толтырыңыз!']);
    exit;
}

// Файл бар ма?
if (!file_exists($usersFile)) {
    echo json_encode(['success' => false, 'message' => 'Пайдаланушылар базасы жоқ']);
    exit;
}

// Логинді тексеру
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
