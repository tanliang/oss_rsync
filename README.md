# oss_rsync
阿里云 OSS 同步脚本，从 OSS 同步至本地

github 有本地同步至 OSS 的[项目](https://github.com/mehwww/oss-sync)，但实际我需要的是从 OSS 同步至本地，只能自己动手了。

# 原理
基本就是利用 OSS 的 logging 功能，检查里面的 PUT 相关的记录，并下载至本地即可，因为 OSS 每小时更新一次日志，使用计划任务，或者 crontab 执行

# 配置
修改 conf.php

define('OSS_ACCESS_ID', '');

define('OSS_ACCESS_KEY', '');

define('OSS_HOST', 'oss.aliyuncs.com');


define('OSS_UPLOAD_BUCKET', '');        // 需要同步内容的 bucket

define('OSS_LOG_BUCKET', '');           // 记录同步日志的 bucket

define('OSS_LOG_PRE', '');              // 设置记录时对应的前缀

$local_path = 'D:\oss';                 // 本地 OSS 存储目录
