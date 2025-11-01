<?php
header("Access-Control-Allow-Origin: https://jailasu.tilda.ws");
header("Access-Control-Allow-Methods: GET, OPTIONS");
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

    $adminEmail = $_GET['email'] ?? '';

    if ($adminEmail !== 'admin@mail.com') {
        echo json_encode(['success' => false, 'message' => 'Рұқсат жоқ']);
        exit;
    }

    $stmt = $db->query("SELECT * FROM bookings ORDER BY date, time");
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $bookings]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Қате: ' . $e->getMessage()]);
}
?>
