<?php
header('Content-Type: application/json; charset=utf-8');
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

// Пусть файлы хранятся в каталоге /var/www/html/data
$usersFile = __DIR__ . 'users.csv';

$data = json_decode(file_get_contents('php://input'), true);
$username = trim($data['username'] ?? '');
$password = trim($data['password'] ?? '');

// Проверка
if ($username === '' || $password === '') {
    echo json_encode(['success' => false, 'message' => 'Барлық өрістерді толтырыңыз.']);
    exit;
}

// Если файла нет — создаём и добавляем заголовок
if (!file_exists($usersFile)) {
    @mkdir(dirname($usersFile), 0777, true);
    file_put_contents($usersFile, "username,password\n");
}

// Проверяем, есть ли уже такой пользователь
$lines = file($usersFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    [$user, $pass] = str_getcsv($line);
    if ($user === $username) {
        echo json_encode(['success' => false, 'message' => 'Мұндай қолданушы бар.']);
        exit;
    }
}

// Хешируем пароль и сохраняем
$hash = password_hash($password, PASSWORD_BCRYPT);
file_put_contents($usersFile, "$username,$hash\n", FILE_APPEND);

echo json_encode(['success' => true, 'message' => 'Тіркелу сәтті өтті!']);
?>
