<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Ошибка: Вы не авторизованы');
}

if (!isset($_FILES['video'])) {
    exit('Ошибка: Файл не передан');
}

$fileError = $_FILES['video']['error'];
if ($fileError !== UPLOAD_ERR_OK) {
    exit("Ошибка загрузки: Код $fileError");
}

$allowed_types = ['video/mp4', 'video/webm', 'video/ogg'];
$file_type = mime_content_type($_FILES['video']['tmp_name']);

if (!in_array($file_type, $allowed_types)) {
    exit('Ошибка: Неподдерживаемый формат файла');
}

$maxSize = 10600 * 1024 * 1024; // 10.6 ГБ
if ($_FILES['video']['size'] > $maxSize) {
    exit('Ошибка: Файл слишком большой');
}

// Проверяем, существует ли папка
$uploadDir = __DIR__ . "/storage/videofiles/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$filename = uniqid() . "." . pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION);
$uploadPath = $uploadDir . $filename;

if (!move_uploaded_file($_FILES['video']['tmp_name'], $uploadPath)) {
    exit('Ошибка: Не удалось сохранить файл');
}

// Добавляем запись в БД
$title = basename($_FILES['video']['name']);  // Можно изменить на более подходящее значение для title
$query = $pdo->prepare("INSERT INTO videos (user_id, title, video_path, upload_date) VALUES (?, ?, ?, NOW())");
if (!$query->execute([$_SESSION['user_id'], $title, $filename])) {
    exit('Ошибка записи в БД');
}

exit('Файл успешно загружен');
?>
