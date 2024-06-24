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

// Запрос на получение данных обратной связи
$sql = "SELECT f.id, f.comment, f.submission_date, f.issue_type, u.nickname as username
        FROM feedback f
        JOIN user u ON f.user_id = u.id
        ORDER BY f.submission_date DESC";
$result = $conn->query($sql);

$feedback = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $feedback[] = $row;
    }
}

// Формирование JSON-ответа
$response = ['success' => true, 'feedback' => $feedback];
echo json_encode($response);

// Закрытие соединения с базой данных
$conn->close();
?>
