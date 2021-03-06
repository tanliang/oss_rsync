<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set('memory_limit','512m');

require_once 'conf.php';
require_once 'Oss.php';

$ossupload = TL_Oss::getInstance(OSS_UPLOAD_BUCKET);
$osslog = TL_Oss::getInstance(OSS_LOG_BUCKET);

$list = $osslog->getList();
$time_start = time();
$time_max = 3600;		// one hour

foreach ($list as $k => $v) {
	if ((time()-$time_start) >= $time_max) {
		break;
	}
	
	if (substr($k, 0, 7) != OSS_LOG_PRE) {
		continue;
	}
	if (isset($argv[1]) && substr($k, -13, 2) % 2 != $argv[1]) {
		continue;
	}
	
	$item = $osslog->get($k);
	$res = array();
	foreach (explode("\n", $item) as $v2) {
		$fields = explode(' ', $v2);
		if ($fields[6] == '"PUT') {
			$_1 = explode('?', $fields[7]);
			$res[$_1[0]]++;
		}
	}

	foreach ($res as $k2 => $v2) {
		$pathinfo = pathinfo($k2);
		$dirname = $pathinfo['dirname'];
		$basename = $pathinfo['basename'];
		
		$dir = getMultDir($local_path, $dirname);
		$file = $dir.$basename;
		$data = $ossupload->get(ltrim($k2, '/'.OSS_UPLOAD_BUCKET.'/'));
		if (createFile($file, $data)) {
			file_put_contents('log/'.date('Ym').'.txt', $file.PHP_EOL, FILE_APPEND);
		}
		
	}
	$osslog->delete($k);
}
