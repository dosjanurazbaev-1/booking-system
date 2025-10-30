<?php
session_start();
if (isset($_SESSION['user'])) {
    header("Location: admin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $file = __DIR__ . '/users.csv';
    $found = false;

    if (file_exists($file)) {
        if (($handle = fopen($file, 'r')) !== false) {
            fgetcsv($handle); // пропускаем заголовок
            while (($data = fgetcsv($handle)) !== false) {
                if ($data[0] === $email && $data[1] === $password) {
                    $found = true;
                    break;
                }
            }
            fclose($handle);
        }
    }

    if ($found) {
        $_SESSION['user'] = $email;
        header("Location: admin.php");
        exit;
    } else {
        $error = "Неверный логин или пароль";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Авторизация</title>
<style>
body { font-family: Arial; background:#f0f0f0; display:flex; align-items:center; justify-content:center; height:100vh; }
form { background:#fff; padding:30px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.1); width:300px; }
input { width:100%; margin:10px 0; padding:10px; border:1px solid #ccc; border-radius:5px; }
button { width:100%; padding:10px; background:#007bff; color:white; border:none; border-radius:5px; }
</style>
</head>
<body>
<form method="POST">
  <h3>Вход администратора</h3>
  <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
  <input type="email" name="email" placeholder="Email" required>
  <input type="password" name="password" placeholder="Пароль" required>
  <button type="submit">Войти</button>
</form>
</body>
</html>
