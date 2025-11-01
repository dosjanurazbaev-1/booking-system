<?php
header("Access-Control-Allow-Origin: https://jailasu.tilda.ws");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    $dbPath = $_ENV['DB_PATH'] ?? '/var/data/database.db';
    $dataDir = __DIR__ . '/data';
    $bookingsFile = __DIR__ . '/bookings.csv';

    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $data = json_decode(file_get_contents('php://input'), true);
    $email = trim($data['email'] ?? '');
    $category = trim($data['category'] ?? '');
    $place = trim($data['place'] ?? '');
    $date = trim($data['date'] ?? '');
    $time = trim($data['time'] ?? '');

    if (!$email || !$category || !$place || !$date || !$time) {
        echo json_encode(['success' => false, 'message' => 'Барлық өрістерді толтырыңыз.']);
        exit;
    }

    // Email бар ма, тексеру
    $stmt = $db->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Мұндай пайдаланушы тіркелмеген.']);
        exit;
    }

    // Категория CSV бар ма
    $csvPath = "$dataDir/$category.csv";
    if (!file_exists($csvPath)) {
        echo json_encode(['success' => false, 'message' => 'Категория табылмады.']);
        exit;
    }

    // Таңдалған орын CSV ішінде бар ма
    $found = false;
    if (($handle = fopen($csvPath, "r")) !== false) {
        $headers = fgetcsv($handle);
        while (($row = fgetcsv($handle)) !== false) {
            $placeData = array_combine($headers, $row);
            if (strcasecmp(trim($placeData['Наименование']), $place) === 0) {
                $found = true;
                break;
            }
        }
        fclose($handle);
    }

    if (!$found) {
        echo json_encode(['success' => false, 'message' => 'Мұндай орын бұл категорияда жоқ.']);
        exit;
    }

    // Bookings.csv файлына жазу
    if (!file_exists($bookingsFile)) {
        file_put_contents($bookingsFile, "email,category,place,date,time\n");
    }

    $f = fopen($bookingsFile, "a");
    fputcsv($f, [$email, $category, $place, $date, $time]);
    fclose($f);

    echo json_encode(['success' => true, 'message' => '✅ Брондау сәтті өтті!']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Қате: ' . $e->getMessage()]);
}
?>
