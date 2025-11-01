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
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

echo "<h2>Все бронирования</h2>";

if (file_exists("bookings.csv")) {
    $rows = array_map('str_getcsv', file('bookings.csv'));
    echo "<table border='1' cellpadding='5'><tr><th>Пользователь</th><th>Место</th><th>Дата</th></tr>";
    foreach ($rows as $row) {
        echo "<tr><td>{$row[0]}</td><td>{$row[1]}</td><td>{$row[2]}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>Нет бронирований.</p>";
}
?>

