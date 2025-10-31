<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

$file = __DIR__ . 'users.csv';
if (!file_exists($file)) {
    echo json_encode(["success" => false, "message" => "Пайдаланушылар базасы жоқ"]);
    exit;
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(["success" => false, "message" => "Email мен пароль енгізіңіз."]);
    exit;
}

$users = array_map('str_getcsv', file($file));
foreach ($users as $user) {
    if ($user[0] === $email && password_verify($password, $user[1])) {
        $_SESSION['user'] = $email;
        echo json_encode(["success" => true, "message" => "Кіру сәтті өтті."]);
        exit;
    }
}

echo json_encode(["success" => false, "message" => "Қате логин немесе пароль"]);
?>
