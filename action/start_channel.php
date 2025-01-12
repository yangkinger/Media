<?php
header('Content-Type: application/json');
include $_SERVER['DOCUMENT_ROOT'].'/init.php'; // 引入文件
$response = [
    "status" => "error",
    "message" => "未知错误"
];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $channel_id = $_POST['channel_id'] ?? '';

    if ($channel_id) {
        // 示例：假设你有一个映射来获取频道的源地址和输出路径
        // $channel_data = getChannelData($channel_id); // 获取频道的数据，包含流地址和输出目录
        $channel_data = [];
        $channel_data['url'] = $_POST['playurl'] ?? '';
        $channel_data['rtmpurl'] = $_POST['rtmpurl'] ?? '';
        $channel_data['ffmpeg'] = $_POST['ffmpeg'] ?? '';
        $channel_data['ffmpeg_name'] = $_POST['ffmpeg_name'] ?? '';
        
        
        $channel_data['output_dir'] = $GLOBALS['hlspath'];

        if (empty($channel_data['url'])) {
            $response['message'] = "未找到该频道的播放地址！";
            echo json_encode($response);
            exit;
        }

        // 调用 FastAPI 接口启动频道
        $api_url = $GLOBALS['apihost'];

        
        
        $post_data = json_encode([
            "input_url" => $channel_data['url'],  // 从频道数据获取流地址
            "output_directory" => $channel_data['output_dir'],  // 从频道数据获取输出目录
            "channel_id" => $channel_id,  // 频道 ID
            "rtmpurl" => $channel_data['rtmpurl'],
            "ffmpeg" => $channel_data['ffmpeg'],
            "ffmpeg_name" => $channel_data['ffmpeg_name'],
            'action' =>'start'
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Content-Length: " . strlen($post_data)
        ]);
        // echo($api_url);exit;
        $api_response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($http_status !== 200 || !$api_response) {
            $response['message'] = "无法连接到流媒体服务，HTTP 状态码：$http_status";
            echo json_encode($response);
            exit;
        }
        // error_log($api_response, 3, $GLOBALS['logs']);

        // 解析 API 响应
        $result = json_decode($api_response, true);

        if ($result['status'] === 'success') {
            // 启动成功
            $response['status'] = 'success';
            $response['message'] = '频道已成功启动！';
        } else {
            $response['message'] = $result['message'] ?? '启动失败，原因未知。';
        }
    } else {
        $response['message'] = '频道 ID 缺失！';
    }
} else {
    $response['message'] = '请求方法必须为 POST！';
}

echo json_encode($response);
