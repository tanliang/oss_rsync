<?php
require_once 'oss/sdk.class.php';

class TL_Oss {
	
	static private $instance = array();
	private $oss_sdk_service = NULL;
	private $oss_bucket = '';
	private $upload_path = '';
	
	private function __construct($bucket) {
		$this->oss_sdk_service = new ALIOSS(OSS_ACCESS_ID, 
				OSS_ACCESS_KEY, OSS_HOST);
		$this->oss_sdk_service->set_debug_mode(FALSE);
		$this->oss_bucket = $bucket;
	}
	
	static function getInstance($bucket) {
		if (!isset(self::$instance[$bucket])) {
			self::$instance[$bucket] = new self($bucket);
		}
		return self::$instance[$bucket];
	}
	
	/**
	 * 复制到oss
	 * 
	 * @param $path 目标目录
	 * @param $file 文件目录
	 */
	public function upload($type, $file) {
		$mime = mime_content_type($file);
		$mime_allow = array(
				'image/jpeg' => 'jpg'
				,'image/png' => 'png'
				,'text/html' => 'htm' // html match oss mime-type
		);
		
		if (!in_array($mime, array_keys($mime_allow))) {
			return false;	
		}
		$path = $type.'/'.date('Ym').'/'.uniqid().'.'.$mime_allow[$mime];
		$response = $this->oss_sdk_service->upload_file_by_file(
				$this->oss_bucket, $path, $file, array('Content-Type'=>$mime));
		if ($response->status == 200) {
			$this->upload_path = $path;
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 删除oss对象
	 *
	 * @param $bucket 目标目录
	 * @param $file 文件目录
	 */
	public function delete($file) {
		$response = $this->oss_sdk_service->delete_object($this->oss_bucket, $file);
		if ($response->status == 204) {
			return true;
		} else {
			return false;
		}
	}
	
	public function get($file)
	{
		$res = NULL;
		$response = $this->oss_sdk_service->get_object($this->oss_bucket, $file);
		if ($response->status == 200) {
			$res = $response->body;
		}
		return $res;
	}
	
	public function getList($option = NULL)
	{
		$res = array();
		$response = $this->oss_sdk_service->list_object($this->oss_bucket, $option);
		//var_dump($response);exit;
		if ($response->status == 200) {
			$dom = new DOMDocument();
			$dom->loadXML($response->body);
			
			$contents = $dom->getElementsByTagName('Contents');
			for ($i=0; $i<$contents->length; $i++) {
				$item = $contents->item($i);
				$key = $item->getElementsByTagName('Key');
				$res[$key->item(0)->nodeValue] = 'file';
			}
			$prefixes = $dom->getElementsByTagName('CommonPrefixes');
			for ($i=0; $i<$prefixes->length; $i++) {
				$item = $prefixes->item($i);
				$pre = $item->getElementsByTagName('Prefix');
				$res[$pre->item(0)->nodeValue] = 'dir';
			}
		} 
		return $res;
	}
	
	public function getUploadPath()
	{
		return 'http://'.$this->oss_bucket.'.'.OSS_HOST.'/'.$this->upload_path;
	}
	
}