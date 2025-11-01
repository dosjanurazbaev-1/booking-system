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
    $db = new PDO('sqlite:/var/www/html/data/database.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $data = $_POST ?: json_decode(file_get_contents('php://input'), true);
    $email = trim($data['email'] ?? '');
    $password = trim($data['password'] ?? '');

    if ($email === '' || $password === '') {
        echo json_encode(['success' => false, 'message' => 'Барлық өрістерді толтырыңыз!']);
        exit;
    }

    // Проверяем, есть ли уже такой email
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Бұл email тіркелген.']);
        exit;
    }

    // Регистрируем
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $stmt->execute([$email, $hash]);

    echo json_encode(['success' => true, 'message' => 'Тіркеу сәтті өтті!']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Қате: ' . $e->getMessage()]);
}
?>
