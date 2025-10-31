<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (!file_exists("users.csv")) {
        echo json_encode(["success" => false, "message" => "Файл табылмады"]);
        exit;
    }

    $file = fopen("users.csv", "r");
    $found = false;

    while (($data = fgetcsv($file)) !== FALSE) {
        if ($data[0] === $username && $data[1] === $password) {
            $_SESSION["user"] = $username;
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
} else {
    echo json_encode(["success" => false, "message" => "Қате орын алды."]);
}
?>
