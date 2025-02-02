<?php
require __DIR__ . '/config.php';

$client_id = 52981560;
$client_secret = 'ERLiJgF7vpXqr3XGDZeW';
$redirect_uri = 'https://digitaltestitsvoysitekrytoy.ru/vk_callback.php';

// Обработка ошибки отсутствия кода
if (!isset($_GET['code'])) {
    header("Location: /?error=" . urlencode("Код авторизации не получен"));
    exit;
}

// Получаем токен
$token_url = "https://oauth.vk.com/access_token?"
    . "client_id=$client_id"
    . "&client_secret=$client_secret"
    . "&redirect_uri=$redirect_uri"
    . "&code=" . $_GET['code'];

$response = file_get_contents($token_url);
$data = json_decode($response, true);

// Проверка токена
if (!isset($data['access_token'])) {
    $error = $data['error_description'] ?? "Неизвестная ошибка";
    header("Location: /?error=" . urlencode("Ошибка VK: $error"));
    exit;
}

// Получаем данные пользователя
$user_info_url = "https://api.vk.com/method/users.get"
    . "?user_ids=" . $data['user_id']
    . "&access_token=" . $data['access_token']
    . "&v=5.131"
    . "&fields=photo_200";

$user_info = json_decode(file_get_contents($user_info_url), true);

// Проверка данных пользователя
if (empty($user_info['response'][0])) {
    header("Location: /?error=" . urlencode("Ошибка получения данных пользователя"));
    exit;
}

try {
    // Поиск пользователя в базе
    $stmt = $pdo->prepare("
        SELECT u.user_id, u.username, u.role 
        FROM users u 
        JOIN social_accounts sa ON u.user_id = sa.user_id 
        WHERE sa.platform = 'vk' 
        AND sa.social_id = :social_id
    ");
    
    $stmt->execute([':social_id' => $data['user_id']]);
    $user = $stmt->fetch();

    if ($user) {
        // Устанавливаем сессию
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        header('Location: /videoload.php');
        exit;
    }

    // Если пользователь не найден
    header("Location: /?error=" . urlencode("Аккаунт не привязан к системе"));
    exit;

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    header("Location: /?error=" . urlencode("Ошибка системы"));
    exit;
}
?>