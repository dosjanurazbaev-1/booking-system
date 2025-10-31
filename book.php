<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$dataDir = "data";
$categories = glob("$dataDir/*.csv");

// Если пользователь выбрал категорию
if (isset($_POST['category'])) {
    $selectedCategory = $_POST['category'];
    $filePath = "$dataDir/$selectedCategory.csv";
    $places = [];

    if (file_exists($filePath)) {
        if (($handle = fopen($filePath, "r")) !== false) {
            $headers = fgetcsv($handle); // читаем заголовки
            while (($row = fgetcsv($handle)) !== false) {
                $places[] = array_combine($headers, $row);
            }
            fclose($handle);
        }
    }
}

// Если отправлена бронь
if (isset($_POST['place_name']) && isset($_POST['date']) && isset($_POST['time'])) {
    $booking = [
        $user,
        $_POST['category'],
        $_POST['place_name'],
        $_POST['date'],
        $_POST['time']
    ];

    if (!file_exists("bookings.csv")) {
        file_put_contents("bookings.csv", "email,category,place,date,time\n");
    }

    $file = fopen("bookings.csv", "a");
    fputcsv($file, $booking);
    fclose($file);

    echo "<p style='color:green;'>✅ Бронь успешно создана!</p>";
}
?>

<h2>Здравствуйте, <?= htmlspecialchars($user) ?>!</h2>
<a href="logout.php">Выйти</a>
<hr>

<form method="POST">
  <label><b>Выберите категорию:</b></label><br>
  <select name="category" required onchange="this.form.submit()">
    <option value="">-- выберите --</option>
    <?php
    foreach ($categories as $path) {
        $name = basename($path, ".csv");
        $selected = (isset($selectedCategory) && $selectedCategory === $name) ? "selected" : "";
        echo "<option value='$name' $selected>$name</option>";
    }
    ?>
  </select>
</form>

<?php if (!empty($places)): ?>
  <hr>
  <form method="POST">
    <input type="hidden" name="category" value="<?= htmlspecialchars($selectedCategory) ?>">

    <label><b>Выберите место:</b></label><br>
    <select name="place_name" required>
      <?php foreach ($places as $p): ?>
        <option value="<?= htmlspecialchars($p['Наименование']) ?>">
          <?= htmlspecialchars($p['Наименование']) ?> — <?= htmlspecialchars($p['Адрес']) ?> (⭐ <?= htmlspecialchars($p['Рейтинг']) ?>)
        </option>
      <?php endforeach; ?>
    </select><br><br>

    <label>Дата:</label><br>
    <input type="date" name="date" required><br>

    <label>Время:</label><br>
    <input type="time" name="time" required><br><br>

    <button type="submit">Забронировать</button>
  </form>
<?php endif; ?>
