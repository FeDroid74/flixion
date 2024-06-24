<?php
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

// Получение ID игры из GET параметра
$gameId = $_GET['id_game'];

// SQL запрос для получения данных об игре
$sql = "SELECT * FROM game WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $gameId);
$stmt->execute();
$result = $stmt->get_result();

// Проверка наличия результата
if ($result->num_rows > 0) {
    $game = $result->fetch_assoc();
    echo json_encode(['success' => true, 'game' => $game]);
} else {
    echo json_encode(['success' => false, 'message' => 'Игра не найдена']);
}

// Закрытие соединения
$stmt->close();
$conn->close();
?>
