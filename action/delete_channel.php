<?php
include $_SERVER['DOCUMENT_ROOT'].'/init.php'; // 引入文件
// delete_channel.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['channel_id'])) {
    $channel_id = $_POST['channel_id'];
    $file_path = $GLOBALS['playurl'];

    // 检查文件是否存在
    if (!file_exists($file_path)) {
        echo json_encode(['status' => 'error', 'message' => '文件不存在']);
        exit();
    }

    // 读取文件内容
    $channels = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $new_channels = [];

    // 遍历文件内容，删除匹配的频道
    foreach ($channels as $line) {
        list($name, $id, $url) = explode(',', $line);
        if (trim($id) !== $channel_id) {
            $new_channels[] = $line; // 将非删除的频道保留下来
        }
    }

    // 将新的频道列表写回文件
    file_put_contents($file_path, implode("\r\n", $new_channels));

    echo json_encode(['status' => 'success', 'message' => '频道删除成功']);
} else {
    echo json_encode(['status' => 'error', 'message' => '无效请求']);
}
?>
