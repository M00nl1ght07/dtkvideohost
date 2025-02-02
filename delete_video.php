<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit(json_encode(['success' => false, 'message' => 'Ошибка: Вы не авторизованы']));
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['video_id']) || !isset($data['video_path'])) {
    exit(json_encode(['success' => false, 'message' => 'Ошибка: Неверные данные']));
}

$videoId = $data['video_id'];
$videoPath = $data['video_path'];

// Удаляем видео из базы данных
$query = $pdo->prepare("DELETE FROM videos WHERE video_id = ? AND user_id = ?");
if (!$query->execute([$videoId, $_SESSION['user_id']])) {
    exit(json_encode(['success' => false, 'message' => 'Ошибка: Не удалось удалить видео из БД']));
}

// Удаляем файл видео
$filePath = __DIR__ . "/storage/videofiles/" . basename($videoPath);
if (file_exists($filePath)) {
    if (!unlink($filePath)) {
        exit(json_encode(['success' => false, 'message' => 'Ошибка: Не удалось удалить файл видео']));
    }
}

echo json_encode(['success' => true, 'message' => 'Видео успешно удалено']);
?>