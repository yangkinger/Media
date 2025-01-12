<?php

// 日志文件路径

define("ONLINE_TIMEOUT_SECONDS", 30); // 在线用户超时时间
include $_SERVER['DOCUMENT_ROOT'].'/init.php'; // 引入文件
define("LOG_FILE", $GLOBALS['channel_status']);
include $_SERVER['DOCUMENT_ROOT'].'/action/function.php'; // 引入文件


// 记录状态变化日志
function logStatusChange($channel_id, $status,$restart_count = 0) {
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = [
        'timestamp' => $timestamp,
        'channel_id' => $channel_id,
        'status' => $status,
        'restart_count' => $restart_count
    ];

    // 读取现有日志
    $log_data = [];
    if (file_exists(LOG_FILE)) {
        $log_data = json_decode(file_get_contents(LOG_FILE), true);
    }

    // 检查是否已有该频道的记录
    $channel_found = false;
    foreach ($log_data as &$entry) {
        if ($entry['channel_id'] === $channel_id) {
            // 如果找到该频道，更新其状态
            $entry['status'] = $status;
            $entry['restart_count'] = $restart_count;
            $entry['timestamp'] = $timestamp; // 更新时间戳
            $channel_found = true;
            break;
        }
    }

    // 如果没有找到该频道，添加新的记录
    if (!$channel_found) {
        $log_data[] = $log_entry;
    }

    // 保存回日志文件
    file_put_contents(LOG_FILE, json_encode($log_data, JSON_PRETTY_PRINT));
}

// 启动任务
function startStream($input_url, $output_directory, $channel_id, $rtmp_url, $ffmpeg_conf, $ffmpeg_name) {
    // 构建输出目录和文件路径
    $output_directory = rtrim($output_directory, '/') . '/' . $channel_id;
    if (!is_dir($output_directory)) {
        mkdir($output_directory, 0777, true);
    }

    $output_m3u8_file = $output_directory . "/{$channel_id}_index.m3u8";
    $output_ts_template = $output_directory . "/{$channel_id}_segment_%03d.ts";
    
    $ffmpeg_cmd = $GLOBALS['ffmpegpath']." -i $input_url $ffmpeg_conf -metadata comment=$channel_id";

    if (strpos($ffmpeg_name, 'rtmp') !== false) {
        $ffmpeg_cmd .= " -f flv $rtmp_url";
    }
    if (strpos($ffmpeg_name, 'hls') !== false) {
        $ffmpeg_cmd .= " -hls_segment_filename $output_ts_template -f hls $output_m3u8_file";
    }
    
    // 将标准输出和标准错误都重定向到 /dev/null
    // $log_file = $output_directory . "/logffmpeg.txt";
    // $ffmpeg_cmd .= " >> $log_file 2>&1 &";  // 追加输出到日志文件
    $ffmpeg_cmd .= " > /dev/null 2>&1 & echo $!";  // 修改这一行
    $pid = 0;
    // 执行命令并获取进程 ID
        // 构建 FFmpeg 命令
    if (file_exists($GLOBALS['ffmpegpath'])) {
        $pid = shell_exec($ffmpeg_cmd);
        // error_log($ffmpeg_cmd, 3, $GLOBALS['logs']);
    }else{
        // error_log($ffmpeg_cmd, 3, $GLOBALS['logs']);
        return [
            'status' => 'error',
            'message' => "任务 $channel_id 失败",
        ];
    }
    
    
    
    // error_log($ffmpeg_cmd, 3, $GLOBALS['logs']);
    // 记录状态变化
    logStatusChange($channel_id, 'start',0);

    return [
        'status' => 'success',
        'message' => "任务 $channel_id 已启动",
        'pid' => $pid
    ];
}

// 停止任务
function stopStream($channel_id) {
    // 查询所有正在运行的进程
    $ps_output = shell_exec("ps -ef| grep ffmpeg");
    logStatusChange($channel_id, 'stop',0);
    // 查找是否有进程正在处理该频道的流任务
    if (strpos($ps_output, $channel_id) !== false) {
        // 获取该进程的 PID
        preg_match("/\s+(\d+)\s+/i", $ps_output, $matches);
        $pid = $matches[1] ?? null;
        
        // 杀掉进程
        shell_exec("kill -9 $pid");

        // 记录状态变化
        
        
        return [
            'status' => 'success',
            'message' => "任务 $channel_id 已停止"
        ];
    }

    return [
        'status' => 'error',
        'message' => "未找到任务 $channel_id 的运行状态"
    ];
}

