<?php
    //跨網域ajax
    // header("Access-Control-Allow-Origin: *");

	//設定時間
	date_default_timezone_set("Asia/Taipei");

	//禁止js取得cookie
	ini_set("session.cookie_httponly", 1);

	//session過期時間
	ini_set('session.cookie_lifetime', 2592000);
	ini_set('session.gc_maxlifetime', 2592000);

	//只能在https傳遞
	ini_set('session.cookie_secure', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']));

	//快取關閉 表單重複提交
	header("Cache-control:no-cache");

	//設定UTF-8 
	mb_internal_encoding('utf-8');
	header("Content-Type:text/html; charset=utf-8");

	//錯誤訊息直接顯示
    ini_set("display_errors","1");

	//錯誤訊息
	error_reporting(E_ALL & ~E_NOTICE);

	//錯誤訊息全關
	// error_reporting(0);

	//檔案根目錄
	define('APP_PATH',str_replace('\\', '/',substr(__FILE__ , 0 , strlen(__DIR__)-strlen('include'))));

	//errorlog路徑
	ini_set("error_log", APP_PATH."error.log");
	
	//開啟session
	if(!is_dir(APP_PATH.'sessionTemp')){
		mkdir(APP_PATH.'sessionTemp');
	}
	session_save_path(APP_PATH."sessionTemp");
	session_start();

	if(php_sapi_name() != "cli"){
		//避免被偽造PHPSESSID攻擊 前一個IP與瀏覽器不同時將SESSION清除
		if(isset($_SESSION['LAST_USER_AGENT']) && isset($_SESSION['LAST_REMOTE_ADDR'])){
			if(($_SERVER['REMOTE_ADDR'] !== $_SESSION['LAST_REMOTE_ADDR']) && ($_SERVER['HTTP_USER_AGENT'] !== $_SESSION['LAST_USER_AGENT'])){
				session_destroy();
				header('Refresh:0;');
			}
		}
		$_SESSION['LAST_REMOTE_ADDR'] = @$_SERVER['REMOTE_ADDR'];
		$_SESSION['LAST_USER_AGENT'] = @$_SERVER['HTTP_USER_AGENT'];
		
	}

	include_once(APP_PATH.'config/define.php');
	include_once(APP_PATH.'config/dataBase.php');//資料庫
	include_once(APP_PATH.'class/setting.class.php');//系統設定
	include_once(APP_PATH.'class/userDeviceInfomation.trait.php');	//使用者裝置資訊
	include_once(APP_PATH.'class/typeConst.const.php');				//const
	
	$setting = new MTsung\setting($conn);
?>