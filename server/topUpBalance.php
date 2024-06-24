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

// Получение данных из POST-запроса
$data = json_decode(file_get_contents('php://input'), true);
$telnum = $data['telnum'];
$balance = floatval($data['balance']);

// Обновление баланса пользователя
$sql = "UPDATE user SET balance = balance + ? WHERE telnum = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ds", $balance, $telnum);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка при обновлении баланса: ' . $stmt->error]);
}

// Закрытие соединения с базой данных
$stmt->close();
$conn->close();
?>