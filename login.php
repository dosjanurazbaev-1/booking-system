<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!file_exists("users.csv")) {
        die("Нет зарегистрированных пользователей. <a href='register.php'>Регистрация</a>");
    }

    $users = array_map('str_getcsv', file('users.csv'));
    foreach ($users as $user) {
        if ($user[0] == $email && password_verify($password, $user[1])) {
            $_SESSION['user'] = $email;
            header("Location: book.php");
            exit();
        }
    }

    echo "Неверный email или пароль!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Вход</title>
    <meta charset="utf-8">
</head>
<body>
    <h2>Авторизация</h2>
    <form method="POST">
        Email: <input type="email" name="email" required><br><br>
        Пароль: <input type="password" name="password" required><br><br>
        <button type="submit">Войти</button>
    </form>
    <p>Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
</body>
</html>

