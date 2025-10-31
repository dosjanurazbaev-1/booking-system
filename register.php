<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if (!file_exists("users.csv")) {
        file_put_contents("users.csv", "email,password\n");
    }

    // Проверяем, не зарегистрирован ли уже
    $users = array_map('str_getcsv', file('users.csv'));
    foreach ($users as $user) {
        if ($user[0] == $email) {
            die("Пользователь с таким email уже существует. <a href='login.php'>Войти</a>");
        }
    }

    $file = fopen("users.csv", "a");
    fputcsv($file, [$email, $password]);
    fclose($file);

    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Регистрация</title>
    <meta charset="utf-8">
</head>
<body>
    <h2>Регистрация</h2>
    <form method="POST">
        Email: <input type="email" name="email" required><br><br>
        Пароль: <input type="password" name="password" required><br><br>
        <button type="submit">Зарегистрироваться</button>
    </form>
    <p>Уже есть аккаунт? <a href="login.php">Войти</a></p>
</body>
</html>
