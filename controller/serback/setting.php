<?php
/**
 * 網站設定
 */
$data["listUrl"] = $web_set['serback_url'].'/'.$console->path[0].'/'.$console->path[1];

$_webSetting = new MTsung\webSetting($console,PREFIX.$console->path[1]."_setting",$settingLang);

include_once(CONTROLLER_PATH.'serback/__about.php');
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
if($explodeArray){//需要轉陣列的欄位
	foreach ($explodeArray as $valueE) {
		if(($valueE != "") && !is_array($data["one"][$valueE]) && $data["one"][$valueE]){
			$data["one"][$valueE] = explode("|__|", $data["one"][$valueE]);
		}
	}
}
if(isset($module["uploadImg"])){//後台用
	foreach ($module["uploadImg"] as $valueM) {
		if(isset($data["one"][$valueM["name"]])){
			$data["one"][$valueM["name"]] = explode("|__|", $data["one"][$valueM["name"]]);
		}

		if(isset($valueM["textOther"])){
			foreach ($valueM["textOther"] as $valueM1) {
				if(isset($data["one"][$valueM["name"].$valueM1])){
					$data["one"][$valueM["name"].$valueM1] = json_encode(explode("|__|", $data["one"][$valueM["name"].$valueM1]));
				}
			}
		}
		if(isset($valueM["textareaOther"])){
			foreach ($valueM["textareaOther"] as $valueM1) {
				if(isset($data["one"][$valueM["name"].$valueM1])){
					$data["one"][$valueM["name"].$valueM1] = json_encode(explode("|__|", $data["one"][$valueM["name"].$valueM1]));
				}
			}
		}
	}
}

if(isset($module["uploadFile"])){//後台用
	foreach ($module["uploadFile"] as $valueF) {
		if(isset($data["one"][$valueF["name"]])){
			$data["one"][$valueF["name"]] = explode("|__|", $data["one"][$valueF["name"]]);
		}

		if(isset($valueF["textOther"])){
			foreach ($valueF["textOther"] as $valueF1) {
				if(isset($data["one"][$valueF["name"].$valueF1])){
					$data["one"][$valueF["name"].$valueF1] = json_encode(explode("|__|", $data["one"][$valueF["name"].$valueF1]));
				}
			}
		}
		if(isset($valueF["textareaOther"])){
			foreach ($valueF["textareaOther"] as $valueF1) {
				if(isset($data["one"][$valueF["name"].$valueF1])){
					$data["one"][$valueF["name"].$valueF1] = json_encode(explode("|__|", $data["one"][$valueF["name"].$valueF1]));
				}
			}
		}
	}
}
if(isset($module["uploadImg"])){
	foreach ($module["uploadImg"] as $key => $value) {
		$_webSetting->addPictureName($value["name"]);
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