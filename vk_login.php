<?php
$client_id = 52981560;
$redirect_uri = 'https://digitaltestitsvoysitekrytoy.ru/vk_callback.php';  
$scope = 'email';

$auth_url = "https://oauth.vk.com/authorize?client_id=$client_id&display=page&redirect_uri=$redirect_uri&scope=$scope&response_type=code&v=5.131";

header("Location: $auth_url");
exit;
?>