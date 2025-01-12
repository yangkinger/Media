<?php

$GLOBALS['hls'] = "/hls/";
$GLOBALS['hlspath'] = __DIR__ ."/hls/";
$GLOBALS['playurl'] = __DIR__ ."/playurl.txt";
// $GLOBALS['apihost'] = "http://38.145.218.246:8000";
$currentUrl = "http";
if ($_SERVER["HTTPS"] == "on") {
    $currentUrl .= "s";
}
$currentUrl .= "://".$_SERVER["HTTP_HOST"];
$GLOBALS['apihost'] = $currentUrl."/api.php";
$GLOBALS['host'] = $currentUrl;
$GLOBALS['playmodel'] = "hls"; #default hls, rtmp
$GLOBALS['ffmpeg-setting'] = __DIR__ ."/ffmpeg.json";
$GLOBALS['logs'] = __DIR__ ."/log.log";
$GLOBALS['channel_status'] = __DIR__ ."/tasks_status.json";
$GLOBALS['ffmpegpath'] = __DIR__  . '/ffmpeg';
    
    
$current_url = $_SERVER['REQUEST_URI'];



// 判断 URI 是否包含 'api'
if (strpos($current_url, 'api') !== false) {
} else {
session_start();
    // 检查用户是否已登录
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }
}


?>
