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
    // 🔹 Путь к постоянной базе (Render дискіне)
    $dbPath = getenv('DB_PATH') ?: '/var/data/database.db';
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            echo json_encode(['success' => false, 'message' => 'Барлық өрістерді толтырыңыз.']);
            exit;
        }

        // 🔹 Іздейміз қолданушыны
        $stmt = $db->prepare("SELECT password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            echo json_encode(['success' => true, 'message' => 'Кіру сәтті өтті!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Қате email немесе құпиясөз.']);
        }
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Қате: ' . $e->getMessage()]);
}
?>
