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

// Получение идентификатора игры из запроса
$gameId = $_GET['id_game'];

// Подготовка и выполнение запроса на удаление данных
$sql = "DELETE FROM game WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $gameId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Игра успешно удалена']);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка удаления игры']);
}

$stmt->close();
$conn->close();
?>