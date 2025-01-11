一个PHP 实现的 调用ffmpeg 切片 转码 推流工具

1、将程序部署在网站的根目录

2、hls 目录设置为777权限

3、nginx 1.2 +

4、PHP 7.2 -7.4

5、PHP 放开shell_exec 函数

6、将链接 https://你的域名/api.php?action=check_and_restart 放入定时任务，每隔5分钟执行

