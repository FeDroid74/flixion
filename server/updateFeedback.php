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

// Получение данных из POST-запроса
$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'];
$username = $data['username'];
$issue_type = $data['issue_type'];
$comment = $data['comment'];
$submission_date = $data['submission_date'];

// SQL запрос для обновления обратной связи
$sql = "UPDATE feedback SET username = ?, issue_type = ?, comment = ?, submission_date = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $username, $issue_type, $comment, $submission_date, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Запись успешно обновлена']);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка обновления записи: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>