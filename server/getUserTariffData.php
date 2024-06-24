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

// Получение тарифного ID из запроса
$tariff_id = isset($_GET['id_tariff']) ? intval($_GET['id_tariff']) : 0;

// Проверка корректности получения ID
if ($tariff_id === 0) {
    echo json_encode(['success' => false, 'message' => 'Некорректный ID тарифа']);
    exit();
}

// Запрос на получение данных о выбранных тарифах пользователями
$sql = "SELECT u.nickname, t.name_tariff, ut.chosen_date
        FROM user_tariff ut
        JOIN user u ON ut.id_user = u.id
        JOIN tariff t ON ut.id_tariff = t.id
        WHERE ut.id_tariff = $tariff_id
        ORDER BY ut.chosen_date DESC";

$result = $conn->query($sql);

// Проверка выполнения запроса
if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Ошибка выполнения запроса: ' . $conn->error]);
    exit();
}

$userTariffs = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $userTariffs[] = $row;
    }
}

// Формирование JSON-ответа
$response = ['success' => true, 'userTariffs' => $userTariffs];
echo json_encode($response);

// Закрытие соединения с базой данных
$conn->close();
?>