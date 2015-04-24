<?php
define('OSS_ACCESS_ID', '');
define('OSS_ACCESS_KEY', '');
define('OSS_HOST', 'oss.aliyuncs.com');

define('OSS_UPLOAD_BUCKET', '');
define('OSS_LOG_BUCKET', '');
define('OSS_LOG_PRE', '');

$local_path = 'D:\oss';

/**
 * 根据 传入的参数生成多级目录
 *
 * @param string $dir|根目录如 _UPLOAD_DIR_
 * @param string $value|多级目录字符串如 'cache'.DIRECTORY_SEPARATOR.date("Ym")
 * @return string $path
 */
function getMultDir($dir, $value)
{
	$value = str_replace('/', DIRECTORY_SEPARATOR, $value);
	$value = str_replace('\\', DIRECTORY_SEPARATOR, $value);
	foreach (explode(DIRECTORY_SEPARATOR, $value) as $item) {
		$dir .= $item.DIRECTORY_SEPARATOR;
		if (!createDir($dir)) {
			throw new Exception('Dir set failed with '.$dir);
		}
	}
	return $dir;
}

function createDir($dir)
{
	clearstatcache();
	if (file_exists($dir))
		return true;

	return mkdir($dir);
}

function createFile($file, $data, $mode = 'wb+')
{
	$fp = fopen($file, $mode);
	if ($fp)
	{
		fwrite($fp, $data);
		fclose($fp);
		return true;
	}
	return false;
}