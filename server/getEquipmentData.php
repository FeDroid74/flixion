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
    error_log("Ошибка подключения к базе данных: " . $conn->connect_error);
    echo json_encode(['success' => false, 'message' => 'Ошибка подключения к базе данных: ' . $conn->connect_error]);
    exit();
}

// Получение ID компьютера из GET-запроса
$computer_id = isset($_GET['computer_id']) ? intval($_GET['computer_id']) : 0;

if ($computer_id === 0) {
    error_log("Некорректный ID компьютера");
    echo json_encode(['success' => false, 'message' => 'Некорректный ID компьютера']);
    exit();
}

// Запрос на получение данных об оборудовании
$sql = "SELECT e.* FROM equipment e JOIN computer c ON e.id = c.id_equipment WHERE c.id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log("Ошибка подготовки запроса: " . $conn->error);
    echo json_encode(['success' => false, 'message' => 'Ошибка подготовки запроса: ' . $conn->error]);
    exit();
}

$stmt->bind_param('i', $computer_id);
$stmt->execute();
$result = $stmt->get_result();

// Формирование JSON-ответа
if ($result->num_rows > 0) {
    $equipment = $result->fetch_assoc();
    echo json_encode([
        'success' => true,
        'equipment' => $equipment
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Оборудование не найдено'
    ]);
}

$stmt->close();
$conn->close();
?>