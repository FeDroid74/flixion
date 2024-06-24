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

// Получение ID обратной связи из запроса
$id_feedback = $_GET['id_feedback'];

// SQL запрос для удаления обратной связи
$sql = "DELETE FROM feedback WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_feedback);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Запись успешно удалена']);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка удаления записи: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>