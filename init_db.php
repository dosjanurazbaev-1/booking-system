<?php
$path = '/var/data/database.db';

try {
    $db = new PDO('sqlite:' . $path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Пайдаланушылар кестесі
    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // Мысалы, броньдар кестесі (егер қолданылса)
    $db->exec("
        CREATE TABLE IF NOT EXISTS bookings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            date TEXT,
            service TEXT,
            FOREIGN KEY (user_id) REFERENCES users(id)
        );
    ");

    echo '✅ Database initialized successfully at: ' . $path . PHP_EOL;
} catch (Exception $e) {
    echo '❌ Error: ' . $e->getMessage() . PHP_EOL;
}
?>
