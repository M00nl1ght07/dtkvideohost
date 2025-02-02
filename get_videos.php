<?php
require 'config.php';

$query = $pdo->query("SELECT video_id, video_path, upload_date, title FROM videos ORDER BY upload_date DESC");
$videos = $query->fetchAll(PDO::FETCH_ASSOC);

$base_url = "https://digitaltestitsvoysitekrytoy.ru/storage/videofiles/";

foreach ($videos as &$video) {
    $video['video_path'] = $base_url . $video['video_path'];
}

header('Content-Type: application/json');
echo json_encode($videos);
?>