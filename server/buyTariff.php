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
    echo json_encode(['success' => false, 'message' => 'Ошибка подключения к базе данных']);
    exit();
}

// Проверка, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Неавторизованный доступ']);
    exit();
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);
$tariff_id = $data['tariff_id'];

// Получение данных о тарифе
$sql_tariff = "SELECT price, duration, name_tariff FROM tariff WHERE id = ?";
$stmt_tariff = $conn->prepare($sql_tariff);
$stmt_tariff->bind_param('i', $tariff_id);
$stmt_tariff->execute();
$result_tariff = $stmt_tariff->get_result();

if ($result_tariff->num_rows > 0) {
    $tariff = $result_tariff->fetch_assoc();
    $price = $tariff['price'];
    $duration = $tariff['duration'];
    $tariff_name = $tariff['name_tariff'];

    // Проверка баланса пользователя
    $sql_balance = "SELECT balance FROM user WHERE id = ?";
    $stmt_balance = $conn->prepare($sql_balance);
    $stmt_balance->bind_param('i', $user_id);
    $stmt_balance->execute();
    $result_balance = $stmt_balance->get_result();

    if ($result_balance->num_rows > 0) {
        $user = $result_balance->fetch_assoc();
        if ($user['balance'] >= $price) {
            // Списание стоимости пакета с баланса пользователя
            $new_balance = $user['balance'] - $price;
            $sql_update_balance = "UPDATE user SET balance = ? WHERE id = ?";
            $stmt_update_balance = $conn->prepare($sql_update_balance);
            $stmt_update_balance->bind_param('di', $new_balance, $user_id);
            $stmt_update_balance->execute();

            // Обновление данных сессии
            $_SESSION['tariff_name'] = $tariff_name;
            $_SESSION['tariff_duration'] = $duration;

            echo json_encode(['success' => true, 'balance' => $new_balance, 'nickname' => $_SESSION['nickname'], 'tariff_name' => $tariff_name, 'duration' => $duration]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Недостаточно средств для покупки пакета']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Ошибка получения баланса']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Тариф не найден']);
}

$stmt_tariff->close();
$conn->close();
?>