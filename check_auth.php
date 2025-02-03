<?php
require_once __DIR__ . '/config.php'; // Сессия уже стартована

if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("Location: /?error=" . urlencode("Для доступа к этой странице необходима авторизация. Пожалуйста, войдите в систему."));
    exit();
}

// Проверка таймаута сессии (30 минут)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    header("Location: /?error=" . urlencode("Сессия истекла. Пожалуйста, войдите снова."));
    exit();
}
$_SESSION['last_activity'] = time();
?>