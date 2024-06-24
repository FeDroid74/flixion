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
$userId = $data['id'];
$nickname = $data['nickname'];
$telnum = $data['telnum'];
$email = $data['email'];
$role = $data['role'];
$balance = $data['balance'];

// SQL запрос для обновления данных пользователя
$sql = "UPDATE user SET nickname = ?, telnum = ?, email = ?, role = ?, balance = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssidi", $nickname, $telnum, $email, $role, $balance, $userId);

// Выполнение SQL запроса и проверка результата
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Пользователь успешно обновлен']);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка обновления пользователя: ' . $stmt->error]);
}

// Закрытие соединения
$stmt->close();
$conn->close();
?>
