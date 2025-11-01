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

session_start();
session_destroy();
header("Location: login.php");
exit;
?>

