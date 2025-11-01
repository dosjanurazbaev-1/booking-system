<?php
try {
    $db = new PDO('sqlite:' . (getenv('DB_PATH') ?: '/var/data/database.db'));
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // users кестесі
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT UNIQUE,
        password TEXT
    )");

    // bookings кестесі
    $db->exec("CREATE TABLE IF NOT EXISTS bookings (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_email TEXT,
        service TEXT,
        date TEXT,
        time TEXT
    )");

    echo "Database initialized successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
