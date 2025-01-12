<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HLS 视频播放</title>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            flex-direction: column;
        }
        .container {
            max-width: 800px;
            width: 100%;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }
        h1 {
            font-size: 24px;
            color: #4CAF50;
            margin-bottom: 20px;
        }
        input[type="text"] {
            width: 80%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        video {
            width: 100%;
            margin-top: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .error {
            color: red;
            font-size: 18px;
            margin-top: 20px;
        }
        footer {
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        // 获取 GET 参数
        $url = isset($_GET['url']) ? htmlspecialchars($_GET['url']) : '';
        ?>

        <h1>HLS 视频播放</h1>
        <form method="get">
            <input type="text" id="urlInput" name="url" value="<?php echo $url; ?>" placeholder="请输入 HLS 视频 URL">
            <button type="submit">加载 URL</button>
        </form>
        <video id="video" controls></video>

        <footer>
            提示：若无法播放，请确保提供的是有效的 HLS 视频 URL。
        </footer>
    </div>

    <script>
        window.onload = function() {
            const videoUrl = "<?php echo $url; ?>";
            const video = document.getElementById('video');

            if (videoUrl) {
                if (Hls.isSupported()) {
                    const hls = new Hls();
                    hls.loadSource(videoUrl);
                    hls.attachMedia(video);
                    hls.on(Hls.Events.MANIFEST_PARSED, function () {
                        video.play();
                    });
                } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                    video.src = videoUrl;
                    video.addEventListener('loadedmetadata', function () {
                        video.play();
                    });
                } else {
                    alert('您的浏览器不支持 HLS 视频播放。');
                }
            }
        };
    </script>
</body>
</html>
