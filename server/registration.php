<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Параметры подключения к базе данных
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "flixion";

// Подключение к базе данных
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Проверка подключения
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Ошибка подключения к базе данных: ' . $conn->connect_error]));
}

// Функция для проверки reCAPTCHA
// function verifyRecaptcha($token) {
//     $secret_key = '6LeJTf0pAAAAAEOsg26ok_Lomg6hDTpNY3rxlKPb';
//     $url = 'https://www.google.com/recaptcha/api/siteverify';

//     $data = [
//         'secret' => $secret_key,
//         'response' => $token
//     ];

//     $options = [
//         'http' => [
//             'method' => 'POST',
//             'header' => 'Content-Type: application/x-www-form-urlencoded',
//             'content' => http_build_query($data)
//         ]
//     ];

//     $context = stream_context_create($options);
//     $response = file_get_contents($url, false, $context);
//     return json_decode($response, true);
// }

// Обработка POST-запроса
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nickname = $_POST['nickname'];
    $telnum = $_POST['telnum'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    // $recaptcha_token = $_POST['g-recaptcha-response'];

    // Проверка на пустые поля
    if (empty($nickname) || empty($telnum) || empty($email) || empty($password) || empty($confirm_password)) {
        echo json_encode(['success' => false, 'message' => 'Пожалуйста, заполните все поля']);
        exit();
    }

    // Проверка reCAPTCHA
    // $recaptcha_response = verifyRecaptcha($recaptcha_token);
    // if (!$recaptcha_response['success']) {
    //     echo json_encode(['success' => false, 'message' => 'Ошибка проверки reCAPTCHA']);
    //     exit();
    // }

    // Проверка допустимых символов для никнейма
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $nickname)) {
        echo json_encode(['success' => false, 'message' => 'Никнейм содержит недопустимые символы']);
        exit();
    }

    // Проверка уникальности номера телефона и почты
    $stmt = $conn->prepare("SELECT COUNT(*) FROM user WHERE telnum = ? OR email = ?");
    if (!$stmt) {
        error_log('Ошибка подготовки запроса: ' . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Ошибка подготовки запроса']);
        exit();
    }
    $stmt->bind_param("ss", $telnum, $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        echo json_encode(['success' => false, 'message' => 'Пользователь с таким номером телефона или почтой уже существует']);
        exit();
    }

    // Проверка совпадения паролей
    if ($password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'Пароли не совпадают']);
        exit();
    }

    // Подготовка и выполнение запроса
    $stmt = $conn->prepare("INSERT INTO user (nickname, telnum, email, password, role, registration_date, balance) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        error_log('Ошибка подготовки запроса: ' . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Ошибка подготовки запроса']);
        exit();
    }

    $default_role = 0; // Роль user по умолчанию
    $registration_date = date('Y-m-d'); // Текущая дата
    $balance = 0.00; // Начальный баланс

    $stmt->bind_param("ssssisd", $nickname, $telnum, $email, $password, $default_role, $registration_date, $balance);

    // Проверка выполнения запроса
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Регистрация прошла успешно']);
    } else {
        error_log('Ошибка выполнения запроса: ' . $stmt->error); // Логирование ошибки выполнения запроса
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $stmt->error]);
    }

    // Закрытие запроса
    $stmt->close();
}

// Закрытие соединения с базой данных
$conn->close();
?>
