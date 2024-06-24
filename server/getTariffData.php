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

// Запрос на получение данных тарифов
$sql = "SELECT id, name_tariff, price, duration FROM tariff ORDER BY id ASC";
$result = $conn->query($sql);

$tariffs = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tariffs[] = $row;
    }
}

// Формирование JSON-ответа
$response = ['success' => true, 'tariffs' => $tariffs];
echo json_encode($response);

// Закрытие соединения с базой данных
$conn->close();
?>