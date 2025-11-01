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
    // SQLite базаға қосылу
    $db = new PDO('sqlite:' . (getenv('DB_PATH') ?: '/var/data/database.db'));
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // JSON деректерді оқу
    $data = json_decode(file_get_contents('php://input'), true);
    $email = trim($data['email'] ?? '');
    $service = trim($data['service'] ?? '');
    $date = trim($data['date'] ?? '');
    $time = trim($data['time'] ?? '');

    if (!$email || !$service || !$date || !$time) {
        echo json_encode(['success' => false, 'message' => 'Барлық өрістерді толтырыңыз.']);
        exit;
    }

    // Email тіркелген бе?
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Бұл email тіркелмеген.']);
        exit;
    }

    // CSV файлына жол
    $csvDir = '/var/data';
    $csvFile = $csvDir . '/' . strtolower(preg_replace('/[^a-zA-Z0-9_-]/', '', $service)) . '.csv';

    // Қалта бар-жоғын тексеру
    if (!file_exists($csvDir)) {
        mkdir($csvDir, 0777, true);
    }

    // CSV файлын ашу (жоқ болса – жасау)
    $isNew = !file_exists($csvFile);
    $fp = fopen($csvFile, 'a');
    if (!$fp) {
        echo json_encode(['success' => false, 'message' => 'CSV файлын ашу мүмкін емес.']);
        exit;
    }

    // Егер жаңа файл болса – баған атауларын жазу
    if ($isNew) {
        fputcsv($fp, ['Email', 'Service', 'Date', 'Time']);
    }

    // Дубликат броньды тексеру (CSV ішінен)
    $existing = false;
    if (($handle = fopen($csvFile, 'r')) !== false) {
        while (($row = fgetcsv($handle)) !== false) {
            if ($row[2] === $date && $row[3] === $time) {
                $existing = true;
                break;
            }
        }
        fclose($handle);
    }

    if ($existing) {
        echo json_encode(['success' => false, 'message' => 'Бұл уақыт бос емес.']);
        fclose($fp);
        exit;
    }

    // Жаңа жазба енгізу
    fputcsv($fp, [$email, $service, $date, $time]);
    fclose($fp);

    echo json_encode(['success' => true, 'message' => 'Брондау сәтті өтті!']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Қате: ' . $e->getMessage()]);
}
?>
