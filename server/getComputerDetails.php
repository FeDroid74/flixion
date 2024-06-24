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

// Получение ID компьютера из GET параметра
$computerId = $_GET['id_computer'];

// SQL запрос для получения данных о компьютере
$sql = "SELECT computer.id, computer.name_computer, room.name_room, computer.status, computer.book 
        FROM computer 
        JOIN room ON computer.id_room = room.id 
        WHERE computer.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $computerId);

// Выполнение SQL запроса
if ($stmt->execute()) {
    $result = $stmt->get_result();
    // Проверка наличия результата
    if ($result->num_rows > 0) {
        $computer = $result->fetch_assoc();
        // Преобразование значений статуса и занятости в читаемый формат
        $computer['status'] = $computer['status'] == 1 ? 'Включен' : 'Выключен';
        $computer['book'] = $computer['book'] == 1 ? 'Занят' : 'Свободен';
        echo json_encode(['success' => true, 'computer' => $computer]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Компьютер не найден']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка выполнения запроса']);
}

// Закрытие соединения
$stmt->close();
$conn->close();
?>
