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

// Получение ID пользователя из GET параметра
$userId = $_GET['id_user'];

// SQL запрос для удаления пользователя
$sql = "DELETE FROM user WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);

// Выполнение SQL запроса и проверка результата
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Пользователь успешно удален']);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка удаления пользователя']);
}

// Закрытие соединения
$stmt->close();
$conn->close();
?>
