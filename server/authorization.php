<?php
session_start();
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
    echo json_encode(['success' => false, 'message' => 'Ошибка подключения к базе данных: ' . $conn->connect_error]);
    exit();
}

// Функция для проверки reCAPTCHA
function verifyRecaptcha($token) {
    $secret_key = '6LeJTf0pAAAAAEOsg26ok_Lomg6hDTpNY3rxlKPb';
    $url = 'https://www.google.com/recaptcha/api/siteverify';

    $data = [
        'secret' => $secret_key,
        'response' => $token
    ];

    $options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query($data)
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    return json_decode($response, true);
}

// Обработка POST-запроса
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $telnum = $_POST['telnum'];
    $password = $_POST['password'];
    $recaptcha_token = $_POST['g-recaptcha-response'];

    // Проверка на пустые поля
    if (empty($telnum) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Пожалуйста, заполните все поля']);
        exit();
    }

    // Проверка reCAPTCHA
    $recaptcha_response = verifyRecaptcha($recaptcha_token);
    if (!$recaptcha_response['success']) {
        echo json_encode(['success' => false, 'message' => 'Ошибка проверки reCAPTCHA']);
        exit();
    }

    // Подготовка и выполнение запроса
    $stmt = $conn->prepare("SELECT id, password, role, nickname FROM user WHERE telnum = ?");
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Выполнение запроса прервано: ' . $conn->error]);
        exit();
    }
    $stmt->bind_param("s", $telnum);
    $stmt->execute();
    $stmt->store_result();

    // Проверка наличия пользователя
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $stored_password, $role, $nickname);
        $stmt->fetch();

        // Проверка пароля
        if ($password === $stored_password) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['role'] = $role;
            $_SESSION['nickname'] = $nickname;
            echo json_encode(['success' => true, 'message' => 'Вы успешно авторизированы', 'role' => $role, 'user_id' => $user_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Вы ввели неверный пароль']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Пользователь с таким номером телефона не найден']);
    }

    // Закрытие запроса
    $stmt->close();
}

// Закрытие соединения с базой данных
$conn->close();
?>