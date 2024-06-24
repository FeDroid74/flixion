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
$equipmentId = $data['id'];
$type = $data['type'];
$graphicsCard = $data['graphicscard'];
$cpu = $data['cpu'];
$ram = $data['ram'];
$motherboard = $data['motherboard'];
$monitor = $data['monitor'];
$keyboard = $data['keyboard'];
$mouse = $data['mouse'];

// SQL-запрос для обновления данных об оборудовании
$sql = "UPDATE equipment SET type = ?, graphicscard = ?, cpu = ?, ram = ?, motherboard = ?, monitor = ?, keyboard = ?, mouse = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssssi", $type, $graphicsCard, $cpu, $ram, $motherboard, $monitor, $keyboard, $mouse, $equipmentId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Оборудование успешно обновлено']);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка обновления оборудования: ' . $stmt->error]);
}

// Закрытие соединения
$stmt->close();
$conn->close();
?>
