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
    $db = new PDO('sqlite:' . (getenv('DB_PATH') ?: '/var/data/database.db'));
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $data = json_decode(file_get_contents('php://input'), true);
    $email = trim($data['email'] ?? '');
    $service = trim($data['service'] ?? '');
    $date = trim($data['date'] ?? '');
    $time = trim($data['time'] ?? '');

    if (!$email || !$service || !$date || !$time) {
        echo json_encode(['success' => false, 'message' => 'Барлық өрістерді толтырыңыз.']);
        exit;
    }

    // Дубликат броньды тексеру
    $stmt = $db->prepare("SELECT id FROM bookings WHERE date = ? AND time = ?");
    $stmt->execute([$date, $time]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Бұл уақыт бос емес.']);
        exit;
    }

    $stmt = $db->prepare("INSERT INTO bookings (user_email, service, date, time) VALUES (?, ?, ?, ?)");
    $stmt->execute([$email, $service, $date, $time]);

    echo json_encode(['success' => true, 'message' => 'Брондау сәтті өтті!']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Қате: ' . $e->getMessage()]);
}
?>
