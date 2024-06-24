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
    echo json_encode(['success' => false, 'message' => 'Ошибка подключения к базе данных']);
    exit();
}

// Проверка, установлена ли роль в сессии
if (!isset($_SESSION['role'])) {
    echo json_encode(['success' => false, 'message' => 'Неавторизованный доступ: роль не установлена']);
    exit();
}

// Получение данных о пользователе для личного кабинета
if ($_SESSION['role'] == 0 && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT nickname, balance, registration_date FROM user WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Ошибка подготовки запроса']);
        exit();
    }
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'user' => $user
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Пользователь не найден']);
    }
    $stmt->close();
}

// Получение данных о всех пользователях для админ-панели
else if ($_SESSION['role'] == 1) {
    $sql = "SELECT id, nickname, telnum, email, role, balance, registration_date FROM user";
    $result = $conn->query($sql);

    $users = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['role'] = $row['role'] == 1 ? 'Администратор' : 'Клиент';
            $users[] = $row;
        }
    }

    echo json_encode([
        'success' => true,
        'nickname' => $_SESSION['nickname'],
        'users' => $users
    ]);
}

// Неавторизованный доступ
else {
    echo json_encode(['success' => false, 'message' => 'Неавторизованный доступ']);
}

$conn->close();
?>