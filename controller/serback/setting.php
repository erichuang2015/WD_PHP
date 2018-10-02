<?php
/**
 * 網站設定
 */
$data["listUrl"] = $console->MT_web["serback_path"].'/'.$console->path[0].'/'.$console->path[1];

$_webSetting = new MTsung\webSetting($console,PREFIX.$console->path[1]."_setting",$settingLang);

$designName = $console->path[1]."_setting";

$switch["buttonBox"] = 1;
$switch["saveButton"] = 1;

/**模組**/
switch ($console->path[1]) {
	case "payment":
		break;
	case "shipment":
		$module["tinemceEditor"][0]["name"] = 'detail';
		break;
	default:
		break;
}
/**模組**/

/**
 * 取得資料
 */
$data["one"] = $_webSetting->getValue();

/**
 * 複製到所有語系
 */
if(isset($_GET['copyAllLang']) &&  ($_GET['copyAllLang'] === '1')){	
	if($_webSetting->copyAllLang()){
		$console->alert($_webSetting->message,$data["listUrl"]);
	}else{
		$console->alert($_webSetting->message,$data["listUrl"]);
	}
}

/**
 * 複製語系
 */
if(isset($_GET['copyLang'])){	
	if($_webSetting->copyLang($_GET['copyLang'])){
		$console->alert($_webSetting->message,$data["listUrl"]);
	}else{
		$console->alert($_webSetting->message,$data["listUrl"]);
	}
}

/**
 * 修改資料
 */
if($_POST){
	if(isset($_POST["emailCheck"])){
		$temp = new MTsung\member($console,PREFIX.'member','member');
		$temp->emailCheck($_POST["emailCheck"]);
	}
	if($_webSetting->setData($_POST)){
		$console->alert($_webSetting->message,$data["listUrl"]);
	}else{
		$console->alert($_webSetting->message,$data["listUrl"]);
	}
}


?>