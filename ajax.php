<?php
	include_once("include/header.php");

	$serbackIsLogin = isset($_SESSION[FRAME_NAME]["member"]["serback"])&&$_SESSION[FRAME_NAME]["member"]["serback"];

	/**
	 * 社群登入 (fb,line,google)
	 */
	if(isset($_GET['socialLogin'])){
		$_SESSION[FRAME_NAME][strtoupper($_GET['socialLogin']).'_LOGIN']["id"] = $_GET["id"];
        $_SESSION[FRAME_NAME][strtoupper($_GET['socialLogin']).'_LOGIN']["name"] = $_GET["name"];
        $_SESSION[FRAME_NAME][strtoupper($_GET['socialLogin']).'_LOGIN']["email"] = $_GET["email"];
        $_SESSION[FRAME_NAME][strtoupper($_GET['socialLogin']).'_LOGIN']["picture"] = $_GET["picture"];
		echo true;
		exit;
	}

	/**
	 * 閒置登出 $_GET['doingTime'] sessionName
	 */
	if(isset($_GET['doingTime']) && isset($_SESSION[FRAME_NAME]["member"][$_GET['doingTime']])){
		$doingTime = (int)$_SESSION[FRAME_NAME]["member"][$_GET['doingTime']]["doingTime"];
		if(isset($_SESSION[FRAME_NAME]["DOING_TIME"][$_GET['doingTime']]) && $doingTime > 0){
			if(strtotime(DATE) - strtotime($_SESSION[FRAME_NAME]["DOING_TIME"][$_GET['doingTime']]) > $doingTime){
				echo "logout";
				exit;
			}
		}
		$_SESSION[FRAME_NAME]["DOING_TIME"][$_GET['doingTime']] = DATE;
		echo $doingTime;
		exit;
	}

	/**
	 * 刪除upload內的檔案
	 */
	if(isset($_GET['rmSrc'])){
		if(!$serbackIsLogin){
			echo false;
			exit;
		}
		//防止使用../操作目錄
		while(strpos($_GET['rmSrc'],'../')!==false || strpos($_GET['rmSrc'],'..\\')!==false){
			$_GET['rmSrc'] = str_replace('../',"",$_GET['rmSrc']);
			$_GET['rmSrc'] = str_replace('..\\',"",$_GET['rmSrc']);
		}
		$_GET['rmSrc'] = str_replace(WEB_PATH.'/upload/',"",$_GET['rmSrc']);
		$_GET['rmSrc'] = str_replace('upload/',"",$_GET['rmSrc']);
		$path = APP_PATH.'upload/';
		if(is_file($path.$_GET['rmSrc'])){
            unlink($path.$_GET['rmSrc']);
            //縮圖
            $minImgPath = $path.str_replace(".".pathinfo($_GET['rmSrc'], PATHINFO_EXTENSION),"_min.".pathinfo($_GET['rmSrc'], PATHINFO_EXTENSION),$_GET['rmSrc']);
            if(is_file($minImgPath)){
            	unlink($minImgPath);
            }
            echo true;
        }else{
        	echo false;
        }
        exit;
	}

	/**
	 * 取得appkey
	 */
	if(isset($_GET['appKey'])){
		//限制能取得的資料
		$checkArray = array('fbAuthAppID','googleAuthAppID');
		if(in_array($_GET['appKey'], $checkArray)){
			echo $setting->getValue($_GET['appKey']);
		}
        exit;
	}

	/**
	 * 設定管理語系
	 */
	if(isset($_GET['setSettingLang'])){
		if(!$serbackIsLogin){
			echo false;
			exit;
		}
		$_SESSION[FRAME_NAME]["SETTING_LANG"] = $_GET['setSettingLang'];
        exit;
	}
	
	/**
	 * 驗證email
	 */
	if(isset($_GET['isEmail'])){
		include_once(APP_PATH.'class/validation.class.php');			//表單驗證
		$validation = new MTsung\validation();
		if($validation->isEmail($_GET['isEmail'])){
			echo "ok";
		}
        exit;
	}

	/**
	 * 驗證帳號
	 */
	if(isset($_GET['isAccount'])){
		include_once(APP_PATH.'class/validation.class.php');			//表單驗證
		$validation = new MTsung\validation();
		if($validation->isAccount($_GET['isAccount'])){
			echo "ok";
		}
		exit;
	}

	/**
	 * 驗證身分證
	 */
	if(isset($_GET['isIDCard'])){
		include_once(APP_PATH.'class/validation.class.php');			//表單驗證
		$validation = new MTsung\validation();
		if($validation->isIDCard($_GET['isIDCard'])){
			echo "ok";
		}
		exit;
	}

	/**
	 * 取得語言包
	 */
	if(isset($_GET['getLanguageMsg'])){
		$Lang = isset($_GET['language'])? $_GET['language'] :LANG;
		$temp = parse_ini_file(LANGUAGE_PATH.$Lang.'.ini',true);
		if (isset($temp["jsMessage"])){
			if($Lang!=LANG){
				$tmpe1 = parse_ini_file(LANGUAGE_PATH.LANG.'.ini',true);
				foreach ($tmpe1['jsMessage'] as $key => $value) {
					if(!isset($tmpe['jsMessage'][$key])){
						$tmpe['jsMessage'][$key] = $value;
					}
				}
			}
			echo json_encode($temp["jsMessage"]);
		}
		exit;
	}

	/**
	 * 後台取得商品資料
	 */
	if(isset($_GET['searckProduct']) && isset($_GET['keyword'])){
		if(!$serbackIsLogin){
			echo false;
			exit;
		}
		$conn->SetFetchMode(ADODB_FETCH_ASSOC);
		$_GET = array_map("addslashes", $_GET);
//id,name,picture,specificationsID,specifications
		$sql = "select * from ".PREFIX."product__".str_replace("-","_",$_SESSION[FRAME_NAME]["SETTING_LANG"])." where ";
		if(isset($_GET['id'])){
			$data[] = $_GET['id'];
			$sql .= " id=? and";
		}
		if(isset($_GET['noSearchID'])){
			$data[] = $_GET['noSearchID'];
			$sql .= " id<>? and";
		}
		$data[] = "%".$_GET['keyword']."%";
		$sql .= " name LIKE ? and status='1' order by sort";

		$output = $conn->GetArray($conn->Prepare($sql),$data);
		if($output){
			foreach ($output as $key => $value) {
				$output[$key]["picture"] = explode("|__|",$value["picture"])[0];
				$output[$key]["specificationsID"] = explode("|__|",$value["specificationsID"]);
				$output[$key]["specifications"] = explode("|__|",$value["specifications"]);
			}
		}
		echo json_encode($output);

		$conn->SetFetchMode(ADODB_FETCH_DEFAULT);
		exit;
	}

	/**
	 * 後台取得最新消息
	 */
	if(isset($_GET['searckNews']) && isset($_GET['keyword'])){
		if(!$serbackIsLogin){
			echo false;
			exit;
		}
		$conn->SetFetchMode(ADODB_FETCH_ASSOC);
		$_GET = array_map("addslashes", $_GET);
		$sql = "select id,name from ".PREFIX."news__".str_replace("-","_",$_SESSION[FRAME_NAME]["SETTING_LANG"])." where ";
		if(isset($_GET['id'])){
			$data[] = $_GET['id'];
			$sql .= " id=? and";
		}
		$data[] = "%".$_GET['keyword']."%";
		$sql .= " name LIKE ? and status='1' order by sort";
		$output = $conn->GetArray($conn->Prepare($sql),$data);
		echo json_encode($output);

		$conn->SetFetchMode(ADODB_FETCH_DEFAULT);
		exit;
	}

	/**
	 * 後台取得資料
	 */
	if(isset($_GET['searckData']) && isset($_GET['keyword'])){
		if(!$serbackIsLogin){
			echo false;
			exit;
		}
		$conn->SetFetchMode(ADODB_FETCH_ASSOC);
		$_GET = array_map("addslashes", $_GET);
		$sql = "select id,name from ".PREFIX.$_GET['searckData']."__".str_replace("-","_",$_SESSION[FRAME_NAME]["SETTING_LANG"])." where ";
		if(isset($_GET['id'])){
			$data[] = $_GET['id'];
			$sql .= " id=? and";
		}
		$data[] = "%".$_GET['keyword']."%";
		$sql .= " name LIKE ? and status='1' order by sort";
		$output = $conn->GetArray($conn->Prepare($sql),$data);
		echo json_encode($output);

		$conn->SetFetchMode(ADODB_FETCH_DEFAULT);
		exit;
	}

	/**
	 * 資料庫時間修正
	 */
	if(isset($_GET["setSqlDate"])){
		if(!$serbackIsLogin || !strtotime($_GET["setSqlDate"])){
			echo false;
			exit;
		}
		$temp_table = $conn->GetArray("show tables");
		if ($temp_table)
		foreach ($temp_table as $k=>$v){
			$conn->Execute("UPDATE ".$v[0]." SET create_date='".$_GET["setSqlDate"]." 00:00:00',update_date='".$_GET["setSqlDate"]." 00:00:00'");
			$conn->Execute("UPDATE ".$v[0]." SET detail='".$_GET["setSqlDate"]." 00:00:00' where name='update_date' or name='create_date'");
		}
		echo "ok";
		exit;
	}

	/**
	 * 資料庫帳號修正
	 */
	if(isset($_GET["setSqlUser"])){
		if(!$serbackIsLogin){
			echo false;
			exit;
		}
		$temp_table = $conn->GetArray("show tables");
		if ($temp_table)
		foreach ($temp_table as $k=>$v){
			$conn->Execute("UPDATE ".$v[0]." SET create_user='".$_GET["setSqlUser"]."',update_user='".$_GET["setSqlUser"]."'");
			$conn->Execute("UPDATE ".$v[0]." SET detail='".$_GET["setSqlUser"]."' where name='update_user' or name='create_user'");
		}
		echo "ok";
		exit;
	}
	
	/**
	 * 資料庫log檔案清除
	 */
	if(isset($_GET["rmLogData"])){
		if(!$serbackIsLogin){
			echo false;
			exit;
		}
		$conn->Execute("TRUNCATE ".PREFIX."admin_logs");
		$conn->Execute("TRUNCATE ".PREFIX."analytics");
		$conn->Execute("TRUNCATE ".PREFIX."ecpay_log");
		$conn->Execute("TRUNCATE ".PREFIX."member_logs");
		$conn->Execute("TRUNCATE ".PREFIX."system_log");
		$conn->Execute("TRUNCATE ".PREFIX."admin_logs");
		echo "ok";
		exit;
	}

	/**
	 * 備份資料庫
	 */
	if(isset($_GET["sqlBackup"])){
		if(!$serbackIsLogin){
			echo false;
			exit;
		}
		if($setting->getValue("sizeSwitch")){
			if(!(
					($setting->getValue("webMaxSize")-getDirSize(APP_PATH)>0) && 
					($setting->getValue("outputMaxSize")-getDirSize(APP_PATH.'output/')>0)
				)){
				echo false;
				exit;
			}
		}
		ignore_user_abort(true);
		set_time_limit(0);
		include_once(APP_PATH.'class/backup.class.php');
		$backup = new MTsung\backup($conn);
		$backup->exportDatabase();
		echo true;
		exit;
	}

	/**
	 * 匯入資料庫
	 */
	if(isset($_GET["sqlImport"])){
		if(!$serbackIsLogin){
			echo false;
			exit;
		}
		ignore_user_abort(true);
		set_time_limit(0);
		include_once(APP_PATH.'class/backup.class.php');
		$backup = new MTsung\backup($conn);
		$backup->importDatabase($_GET["sqlImport"]);
		echo true;
		exit;
	}

	/**
	 * 裁切圖片
	 */
	if(isset($_GET["cropperImage"])){

		while(strpos($_GET['src'],'../')!==false || strpos($_GET['src'],'..\\')!==false){
			$_GET['src'] = str_replace('../',"",$_GET['src']);
			$_GET['src'] = str_replace('..\\',"",$_GET['src']);
		}
		$_GET['src'] = str_replace(WEB_PATH.'/upload/',"",$_GET['src']);
		$_GET['src'] = $path = APP_PATH.'upload/'.str_replace('upload/',"",$_GET['src']);

		$temp = explode(".",$_GET["src"]);
		$type = strtolower(end($temp));
		$array = array('jpg' ,'jpeg', 'png', 'bmp');
        if(in_array($type, $array)){
			if($type=='jpg') $type = 'jpeg';
        	eval("\$img = imagecreatefrom".$type."(\$_GET['src']);");
			$output = imagecreatetruecolor($_GET["w"], $_GET["h"]);
	        imagesavealpha($output, true);
	        imageinterlace($output, 1);
	        imagefill($output, 0, 0, imagecolorallocatealpha($output, 0, 0, 0, 127));
	        imagecopy($output, $img, 0, 0, $_GET["x"], $_GET["y"], $_GET["w"], $_GET["h"]);
	        if($type == 'jpeg'){
	        	eval("image".$type."(\$output,\$_GET['src'],100);");
	        }else{
	        	eval("image".$type."(\$output,\$_GET['src']);");
	        }
        }
		
		exit;

	}

	/**
	 * 測試SMTP
	 */
	if(isset($_GET["testSMTP"])){

		include_once(APP_PATH.'class/design.class.php');				//樣板模組
		include_once(APP_PATH.'class/PHPMailer.class.php');				//郵件模組
		include_once(APP_PATH.'class/validation.class.php');			//表單驗證
		include_once(APP_PATH.'class/webSetting.class.php');			//網站設定
		include_once(APP_PATH.'include/main.php');						//核心

		$design = new MTsung\design();
		$console = new MTsung\main($conn,$design,$setting);
		$webSetting = new MTsung\webSetting($console,PREFIX."web_setting",$_SESSION[FRAME_NAME]['language'.$console->langSessionName]);//前端輸出用
		$console->setWebSetting($webSetting);

		$mail = new MTsung\phpMailer($console);
		// $mail->SMTPDebug = 4;
		$mail->setMailTitle('信件寄送測試');
		$mail->setMailFrom($console->setting->getValue("senderEmail"),$console->setting->getValue("senderName"));
		$mail->setMailAddress($console->setting->getValue("recipientEmail"));
		$mail->Body = "測試測試";
		echo $mail->sendMail('','');
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
?>