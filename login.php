<?php
header('Content-Type: application/json; charset=utf-8');
$data = json_decode(file_get_contents("php://input"), true);

$username = trim($data["username"] ?? "");
$password = trim($data["password"] ?? "");

if (!file_exists("users.csv")) {
    echo json_encode(["success" => false, "message" => "Файл табылмады."]);
    exit;
}

$file = fopen("users.csv", "r");
$found = false;
while (($row = fgetcsv($file)) !== false) {
    if ($row[0] === $username && $row[1] === $password) {
        $found = true;
        break;
    }
}
fclose($file);

if ($found) {
    echo json_encode(["success" => true, "message" => "Кіру сәтті өтті"]);
} else {
    echo json_encode(["success" => false, "message" => "Қате логин немесе пароль"]);
}
?>
