<?php
	include_once("include/header.php");

	/**
	 * 上傳檔案
	 */
	error_reporting(0);
	set_time_limit(0);
	ini_set('upload_max_filesize ', '800M');
	ini_set('post_max_size', '800M');
	ini_set('memory_limit', '-1');

	//網站空間限制
	$webSize[] = $setting->getValue("webMaxSize")-$console->getDirSize(APP_PATH);
	$webSize[] = $setting->getValue("uploadMaxSize")-$console->getDirSize(APP_PATH.UPLOAD_PATH);

	if($setting->getValue("sizeSwitch")){
		if($_FILES){
			$size = 0;
			foreach ($_FILES as $key => $value) {
				if(is_array($value["size"])){
					foreach ($value["size"] as $key1 => $value1) {
						$size += $value1;
					}
				}else{
					$size += $value["size"];
				}
			}
			foreach ($webSize as $key => $value) {
				if($size > $value){
					echo "網站容量超過大小限制!請連絡網站管理員";
					exit;
				}
			}
		}
	}
	//網站空間限制

	//前台檔案限制
	$allowMIME = array('image/jpeg', 'image/png', 'image/gif', 'image/bmp' , 'image/x-icon' ,'video/mp4', 'audio/mpeg' , 'audio/mp3' ,'application/pdf' ,'application/msword');
	$allowExt = array('jpeg', 'jpg', 'bmp', 'gif', 'png' , 'pdf' , 'ico' , 'mp3' , 'mp4');
	$maxSize = (int)$setting->getValue("oneUploadMaxSize");//1MB=1048576


	//後台登入狀態
	$isLogin = isset($_SESSION[FRAME_NAME]["member"]["serback"]) && $_SESSION[FRAME_NAME]["member"]["serback"];

	// 取得來源
	if(isset($_SERVER["HTTP_REFERER"]) && substr($_SERVER["HTTP_REFERER"], -1)=='/'){
		$_SERVER["HTTP_REFERER"] = substr($_SERVER["HTTP_REFERER"],0,-1);
	}
	$temp = explode($_SERVER["HTTP_HOST"].WEB_PATH, $_SERVER["HTTP_REFERER"]);

	if(isset($temp[1])){
		if(isset($_GET["isTinyMCE"]) && $_GET["isTinyMCE"]==='1' && $isLogin){
			$temp = 'files';
			$allowMIME = array();
			$allowExt = array();
		}else{
			$temp[1] = str_replace("/serback","",$temp[1]);
			$temp = explode("?",explode("/",$temp[1])[1])[0];
		}
		$upload = new MTsung\Upload(	$allowMIME,
										$allowExt,
										$maxSize,
										false,
										UPLOAD_PATH.$temp
									);
		if($_FILES){

			//圖片處理
			foreach ($_FILES as $key => $value) {
				if(is_array($value["type"])){
					foreach ($value["type"] as $key1 => $value1) {
						$array = array('image/jpeg', 'image/png', 'image/bmp');
						if(in_array($value1, $array)){
							$tempFile = $value['tmp_name'][$key1].".".explode("/",$value1)[1];
							copy($value['tmp_name'][$key1],$tempFile);

							//壓縮
							// $image = (new MTsung\imgCompress($tempFile,1))->TinyPNG($tempFile);
							//浮水印
							if(isset($_GET["watermark"]) && is_numeric($_GET["watermark"]) && $setting->getValue("watermark")){
								$watermark = new MTsung\watermark();
								$watermarkFile = APP_PATH.UPLOAD_PATH.$setting->getValue("watermark");
								$watermark->apply($tempFile, $tempFile, $watermarkFile, $_GET["watermark"]);
							}

							copy($tempFile,$value['tmp_name'][$key1]);
							unlink($tempFile);
						}
					}
				}else{
					$array = array('image/jpeg', 'image/png', 'image/bmp');
					if(in_array($value["type"], $array)){
						$tempFile = $value['tmp_name'].".".explode("/",$value["type"])[1];
						copy($value['tmp_name'],$tempFile);

						//壓縮
						// $image = (new MTsung\imgCompress($tempFile,1))->TinyPNG($tempFile);
						// 浮水印
						if(isset($_GET["watermark"]) && is_numeric($_GET["watermark"]) && $setting->getValue("watermark")){
							$watermark = new MTsung\watermark();
							$watermarkFile = APP_PATH.UPLOAD_PATH.$setting->getValue("watermark");
							$watermark->apply($tempFile, $tempFile, $watermarkFile, $_GET["watermark"]);
						}

						copy($tempFile,$value['tmp_name']);
						unlink($tempFile);
					}
				}
			}
		}
		
		$upload->callUploadFile();

		if(!$upload->getDestination()){
			echo "Upload error.\n".$upload->res['error'];
			// echo "upload_max_filesize is ".ini_get('upload_max_filesize');
			exit;
		}
		if(isset($_GET["isTinyMCE"]) && $_GET["isTinyMCE"]==='1'){
			if($upload->getDestination()[0]){
				if(isset($_GET["isFile"]) && $_GET["isFile"]==='1'){
					echo json_encode(array('location' => str_replace(DATA_PATH,"",$upload->getDestination()[0])));
					exit;
				}
				echo json_encode(array('location' => $upload->getDestination()[0]));
			}else{
				header("HTTP/1.1 400 Invalid file type.");
			}
		}else{
			$temp = $upload->getDestination();
			if($temp){
				foreach ($temp as $key => $value) {
					$temp[$key] = $value = str_replace(DATA_PATH,"",$value);
					$_SESSION[FRAME_NAME]["PICTURE_TEMP"][] = str_replace(UPLOAD_PATH,"",$value);
				}
				print_r(json_encode($temp));
			}
		}
		exit;
	}else{
		// 來源錯誤
		echo "來源網域錯誤!!";
		exit;
	}
	exit;