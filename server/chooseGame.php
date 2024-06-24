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

// Проверка, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Неавторизованный доступ']);
    exit();
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);
$game_id = $data['game_id'];
$action = $data['action'];

if ($action === 'start') {
    // Запуск игры
    $sql = "UPDATE game SET times_ordered = times_ordered + 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Ошибка подготовки запроса: ' . $conn->error]);
        exit();
    }
    $stmt->bind_param('i', $game_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Игра запущена']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ошибка запуска игры']);
    }
    $stmt->close();
} elseif ($action === 'exit') {
    // Выход из игры
    echo json_encode(['success' => true, 'message' => 'Игра закрыта']);
} else {
    echo json_encode(['success' => false, 'message' => 'Некорректное действие']);
}

$conn->close();
?>