<?php
// 判断是否通过POST提交表单数据
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取表单数据
    $video_size = $_POST['video_size'] ?? 'copy';
    $video_codec = $_POST['video_codec'] ?? 'copy';
    $audio_codec = $_POST['audio_codec'] ?? 'copy';
    $audio_bitrate = $_POST['audio_bitrate'] ?? '128';
    $audio_sample_rate = $_POST['audio_sample_rate'] ?? '44100';
    $hls_time = $_POST['hls_time'] ?? '4';
    $hls_list_size = $_POST['hls_list_size'] ?? '6';
    $v_c_same = $_POST['v_c_same'] ?? '';
    $v_c = $_POST['v_c'] ?? '';
    $extra_params = $_POST['extra_params'] ?? '';
    $source_address = $_POST['source_address'] ?? 'input.mp4';  // 假设用户输入了源视频地址

    // 生成FFmpeg命令
    $ffmpegCommand = "ffmpeg -i $source_address";

    // 添加分辨率设置
    if ($video_size !== 'copy') {
        $ffmpegCommand .= " -s $video_size";
    }

    // 添加视频编解码器设置
    if ($video_codec !== 'copy') {
        $ffmpegCommand .= " -vcodec $video_codec";
    }

    // 添加音频编解码器设置
    if ($audio_codec !== 'copy') {
        $ffmpegCommand .= " -acodec $audio_codec";
    }

    // 添加音频比特率设置
    $ffmpegCommand .= " -b:a {$audio_bitrate}k";

    // 添加音频采样率设置
    $ffmpegCommand .= " -ar $audio_sample_rate";

    // 添加HLS切片时长
    $ffmpegCommand .= " -hls_time $hls_time";

    // 添加HLS列表大小
    $ffmpegCommand .= " -hls_list_size $hls_list_size";

    // 添加音视频同步设置
    if ($v_c_same) {
        $ffmpegCommand .= " $v_c_same";
    }

    // 添加额外参数
    if ($v_c) {
        $ffmpegCommand .= " $v_c";
    }

    if ($extra_params) {
        $ffmpegCommand .= " $extra_params";
    }

    // 添加输出格式
    $ffmpegCommand .= " -f hls output.m3u8";

    // 生成FFmpeg命令行文本文件
    $fileName = 'ffmpeg_command.txt';
    file_put_contents($fileName, $ffmpegCommand);

    // 提示用户下载生成的FFmpeg命令文件
    echo "FFmpeg命令已经生成，并保存在 <a href='$fileName'>$fileName</a> 文件中。";
} 
?>
