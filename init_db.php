<?php
try {
    $db = new PDO('sqlite:/var/www/html/data/database.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Таблица пользователей
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT UNIQUE,
        password TEXT
    )");

    // Таблица броней
    $db->exec("CREATE TABLE IF NOT EXISTS bookings (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        date TEXT,
        place TEXT,
        FOREIGN KEY(user_id) REFERENCES users(id)
    )");

    echo 'Database initialized successfully!';
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
