<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

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

$roomId = $_GET['id_room'];

// SQL запрос для получения деталей зала
$sql = "SELECT id, name_room, cost FROM room WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $roomId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $room = $result->fetch_assoc();
    echo json_encode(['success' => true, 'room' => $room]);
} else {
    echo json_encode(['success' => false, 'message' => 'Зал не найден']);
}

$stmt->close();
$conn->close();
?>