<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "flixion";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Ошибка подключения к базе данных: ' . $conn->connect_error]));
}

session_start();
$user_id = $_SESSION['user_id']; // Предполагаем, что идентификатор пользователя хранится в сессии

$data = json_decode(file_get_contents('php://input'), true);

$issue_type = $data['issue_type'];
$comment = $data['comment'];

if (empty($issue_type) || empty($comment)) {
    echo json_encode(['success' => false, 'message' => 'Заполните все поля']);
    exit();
}

$stmt = $conn->prepare("INSERT INTO feedback (user_id, issue_type, comment) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $user_id, $issue_type, $comment);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Ваше сообщение отправлено.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>