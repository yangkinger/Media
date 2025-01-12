<?php

include $_SERVER['DOCUMENT_ROOT'].'/action/function.php'; // 引入文件

// 获取频道列表的函数
function getChannelList($file_path) {
    // 检查文件是否存在
    if (!file_exists($file_path)) {
        return [];
    }

    // 读取文件内容
    $channels = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $channel_data = [];

    // 处理每一行数据，假设每行格式为: 频道名称-频道ID-频道源地址
    foreach ($channels as $line) {
        list($name, $id, $url,$rtmpurl,$ffmpeg) = explode(',', $line);
        $listchannel = getChannelStatus(trim($id));
        
        $channel_data[] = [
            'tvname' => trim($name),
            'channel_id' => trim($id),
            'playurl' => trim($url),
            'rtmpurl' => trim($rtmpurl),
            'status' => $listchannel['status'], // 获取频道状态
            'restart_count'=>$listchannel['restart_count'], 
            'mark' =>$listchannel['mark'],
            'ffmpeg' =>getItem($ffmpeg)['name'],
            'ffmpegval' =>getItem($ffmpeg)['value'],
            'channel_action_status' =>$listchannel['channel_action_status']
        ];
    }
   
    return $channel_data;
}



function getChannelStatus($channel_id) {
    $api_url = $GLOBALS['apihost'] ;  // FastAPI 获取频道状态的接口
    
    // 初始化 cURL 请求
    $ch = curl_init();
    
    // 设置 cURL 请求方式为 POST
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    
    // 设置 POST 数据，使用 JSON 格式
    $post_data = json_encode([
        'channel_id' => $channel_id,  // 频道 ID
        'action' => 'status'  // 动作参数为 status
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);  // 将数据作为 JSON 格式发送

    // 设置 HTTP 头部，告知服务器接收 JSON 数据
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);
    
    // 执行请求
    $response = curl_exec($ch);
    
    // 检查请求是否成功
    if (curl_errno($ch)) {
        curl_close($ch);
        return [
            'status' => 'inactive',  // 如果请求失败，默认返回 'inactive'
            'restart_count' => 0
        ];
    }
    
    // 关闭 cURL
    curl_close($ch);
    
    // 解析 API 响应
    $result = json_decode($response, true);
    
    // 检查返回的状态和重启次数
    if (isset($result['status']) && $result['is_online'] === true) {
        $status = 'active';  // 如果状态是 running，则返回 active
    } else {
        $status = 'inactive';  // 否则，返回 inactive
    }
    
    // 获取重启次数，默认为 0
    $restart_count = isset($result['restart_count']) ? $result['restart_count'] : 0;
    $mark = isset($result['mark']) ? $result['mark'] : '';  // 防止 mark 不存在
    return [
        'status' => $status,
        'restart_count' => $restart_count,
        'mark' => $mark,
        'channel_action_status' => $result['channel_action_status']
    ];
}



?>
