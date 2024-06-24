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

// Получение компьютерного ID из запроса
$computer_id = isset($_GET['id_computer']) ? intval($_GET['id_computer']) : 0;

// Запрос на получение данных о выборе компьютеров пользователями
$sql = "SELECT u.nickname, c.name_computer, uc.start_time, uc.end_time
        FROM user_computer uc
        JOIN user u ON uc.id_user = u.id
        JOIN computer c ON uc.id_computer = c.id
        WHERE uc.id_computer = ?
        ORDER BY uc.start_time DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $computer_id);
$stmt->execute();
$result = $stmt->get_result();

$userComputers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $userComputers[] = $row;
    }
}

// Формирование JSON-ответа
$response = ['success' => true, 'userComputers' => $userComputers];
echo json_encode($response);

// Закрытие соединения с базой данных
$stmt->close();
$conn->close();
?>