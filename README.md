多频道直播管理程序

一个PHP 实现的 调用ffmpeg 切片 转码 推流工具

1、将程序部署在网站的根目录

2、hls 目录设置为777权限

3、nginx 1.2 +

4、PHP 7.2 -7.4

5、PHP 放开shell_exec 函数

6、将链接 https://你的域名/api.php?action=check_and_restart 放入定时任务，每隔5分钟执行

7、从 https://ffmpeg.org/download.html 下载二进制ffmpeg 文件放入根目录，并设置可执行权限

本程序为免费公开项目，禁止任何用于违法的行为


A PHP-based tool for calling FFmpeg to slice, transcode, and push streams

Deploy the program in the root directory of the website.

Set the hls directory permissions to 777.

Use Nginx version 1.2 or higher.

PHP versions 7.2 to 7.4.

Ensure that the shell_exec function is enabled in PHP.

Add the link https://your-domain.com/api.php?action=check_and_restart to a cron job to run every 5 minutes.

Download the binary FFmpeg file from https://ffmpeg.org/download.html and place it in the root directory, then set the executable permissions.

The program is a free and open-source project and any use for illegal activities is prohibited.






