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

// SQL-запрос для удаления оборудования
$sql = "DELETE FROM equipment WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $equipmentId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Оборудование успешно удалено']);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка удаления оборудования: ' . $stmt->error]);
}

// Закрытие соединения
$stmt->close();
$conn->close();
?>
