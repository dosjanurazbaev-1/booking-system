<?php
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents("php://input"), true);
$username = trim($data["username"] ?? "");
$password = trim($data["password"] ?? "");

if ($username === "" || $password === "") {
    echo json_encode(["success" => false, "message" => "Барлық өрістерді толтырыңыз."]);
    exit;
}

$file = fopen("users.csv", "a+");
$exists = false;

rewind($file);
while (($row = fgetcsv($file)) !== false) {
    if ($row[0] === $username) {
        $exists = true;
        break;
    }
}

if ($exists) {
    echo json_encode(["success" => false, "message" => "Бұл логин бұрын тіркелген."]);
} else {
    fputcsv($file, [$username, $password]);
    echo json_encode(["success" => true, "message" => "Тіркелу сәтті өтті!"]);
}
fclose($file);
?>
