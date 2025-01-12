<?php
include $_SERVER['DOCUMENT_ROOT'].'/action/function.php'; // 引入文件
// 添加频道




if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['channel_id'])) {
    // echo(json_encode($_POST));exit();
    $channel_name = $_POST['channel_name'];
    $channel_id = $_POST['channel_id'];
    $channel_url = $_POST['channel_url'];
    $rtmp_url = $_POST['rtmp_url'];
    $ffmpeg_config = $_POST['ffmpeg_config'];
    $file_path = $GLOBALS['playurl'];
    // 拼接频道信息
    $new_channel = "{$channel_name},{$channel_id},{$channel_url},{$rtmp_url},{$ffmpeg_config}\r\n";
    
    // 将新频道追加到文件
    file_put_contents($file_path, $new_channel, FILE_APPEND);
    // 重定向回页面
    echo json_encode(['status' => 'success', 'message' => '频道信息已添加']);
    exit();
}else{
    echo('非法提交');
}



?>
