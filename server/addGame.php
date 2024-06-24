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

// Получение данных из запроса
$data = json_decode(file_get_contents('php://input'), true);
$gameName = $data['name_game'];
$gameDescription = $data['description'];
$gameDeveloper = $data['developer'];
$gamePublisher = $data['publisher'];

// Подготовка и выполнение запроса на вставку данных
$sql = "INSERT INTO game (name_game, description, developer, publisher) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $gameName, $gameDescription, $gameDeveloper, $gamePublisher);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Игра успешно добавлена']);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка добавления игры']);
}

$stmt->close();
$conn->close();
?>