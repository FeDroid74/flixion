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

// Получение данных о тарифе
$tariff_id = intval($_GET['id_tariff']);
$sql = "SELECT id, name_tariff, price, duration FROM tariff WHERE id = $tariff_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $tariff = $result->fetch_assoc();
    echo json_encode(['success' => true, 'tariff' => $tariff]);
} else {
    echo json_encode(['success' => false, 'message' => 'Тариф не найден']);
}

$conn->close();
?>