<?php
// 开启会话
session_start();

// 检查用户是否已登录
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}


$username = $_SESSION['username'];
include 'init.php'; // 引入文件
include 'action/load_channel.php'; // 引入文件
// 读取频道列表
$file_path = $GLOBALS['playurl'];  // 假设txt文件路径为 playurl.txt
$channels = getChannelList($file_path);
// print_r(json_encode($channels));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>频道管理</title>

    <?php include  __DIR__ .'/static/head.html';?>
    <script src="/static/main.js"></script>
</head>
<body>
    
        <!-- 顶部栏 -->
    <div class="header">
        欢迎
    </div>


     <?php include  __DIR__ .'/static/p_head.html';?>
     
    <div class="content">
        <div style="float:right; padding:10px"><button id="openModalBtn">添加频道</button></div>

        <table>
            <thead>
                <tr>
                    <th>频道名称</th>
                    <th>配置文件</th>
                    <th>频道ID</th>
                    <th>频道源地址</th>
                    <th>HLS 链接</th>
                    <th>推流地址</th>
                    <th>推流播放地址</th>
                    <th>频道状态</th>
                    <th>重启次数</th>
                    <th>启动操作</th>
                    <th>停止操作</th>
                    <th>编辑</th>
                    <th>删除操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($channels as $channel): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($channel['tvname']); ?></td>
                        <td><?php echo htmlspecialchars($channel['ffmpeg']); ?></td>
                        <td><?php echo htmlspecialchars($channel['channel_id']); ?></td>
                        <td>
                            <a href="<?php echo htmlspecialchars($channel['playurl']); ?>" target="_blank" title="<?php echo htmlspecialchars($channel['playurl']); ?>">
                                <?php echo mb_strlen($channel['playurl']) > 30 ? mb_substr($channel['playurl'], 0, 30) . '...' : htmlspecialchars($channel['playurl']); ?>
                            </a>
                        </td>
                        <td>
                            <a href="/hls/<?php echo htmlspecialchars($channel['channel_id']); ?>/<?php echo htmlspecialchars($channel['channel_id']); ?>_index.m3u8" target="_blank" title="/hls/<?php echo htmlspecialchars($channel['channel_id']); ?>/<?php echo htmlspecialchars($channel['channel_id']); ?>_index.m3u8">
                                <?php echo mb_strlen($channel['channel_id']) > 20 ? mb_substr($channel['channel_id'], 0, 20) . '...' : htmlspecialchars($channel['channel_id']); ?>
                            </a>
                        </td>
                        <td>
                            <a href="<?php echo htmlspecialchars($channel['rtmpurl']); ?>" target="_blank" title="<?php echo htmlspecialchars($channel['rtmpurl']); ?>">
                                <?php echo mb_strlen($channel['rtmpurl']) > 30 ? mb_substr($channel['rtmpurl'], 0, 30) . '...' : htmlspecialchars($channel['rtmpurl']); ?>
                            </a>
                        </td>
                        <td>
                            <?php
                            if (preg_match('/\/([^\/]+)$/', htmlspecialchars($channel['rtmpurl']), $matches)) {
                                $stream_id = $matches[1];  // 提取的流 ID
                                $rtmpouturl =  "http://ali.hlspull.yximgs.com/live/".$stream_id.'.flv';
                            } else {
                                $rtmpouturl = "";
                            }
                            ?>
                            <a href="<?php echo htmlspecialchars($rtmpouturl); ?>" target="_blank" title="<?php echo htmlspecialchars($rtmpouturl); ?>">
                                <?php echo mb_strlen($rtmpouturl) > 30 ? mb_substr($rtmpouturl, 0, 30) . '...' : $rtmpouturl; ?>
                            </a>
                        </td>

                        <td class="<?php echo $channel['status'] == 'active' ? 'active' : 'inactive'; ?>">
                            <?php echo $channel['status'] == 'active' ? '✔ 在线' : '✘ 离线'; ?>
                        </td>
                        <td> <?php echo $channel['restart_count'] == null ? '0' : $channel['restart_count']; ?></td>
                        
                        <td>
                            <button type="button" class="startChannelButton "  <?php echo $channel['channel_action_status'] == 'start' ? 'disabled' : ''; ?>  data-ffmpeg-name="<?php echo $channel['ffmpeg']; ?>" data-ffmpeg="<?php echo $channel['ffmpegval']; ?>" data-channel-rtmpurl="<?php echo htmlspecialchars($channel['rtmpurl']); ?>" data-channel-playurl="<?php echo htmlspecialchars($channel['playurl']); ?>" data-channel-id="<?php echo htmlspecialchars($channel['channel_id']); ?>">启动</button>
                        </td>
                        <td>
                            <button type="button" class="stopChannelButton "  <?php echo $channel['channel_action_status'] == 'start' ? '' : 'disabled'; ?>  data-channel-id="<?php echo htmlspecialchars($channel['channel_id']); ?>">停止</button>
                        </td>
                        
                        <td><button class="editButton" data-ffmpeg="<?php echo $channel['ffmpeg']; ?>" data-id="<?php echo $channel['channel_id']; ?>" data-name="<?php echo $channel['tvname']; ?>" data-url="<?php echo $channel['playurl']; ?>" data-rtmpurl="<?php echo $channel['rtmpurl']; ?>">编辑</button></td>
                        <td>
                            <button type="button" class="deleteChannelButton" data-channel-id="<?php echo htmlspecialchars($channel['channel_id']); ?>">删除</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
            <!-- 模态窗口 -->
        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close" id="closeModalBtn">&times;</span>
                <h2>添加新频道</h2>
                <form id="addChannelForm">
                    <input type="text" name="channel_name" placeholder="频道名称" required>
                    <input type="text" name="channel_id" placeholder="频道ID" required>
                    <input type="url" name="channel_url" placeholder="频道源地址" required>
                    <input type="url" name="rtmp_url" placeholder="推流地址" >
                    <select class="form-select" name="ffmpeg_config" id="modalItemSelect" required>
                        
                    </select>
                    <button type="submit">添加频道</button>
                </form>
            </div>
        </div>

        <!-- 编辑频道模态窗口 -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close" id="closeEditModalBtn">&times;</span>
                <h2>编辑频道</h2>
                <form id="editChannelForm">
                    <input type="hidden" name="editChannelId" id="editChannelId">
                    <input type="text" name="editChannelName" id="editChannelName" placeholder="频道名称" required>
                    <input type="url" name="editChannelUrl" id="editChannelUrl" placeholder="频道源地址" required>
                    <input type="url" name="editrtmpUrl" id="editrtmpUrl" placeholder="推流地址" >
                    <select class="form-select" name="editffmpeg_config" id="editmodalItemSelect" required>
                        
                    </select>
                    <button type="submit">保存更改</button>
                </form>
            </div>
        </div>
        
        
        <div id="hlsModal" class="modal">
            <?php include  __DIR__ .'/static/hls.html';?>
        </div>
        <div class="footer">
            <p>© 2024 频道管理平台 | <a href="#">帮助中心</a></p>
        </div>
    </div>
    
    

    
    
    

</body>
</html>
