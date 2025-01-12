<?php
include $_SERVER['DOCUMENT_ROOT'].'/init.php'; // 引入文件
// 省略已有的功能函数...
// 添加一个更新频道的函数
function updateChannel($channel_id, $new_name, $new_url, $rtmp_url,$file_path,$ffmpeg_config) {
    // 读取文件内容
    $channels = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $updated_channels = [];

    // 遍历所有频道，找到需要更新的频道
    foreach ($channels as $line) {
        list($name, $id, $url) = explode(',', $line);
        if (trim($id) === $channel_id) {
            $updated_channels[] = "{$new_name},{$channel_id},{$new_url},{$rtmp_url},{$ffmpeg_config}";
        } else {
            $updated_channels[] = $line;
        }
    }

    // 将更新后的内容写回文件
    file_put_contents($file_path, implode("\r\n", $updated_channels) . "\r\n");
}

// 检查是否为编辑请求
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editChannelId'])) {
    $channel_id = $_POST['editChannelId'];
    $channel_name = $_POST['editChannelName'];
    $channel_url = $_POST['editChannelUrl'];
    $rtmp_url = $_POST['editrtmpUrl'];
    $ffmpeg_config = $_POST['editffmpeg_config'];
    $file_path = $GLOBALS['playurl'];
    // 更新频道信息
    updateChannel($channel_id, $channel_name, $channel_url,$rtmp_url, $file_path,$ffmpeg_config);

    // 返回 JSON 响应
    echo json_encode(['status' => 'success', 'message' => '频道信息已更新']);
    exit();
}
?>
