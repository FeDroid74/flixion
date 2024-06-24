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

// Получение ID оборудования из GET параметра
$equipmentId = $_GET['id_equipment'];

// SQL-запрос для получения данных об оборудовании
$sql = "SELECT * FROM equipment WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $equipmentId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $equipment = $result->fetch_assoc();
    echo json_encode(['success' => true, 'equipment' => $equipment]);
} else {
    echo json_encode(['success' => false, 'message' => 'Оборудование не найдено']);
}

// Закрытие соединения
$stmt->close();
$conn->close();
?>
