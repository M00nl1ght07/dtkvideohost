<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];

    // Объединенный запрос
    $stmt = $conn->prepare("
    (SELECT u.user_id, u.username, u.password_hash, u.role, 'permanent' as type 
        FROM users u 
        WHERE u.username = ?)
        UNION
        (SELECT t.user_id, t.login as username, t.password_hash, u.role, 'temporary' as type 
        FROM temporary_logins t 
        JOIN users u ON t.user_id = u.user_id 
        WHERE t.login = ? AND t.is_used = 0 AND t.expiration_time > NOW())
    ");
    
    $stmt->bind_param("ss", $login, $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $hashed_input = hash('sha256', $password);
        
        if (hash_equals($user['password_hash'], $hashed_input)) {
            // Помечаем временный логин как использованный
            if ($user['type'] === 'temporary') {
                $update = $conn->prepare("UPDATE temporary_logins SET is_used = 1 WHERE login = ?");
                $update->bind_param("s", $login);
                $update->execute();
            }

            // Устанавливаем сессию
            $_SESSION['user_id'] = $user['user_id']; // Добавьте эту строку
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            echo json_encode(['success' => true, 'redirect' => 'videoload.php']);
            exit;
        }
    }

    echo json_encode(['success' => false, 'message' => 'Неверный логин или пароль']);
    exit;
}
?>

