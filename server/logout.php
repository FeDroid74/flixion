<?php
session_start();
session_destroy(); // Завершение сессии
header('Location: ../authorization.html'); // Перенаправление на страницу входа
exit();
?>