<?php
// Убедитесь что нет пробелов/переносов до этого тега
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$servername = "31.31.196.166";
$username = "u2566469_dtkvide";
$password = "dtkvideO123";
$dbname = "u2566469_dtkvideo";

// MySQLi подключение
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// PDO подключение
try {
    $pdo = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Ошибка подключения PDO: " . $e->getMessage());
}
?>