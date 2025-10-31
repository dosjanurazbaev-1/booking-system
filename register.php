<?php
header('Content-Type: application/json; charset=utf-8');

$file = __DIR__ . 'users.csv';

if (!file_exists($file)) {
    file_put_contents($file, "email,password\n");
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(["success" => false, "message" => "Барлық өрістерді толтырыңыз."]);
    exit;
}

$users = array_map('str_getcsv', file($file));
foreach ($users as $user) {
    if ($user[0] === $email) {
        echo json_encode(["success" => false, "message" => "Бұл email тіркелген."]);
        exit;
    }
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$f = fopen($file, 'a');
fputcsv($f, [$email, $hash]);
fclose($f);

echo json_encode(["success" => true, "message" => "Тіркелу сәтті өтті."]);
?>
