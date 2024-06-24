<?php
// Включение отображения ошибок
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

// Получение данных из POST запроса
$data = json_decode(file_get_contents('php://input'), true);
$computerId = $data['id'];
$computerName = $data['name_computer'];
$roomId = $data['room_id'];
$status = $data['status'] === 'Включен' ? 1 : 0;
$book = $data['book'] === 'Занят' ? 1 : 0;

// SQL запрос для обновления данных компьютера
$sql = "UPDATE computer SET name_computer = ?, id_room = ?, status = ?, book = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("siiii", $computerName, $roomId, $status, $book, $computerId);

// Выполнение SQL запроса
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Компьютер успешно обновлен']);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка обновления компьютера']);
}

// Закрытие соединения
$stmt->close();
$conn->close();
?>