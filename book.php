<?php
// Обработка формы бронирования с Tilda

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $seat = $_POST['seat'] ?? '';
    $date = date('Y-m-d H:i:s');

    if ($name === '' || $email === '' || $seat === '') {
        http_response_code(400);
        echo "Ошибка: не все поля заполнены.";
        exit;
    }

    $file = __DIR__ . '/bookings.csv';
    if (!file_exists($file)) {
        file_put_contents($file, "name,email,phone,seat,date\n");
    }

    $line = "$name,$email,$phone,$seat,$date\n";
    file_put_contents($file, $line, FILE_APPEND);

    echo "OK";
} else {
    http_response_code(405);
    echo "Метод не поддерживается";
}
?>
