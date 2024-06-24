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
    echo json_encode(['success' => false, 'message' => 'Ошибка подключения к базе данных: ' . $conn->connect_error]);
    exit();
}

// Получение данных из запроса
$data = json_decode(file_get_contents('php://input'), true);
$tariffName = $data['name_tariff'];
$tariffPrice = $data['price'];
$tariffDuration = $data['duration'];

// Подготовка и выполнение запроса на вставку данных
$sql = "INSERT INTO tariff (name_tariff, price, duration) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sdi", $tariffName, $tariffPrice, $tariffDuration);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Тариф успешно добавлен']);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка добавления тарифа']);
}

$stmt->close();
$conn->close();
?>
