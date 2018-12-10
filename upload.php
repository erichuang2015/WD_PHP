<?php
	set_time_limit(0);
	ini_set('upload_max_filesize ', '800M');
	ini_set('post_max_size', '800M');
	ini_set('memory_limit', '-1');

	/**
	 * 上傳檔案
	 */
	include_once("include/header.php");
	include_once(APP_PATH.'class/uploadFile.class.php');// 上傳模組
	include_once(APP_PATH.'class/watermark.class.php');// 浮水印
	include_once(APP_PATH.'class/imgCompress.class.php');// 浮水印

	//網站空間限制
	$webSize[] = $setting->getValue("webMaxSize")-getDirSize(APP_PATH);
	$webSize[] = $setting->getValue("uploadMaxSize")-getDirSize(APP_PATH.'upload/');

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

	
	if(isset($_SESSION[FRAME_NAME]["member"]["serback"]) && $_SESSION[FRAME_NAME]["member"]["serback"]){
		$allowMIME = array('image/jpeg', 'image/png', 'image/gif', 'image/bmp' , 'image/x-icon' ,'video/mp4', 'audio/mpeg' , 'audio/mp3' ,'application/pdf' ,'application/msword');
		$allowExt = array('jpeg', 'jpg', 'bmp', 'gif', 'png' , 'pdf' , 'ico' , 'mp3' , 'mp4');
		// 取得來源
		if(isset($_SERVER["HTTP_REFERER"]) && substr($_SERVER["HTTP_REFERER"], -1)=='/'){
			$_SERVER["HTTP_REFERER"] = substr($_SERVER["HTTP_REFERER"],0,-1);
		}
		$temp = explode($_SERVER["HTTP_HOST"].$MT_web["main_path"], $_SERVER["HTTP_REFERER"]);
		if(isset($temp[1])){
			if(isset($_GET["isTinyMCE"]) && $_GET["isTinyMCE"]==='1'){
				$temp = 'files';
				$allowMIME = array();
				$allowExt = array();
			}else{
				$temp[1] = str_replace("/serback","",$temp[1]);
				$temp = explode("/",$temp[1])[1];
			}
			$upload = new MTsung\Upload(	$allowMIME,
											$allowExt,
											20971520,
											false,
											'upload/'.$temp
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
									$watermarkFile = str_replace(WEB_PATH,APP_PATH,$setting->getValue("watermark"));
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
								$watermarkFile = str_replace(WEB_PATH,APP_PATH,$setting->getValue("watermark"));
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
				echo "Upload error.";
				// echo "upload_max_filesize is ".ini_get('upload_max_filesize');
				exit;
			}
			if(isset($_GET["isTinyMCE"]) && $_GET["isTinyMCE"]==='1'){
				if($upload->getDestination()[0]){
					echo json_encode(array('location' => $upload->getDestination()[0]));
				}else{
					header("HTTP/1.1 400 Invalid file type.");
				}
			}else{
				$temp = $upload->getDestination();
				if($temp){
					foreach ($temp as $key => $value) {
						$_SESSION[FRAME_NAME]["PICTURE_TEMP"][] = str_replace("upload/","",$value);
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
	}else{
		// 沒登入
		echo "無權限";
		exit;
	}
	exit;

	/**
	 * 計算資料夾/檔案大小
	 * @param [type] $path [description]
	 */
	function getDirSize($path) {
	 
	    // I reccomend using a normalize_path function here
	    // to make sure $path contains an ending slash
	    // (-> http://www.jonasjohn.de/snippets/php/normalize-path.htm)
	 
	    // To display a good looking size you can use a readable_filesize
	    // function.
	    // (-> http://www.jonasjohn.de/snippets/php/readable-filesize.htm)
	 
	    $Size = 0;
	 
	    $Dir = opendir($path);
	 
	    if (!$Dir)
	        return -1;
	 
	    while (($File = readdir($Dir)) !== false) {
	 
	        // Skip file pointers
	        if ($File[0] == '.') continue;
	 
	        // Go recursive down, or add the file size
	        if (is_dir($path.$File))
	            $Size += getDirSize($path.$File.DIRECTORY_SEPARATOR);
	        else
	            $Size += filesize($path.$File);
	    }
	 
	    closedir($Dir);
	 
	    return $Size;
	}




