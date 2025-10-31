<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_SESSION['user'];
    $place = $_POST['place'];
    $date = $_POST['date'];

    if (!file_exists("bookings.csv")) {
        file_put_contents("bookings.csv", "user,place,date\n");
    }

    $file = fopen("bookings.csv", "a");
    fputcsv($file, [$user, $place, $date]);
    fclose($file);

    echo "<p>✅ Место успешно забронировано!</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Бронирование мест</title>
    <meta charset="utf-8">
</head>
<body>
    <h2>Бронирование мест</h2>
    <p>Вы вошли как: <b><?= $_SESSION['user'] ?></b> | <a href="logout.php">Выйти</a></p>

    <form method="POST">
        Место: <input type="text" name="place" required><br><br>
        Дата: <input type="date" name="date" required><br><br>
        <button type="submit">Забронировать</button>
    </form>
</body>
</html>
