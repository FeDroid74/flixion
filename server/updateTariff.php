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

// Получение данных из запроса
$data = json_decode(file_get_contents('php://input'), true);
$tariff_id = intval($data['id']);
$name_tariff = $conn->real_escape_string($data['name_tariff']);
$price = floatval($data['price']);
$duration = $conn->real_escape_string($data['duration']);

// Обновление данных тарифа
$sql = "UPDATE tariff SET name_tariff='$name_tariff', price=$price, duration='$duration' WHERE id=$tariff_id";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'Тариф успешно обновлен']);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка обновления тарифа: ' . $conn->error]);
}

$conn->close();
?>