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

// Получение стоимости и id зала из POST-запроса
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['cost']) || !isset($data['room_id'])) {
    echo json_encode(['success' => false, 'message' => 'Стоимость зала или id зала не указаны']);
    exit();
}

// Установка стоимости зала в сессии
$_SESSION['room_cost'] = $data['cost'];
$_SESSION['room_id'] = $data['room_id'];
echo json_encode(['success' => true, 'message' => 'Стоимость зала установлена']);
?>