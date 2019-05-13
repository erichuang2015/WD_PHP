<?php
	//網站token名字
	define('TOKEN_NAME','MTsung_token');

	//網站SESSION陣列名稱
	define('FRAME_NAME','MTsung');
	
	//定義網站根目錄
	define('WEB_PATH',str_replace(str_replace("\\","/",$_SERVER['DOCUMENT_ROOT']),"",str_replace("\\","/",dirname(dirname(__FILE__)))));
	define('SERBACK_PATH',WEB_PATH."/serback");

	//定義網站預設controller
	define('INDEX_PATH','index');
	
	//定義網站預設語言
	define('LANG','zh-tw');

	//訂單編號長度
	define('ORDER_SIZE',10);

	//訂單編號前墜
	define('ORDER_PREFIX','WD');

	//現在時間
	define('DATE',date("Y-m-d H:i:s"));
	define('DATE_Y',date("Y"));
	define('DATE_M',date("m"));
	define('DATE_D',date("d"));


	define('HTTP',(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? "https://" : "http://");
	if(php_sapi_name() != "cli"){
		define('HTTP_PATH',HTTP.$_SERVER['HTTP_HOST'].WEB_PATH.'/');
	}
	define('CONTROLLER_PATH',APP_PATH.'controller/');
	define('INCLUDE_PATH',APP_PATH.'include/');

?>