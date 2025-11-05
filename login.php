<?php
header("Access-Control-Allow-Origin: https://jailasu.tilda.ws");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

try {
    // ✅ PostgreSQL қосылу (дұрыс форматта)
    $url = getenv('DATABASE_URL') ?: 'postgresql://ass_8eo7_user:SDOmtOY1fr5N0PkuPOBaayjzoQWq7P9B@dpg-d45hg2emcj7s739137f0-a/ass_8eo7';
    $url = parse_url($url);

    $host = $url['host'];
    $user = $url['user'];
    $pass = $url['pass'];
    $db   = ltrim($url['path'], '/');
    $port = $url['port'] ?? 5432;

    $dsn = "pgsql:host=$host;port=$port;dbname=$db;user=$user;password=$pass";
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ✅ Егер кестелер жоқ болса, бір рет жасаймыз
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            email TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL
        );

        CREATE TABLE IF NOT EXISTS bookings (
            id SERIAL PRIMARY KEY,
            user_email TEXT NOT NULL,
            service TEXT,
            date TEXT,
            time TEXT
        );
    ");

    // ✅ Пайдаланушыдан мәлімет оқу
    $data = json_decode(file_get_contents('php://input'), true);
    $email = trim($data['email'] ?? '');
    $service = trim($data['service'] ?? '');
    $date = trim($data['date'] ?? '');
    $time = trim($data['time'] ?? '');

    if (!$email || !$service || !$date || !$time) {
        echo json_encode(['success' => false, 'message' => 'Барлық өрістерді толтырыңыз.']);
        exit;
    }

    // ✅ Дубликат бронь тексеру
    $stmt = $pdo->prepare("SELECT id FROM bookings WHERE date = ? AND time = ?");
    $stmt->execute([$date, $time]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Бұл уақыт бос емес.']);
        exit;
    }

    // ✅ Жазу базада сақтау
    $stmt = $pdo->prepare("INSERT INTO bookings (user_email, service, date, time) VALUES (?, ?, ?, ?)");
    $stmt->execute([$email, $service, $date, $time]);

    echo json_encode(['success' => true, 'message' => 'Брондау сәтті өтті!']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Қате: ' . $e->getMessage()]);
}
?>
