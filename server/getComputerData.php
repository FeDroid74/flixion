<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Проверка, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Неавторизованный доступ']);
    exit();
}

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

// Запрос на получение данных о компьютерах вместе с комнатами
$sql = "SELECT c.id, c.name_computer, c.status, c.book, r.id as room_id, r.name_room, r.cost 
        FROM computer c 
        JOIN room r ON c.id_room = r.id";
$result = $conn->query($sql);

$computers = [];
$occupied = 0;
$available = 0;
$powered_on = 0;
$powered_off = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Преобразование значений статуса и брони
        $row['status'] = $row['status'] == 0 ? 'Выключен' : 'Включен';
        $row['book'] = $row['book'] == 0 ? 'Свободен' : 'Занят';

        $computers[] = $row; // Добавление данных о компьютере в массив

        // Подсчет количества занятых и свободных компьютеров
        if ($row['book'] == 'Занят') {
            $occupied++;
        } else {
            $available++;
        }

        // Подсчет количества включенных и выключенных компьютеров
        if ($row['status'] == 'Включен') {
            $powered_on++;
        } else {
            $powered_off++;
        }
    }
}

// Формирование JSON-ответа
echo json_encode([
    'success' => true,
    'data' => $computers,
    'occupied' => $occupied,
    'available' => $available,
    'powered_on' => $powered_on,
    'powered_off' => $powered_off
]);

// Закрытие соединения с базой данных
$conn->close();
?>