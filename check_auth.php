<?php
require_once __DIR__ . '/config.php'; // Сессия уже стартована

if (!isset($_SESSION['username'])) {
    header("Location: /?error=" . urlencode("Требуется авторизация!"));
    exit();
}
?>