// 判断频道是否在线（通过进程判断）
function getStreamOnlineStatus($channel_id) {
    // 查询所有正在运行的进程
    $ps_output = shell_exec("ps -ef| grep ffmpeg");
   
    // 查找是否有进程正在处理该频道的流任务
    if (strpos($ps_output, $channel_id) !== false) {
        // 获取该进程的 PID
        preg_match("/\s+(\d+)\s+/i", $ps_output, $matches);
        $pid = $matches[1] ?? null;

        return [
            'status' => 'success',
            'message' => "频道 $channel_id 正在运行",
            'is_online' => true,
            'pid' => $pid,
            'channel_action_status'  => getChannelStatus($channel_id)['status'],
            'restart_count' => getChannelStatus($channel_id)['restart_count']
        ];
    }
    
    
    return [
        'status' => 'success',
        'message' => "频道 $channel_id 未运行",
        'is_online' => false,
        'pid' => null,
        'channel_action_status'  => getChannelStatus($channel_id)['status'],
        'restart_count' => getChannelStatus($channel_id)['restart_count']
    ];
}

// 获取频道的状态（通过 JSON 格式的日志）
function getChannelStatus($channel_id) {
    if (file_exists(LOG_FILE)) {
        $log_data = json_decode(file_get_contents(LOG_FILE), true);

        // 直接查找该频道的状态
        foreach ($log_data as $entry) {
            if ($entry['channel_id'] == $channel_id) {
                return [
                    'status' =>  $entry['status'],
                    'restart_count' => $entry['restart_count'],
                    'message' => "频道 $channel_id 当前状态: " . $entry['status']
                ];
            }
        }
    }

    return [
        'status' => 'stop',
        'restart_count' => 0,
        'message' => "无法获取频道 $channel_id 状态"
    ];
}






// 自动检查进程状态并重启
function autoCheckAndRestart() {
    
    $channels = file($GLOBALS['playurl'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // 读取日志文件
    $log_data = [];
    if (file_exists(LOG_FILE)) {
        $log_data = json_decode(file_get_contents(LOG_FILE), true);
    }

    foreach ($log_data as &$entry) {
        // 如果频道状态是 'start' 并且进程不在线，则重新启动
        if ($entry['status'] === 'start') {
            $ps_output = shell_exec("ps -ef| grep ffmpeg");

            if (strpos($ps_output, $entry['channel_id']) === false) {
                $channelid = $entry['channel_id'];
                
                // list($channel_name, $channel_id, $input_url,$rtmp_url,$ffmpeg)  = explode(',', preg_grep("/$channelid/", $channels)[0]);
                $srcchannel = preg_grep("/$channelid/", $channels)["1"];
                $chlinfo = explode(',', $srcchannel);
                list($channel_name, $channel_id, $input_url,$rtmp_url,$ffmpeg)  = $chlinfo;
                
                
                // error_log('频道'.$srcchannel.'重启了一次', 3, $GLOBALS['logs']);
                
                $ffmpeg_conf = getItem($ffmpeg)['value'];// Placeholder
                
                $ffmpeg_name = getItem($ffmpeg)['name']; // Placeholder
                
                startStream($input_url, $GLOBALS['hlspath'], $entry['channel_id'], $rtmp_url, $ffmpeg_conf, $ffmpeg_name);

                // 更新重启计数器
                $entry['restart_count']++;
                logStatusChange($entry['channel_id'], 'start', $entry['restart_count']);
            }
        }
    }
}

// 定时任务：每隔 5 分钟执行一次
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'check_and_restart') {
    autoCheckAndRestart();
    echo json_encode(['status' => 'success', 'message' => '进程检查和重启已完成']);
}



// 入口
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取原始的 JSON 数据
    $json_data = file_get_contents('php://input');
    
    // 将 JSON 数据解码为 PHP 数组
    $data = json_decode($json_data, true);

    $action = $data['action'] ?? '';
    $channel_id = $data['channel_id'] ?? '';
    switch ($action) {
        case 'start':
            $input_url = $data['input_url'] ?? '';
            $output_directory = $data['output_directory'] ?? '';
            $rtmp_url = $data['rtmpurl'] ?? '';
            $ffmpeg_conf = $data['ffmpeg'] ?? '';
            $ffmpeg_name = $data['ffmpeg_name'] ?? '';
            echo json_encode(startStream($input_url, $output_directory, $channel_id, $rtmp_url, $ffmpeg_conf, $ffmpeg_name));
            break;

        case 'stop':
            echo json_encode(stopStream($channel_id));
            break;

        case 'status':
            echo json_encode(getStreamOnlineStatus($channel_id));
            break;

        case 'get_status':
            echo json_encode(getChannelStatus($channel_id));
            break;

        default:
            echo json_encode(['status' => 'error', 'message' =>'no data']);
            break;
    }
}
