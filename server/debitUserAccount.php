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
if (!isset($_SESSION['user_id']) || !isset($_SESSION['room_cost'])) {
    echo json_encode(['success' => false, 'message' => 'Неавторизованный доступ']);
    exit();
}

$user_id = $_SESSION['user_id'];
$cost_per_minute = $_SESSION['room_cost']; // Стоимость за минуту, зависит от выбранного зала

// Проверка на активированный тарифный пакет
if (isset($_SESSION['tariff_duration']) && isset($_SESSION['tariff_start_time'])) {
    $current_time = new DateTime();
    $start_time = new DateTime($_SESSION['tariff_start_time']);
    $duration = new DateInterval('PT' . $_SESSION['tariff_duration']);
    $end_time = (clone $start_time)->add($duration);

    if ($current_time < $end_time) {
        $remaining_time = $current_time->diff($end_time);
        echo json_encode(['success' => true, 'balance' => $remaining_time->format('%H:%I:%S'), 'nickname' => $_SESSION['nickname']]);
        exit();
    } else {
        // Тарифный пакет истек
        unset($_SESSION['tariff_duration']);
        unset($_SESSION['tariff_start_time']);
    }
}

// Проверка положительного баланса перед списанием
$sql_balance_check = "SELECT balance FROM user WHERE id = ?";
$stmt_balance_check = $conn->prepare($sql_balance_check);
$stmt_balance_check->bind_param('i', $user_id);
$stmt_balance_check->execute();
$result_balance_check = $stmt_balance_check->get_result();

if ($result_balance_check->num_rows > 0) {
    $user = $result_balance_check->fetch_assoc();
    if ($user['balance'] >= $cost_per_minute) {
        // Запрос на уменьшение баланса
        $sql = "UPDATE user SET balance = balance - ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo json_encode(['success' => false, 'message' => 'Ошибка подготовки запроса']);
            exit();
        }

        $stmt->bind_param('di', $cost_per_minute, $user_id);
        $stmt->execute();

        // Получение нового баланса пользователя
        $sql_balance = "SELECT balance FROM user WHERE id = ?";
        $stmt_balance = $conn->prepare($sql_balance);
        $stmt_balance->bind_param('i', $user_id);
        $stmt_balance->execute();
        $result_balance = $stmt_balance->get_result();

        if ($result_balance->num_rows > 0) {
            $user = $result_balance->fetch_assoc();
            echo json_encode(['success' => true, 'balance' => $user['balance'], 'nickname' => $_SESSION['nickname']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Ошибка получения баланса']);
        }

        $stmt_balance->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Недостаточно средств']);
    }
    $stmt_balance_check->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка получения баланса']);
}

$conn->close();
?>