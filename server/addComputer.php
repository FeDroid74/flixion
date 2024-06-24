<?php
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

// Получение данных из POST запроса
$data = json_decode(file_get_contents("php://input"), true);

$name_computer = $data['name_computer'];
$id_room = $data['id_room'];
$status = $data['status'];
$book = $data['book'];

// SQL запрос для добавления нового компьютера
$sql = "INSERT INTO computers (name_computer, id_room, status, book) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("siss", $name_computer, $id_room, $status, $book);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Компьютер успешно добавлен']);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка добавления компьютера: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
