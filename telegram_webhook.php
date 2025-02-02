<?php
session_start();
include('config.php');

$bot_token = "8044433503:AAGtKDWP4s3_ZApzzfZJ65Lr-FIBGZ1aQkU";
$webhook_url = "https://digitaltestitsvoysitekrytoy.ru/telegram_webhook.php";

// Установка webhook (лучше вызывать отдельно)
file_get_contents("https://api.telegram.org/bot$bot_token/setWebhook?url=$webhook_url");

$update = json_decode(file_get_contents("php://input"), true);
$user_id = $update['message']['from']['id'] ?? null;
$message_text = $update['message']['text'] ?? '';

// Обрабатываем только команду /start
if (strpos($message_text, '/start authorize') === 0) {
    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Поиск пользователя
        $stmt = $pdo->prepare("
            SELECT u.user_id 
            FROM users u 
            JOIN social_accounts sa ON u.user_id = sa.user_id 
            WHERE sa.platform = 'telegram' AND sa.social_id = ?
        ");
        $stmt->execute([$user_id]);
        $existing_user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing_user) {
            // Генерация временных данных
            $temporary_login = bin2hex(random_bytes(8));
            $temporary_password = bin2hex(random_bytes(4));
            $expiration_time = date('Y-m-d H:i:s', time() + 600);

            // Сохранение в БД
            $stmt = $pdo->prepare("
                INSERT INTO temporary_logins 
                (user_id, login, password_hash, expiration_time)
                VALUES (?, ?, ?, ?)
            ");
            $hashed_password = hash('sha256', $temporary_password);
            $stmt->execute([
                $existing_user['user_id'],
                $temporary_login,
                $hashed_password,
                $expiration_time
            ]);

            // Отправка данных пользователю
            $login_url = "https://digitaltestitsvoysitekrytoy.ru/"; // Замените на свой URL
            $message = "Ваши временные данные:\nЛогин: $temporary_login\nПароль: $temporary_password\n\nПерейдите на сайт: $login_url";
            file_get_contents("https://api.telegram.org/bot$bot_token/sendMessage?chat_id=$user_id&text=" . urlencode($message));
            
        } else {
            file_get_contents("https://api.telegram.org/bot$bot_token/sendMessage?chat_id=$user_id&text=Ошибка: Пользователь не найден!");
        }
    } catch (PDOException $e) {
        file_get_contents("https://api.telegram.org/bot$bot_token/sendMessage?chat_id=$user_id&text=Ошибка сервера");
    }
}
?>