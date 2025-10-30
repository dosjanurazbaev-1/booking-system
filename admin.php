<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$file = __DIR__ . '/bookings.csv';
$bookings = [];

if (file_exists($file)) {
    if (($handle = fopen($file, 'r')) !== false) {
        $headers = fgetcsv($handle);
        while (($row = fgetcsv($handle)) !== false) {
            $bookings[] = array_combine($headers, $row);
        }
        fclose($handle);
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Панель администратора</title>
<style>
body { font-family: Arial; margin:30px; background:#f9f9f9; }
table { border-collapse: collapse; width:100%; background:white; }
th, td { border:1px solid #ccc; padding:8px; }
th { background:#007bff; color:white; }
.logout { float:right; text-decoration:none; padding:5px 10px; background:red; color:white; border-radius:5px; }
</style>
</head>
<body>
<h2>Брони пользователей</h2>
<a href="logout.php" class="logout">Выйти</a>
<?php if (empty($bookings)): ?>
<p>Пока нет броней.</p>
<?php else: ?>
<table>
<tr>
<?php foreach ($headers as $h): ?><th><?= htmlspecialchars($h) ?></th><?php endforeach; ?>
</tr>
<?php foreach ($bookings as $b): ?>
<tr>
<?php foreach ($b as $v): ?><td><?= htmlspecialchars($v) ?></td><?php endforeach; ?>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>
</body>
</html>
