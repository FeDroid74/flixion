<?php
// Включение отображения ошибок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Установка заголовка для ответа в формате JSON
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

// SQL-запрос для получения всех данных об оборудовании
$sql = "SELECT * FROM equipment";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $equipment = [];
    while ($row = $result->fetch_assoc()) {
        $equipment[] = $row;
    }
    echo json_encode(['success' => true, 'equipment' => $equipment]);
} else {
    echo json_encode(['success' => false, 'message' => 'Данные об оборудовании не найдены']);
}

// Закрытие соединения
$conn->close();
?>