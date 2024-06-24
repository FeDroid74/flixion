<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Проверка, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Неавторизованный доступ']);
    exit();
}

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

// Получение ID зала из DELETE-запроса
$id_room = isset($_GET['id_room']) ? intval($_GET['id_room']) : 0;

if ($id_room === 0) {
    echo json_encode(['success' => false, 'message' => 'Некорректный ID зала']);
    exit();
}

// Удаление зала
$sql = "DELETE FROM room WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param('i', $id_room);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Зал успешно удален']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ошибка при удалении зала: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка подготовки запроса: ' . $conn->error]);
}

$conn->close();
?>