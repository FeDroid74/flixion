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

// Удаление тарифа
$tariff_id = intval($_GET['id_tariff']);
$sql = "DELETE FROM tariff WHERE id = $tariff_id";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'Тариф успешно удален']);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка удаления тарифа: ' . $conn->error]);
}

$conn->close();
?>