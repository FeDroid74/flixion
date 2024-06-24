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

// Получение данных из POST-запроса
$data = json_decode(file_get_contents('php://input'), true);
$type = $data['type'];
$graphicsCard = $data['graphicscard'];
$cpu = $data['cpu'];
$ram = $data['ram'];
$motherboard = $data['motherboard'];
$monitor = $data['monitor'];
$keyboard = $data['keyboard'];
$mouse = $data['mouse'];

// SQL-запрос для добавления нового оборудования
$sql = "INSERT INTO equipment (type, graphicscard, cpu, ram, motherboard, monitor, keyboard, mouse) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssss", $type, $graphicsCard, $cpu, $ram, $motherboard, $monitor, $keyboard, $mouse);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Оборудование успешно добавлено']);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка добавления оборудования: ' . $stmt->error]);
}

// Закрытие соединения
$stmt->close();
$conn->close();
?>
