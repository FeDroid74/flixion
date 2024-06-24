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

// Получение ID пользователя из GET параметра
$userId = $_GET['id'];

// SQL запрос для получения данных о пользователе
$sql = "SELECT id, nickname, telnum, email, role, balance FROM user WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Ошибка подготовки запроса: ' . $conn->error]);
    exit();
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Проверка наличия результата
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode(['success' => true, 'user' => $user]);
    error_log('Запись найдена: ' . json_encode($user)); // Логирование найденной записи
} else {
    echo json_encode(['success' => false, 'message' => 'Пользователь не найден']);
    error_log('Пользователь не найден для ID: ' . $userId); // Логирование, если запись не найдена
}

// Закрытие соединения
$stmt->close();
$conn->close();
?>