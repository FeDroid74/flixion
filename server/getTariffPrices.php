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
    echo json_encode(['success' => false, 'message' => 'Ошибка подключения к базе данных']);
    exit();
}

// Получение id зала из POST-запроса
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['room_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID зала не указан']);
    exit();
}

$room_id = $data['room_id'];

// Получение тарифов для данного зала
$sql_tariffs = "SELECT name_tariff, price FROM tariff WHERE id_room = ?";
$stmt_tariffs = $conn->prepare($sql_tariffs);
$stmt_tariffs->bind_param('i', $room_id);
$stmt_tariffs->execute();
$result_tariffs = $stmt_tariffs->get_result();

$tariffs = [];
while ($row = $result_tariffs->fetch_assoc()) {
    $tariffs[] = $row;
}

echo json_encode(['success' => true, 'tariffs' => $tariffs]);

$stmt_tariffs->close();
$conn->close();
?>