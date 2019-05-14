<?php 
	include_once("include/header.php");

	function secondAutoload($file){
		$file = str_replace("MTsung\\", "", $file);
	    $filename = APP_PATH."/class/".$file.".class.php";
	    if (is_readable($filename)){
	        require $filename;
	    }
	}

	spl_autoload_register('secondAutoload');//自動載入calss
	include_once(APP_PATH.'include/main.php');//核心
	include_once(APP_PATH.'class/uploadFile.class.php');// 上傳模組



	$design = new MTsung\design();
	$console = new MTsung\main($conn,$design,$setting);
	$webSetting = new MTsung\webSetting($console,PREFIX."web_setting",$_SESSION[FRAME_NAME]['language'.$console->langSessionName]);//前端輸出用
	$console->setWebSetting($webSetting);
	$console->loadController();