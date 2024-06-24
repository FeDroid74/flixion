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

$feedbackId = $_GET['id_feedback'];
error_log('ID обратной связи: ' . $feedbackId); // Логирование ID обратной связи

// SQL запрос для получения деталей обратной связи
$sql = "SELECT f.id, u.nickname as username, f.issue_type, f.comment, f.submission_date
        FROM feedback f
        JOIN user u ON f.user_id = u.id
        WHERE f.id = ?";
error_log('SQL запрос: ' . $sql); // Логирование SQL запроса

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $feedbackId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $feedback = $result->fetch_assoc();
    echo json_encode(['success' => true, 'feedback' => $feedback]);
    error_log('Запись найдена: ' . json_encode($feedback)); // Логирование найденной записи
} else {
    echo json_encode(['success' => false, 'message' => 'Запись обратной связи не найдена']);
    error_log('Запись обратной связи не найдена для ID: ' . $feedbackId); // Логирование, если запись не найдена
}

$stmt->close();
$conn->close();
?>