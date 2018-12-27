<?php
/**
 * 網站設定
 */
$data["listUrl"] = $web_set['serback_url'].'/'.$console->path[0].'/'.$console->path[1];

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

$data["one"] = $_webSetting->getValue();

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