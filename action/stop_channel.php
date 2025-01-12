<?php
include $_SERVER['DOCUMENT_ROOT'].'/init.php'; // 引入文件
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $channel_id = $_POST['channel_id'] ?? '';

    if ($channel_id) {
        // 假设停止切片的逻辑是向 API 发送请求
        $api_url =  $GLOBALS['apihost']."/stop_stream/";  // 假设 FastAPI 有停止流的接口
        $post_data = json_encode([
            "channel_id" => $channel_id,  // 频道 ID
            "action" => 'stop'
        ]);
        // 初始化 cURL 请求
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Content-Length: " . strlen($post_data)
        ]);

        // 执行请求
        $response = curl_exec($ch);
        curl_close($ch);
        // 解析响应
        $result = json_decode($response, true);
        if (isset($result['status']) && $result['status'] === 'success') {
            echo json_encode([
                'status' => 'success',
                'message' => '频道已成功停止切片！'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                // 'message' => '停止失败，请重试！'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => '频道ID缺失！'
        ]);
    }
}
?>
