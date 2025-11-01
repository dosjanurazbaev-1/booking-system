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
    $db = new PDO('sqlite:' . ($_ENV['DB_PATH'] ?? '/var/data/database.db'));
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $data = json_decode(file_get_contents('php://input'), true);
    $email = trim($data['email'] ?? '');
    $service = trim($data['service'] ?? '');
    $category = trim($data['category'] ?? '');
    $date = trim($data['date'] ?? '');
    $time = trim($data['time'] ?? '');

    if (!$email || !$service || !$category || !$date || !$time) {
        echo json_encode(['success' => false, 'message' => 'Барлық өрістерді толтырыңыз.']);
        exit;
    }

    // Email тексеру
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Бұл email тіркелмеген.']);
        exit;
    }

    // CSV файлын ашу
    $filePath = "/var/data/" . $category . ".csv";
    if (!file_exists($filePath)) {
        echo json_encode(['success' => false, 'message' => 'Категория табылмады.']);
        exit;
    }

    // CSV файлына жаңа бронь қосу
    $f = fopen($filePath, 'a');
    if ($f === false) {
        throw new Exception("Файлды ашу мүмкін емес: $filePath");
    }

    $now = date('Y-m-d H:i:s');
    fputcsv($f, [$email, $service, $date, $time, $now], ',');
    fclose($f);

    echo json_encode(['success' => true, 'message' => 'Брондау сәтті өтті!']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Қате: ' . $e->getMessage()]);
}
?>
