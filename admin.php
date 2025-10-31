<?php
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
