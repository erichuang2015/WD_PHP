<?php

$switch["buttonBox"] = 1;
$switch["saveButton"] = 1;

$data["listUrl"] = $web_set['serback_url'].'/'.$console->path[0].'/'.$console->path[1];

$basicOne = new MTsung\dataList($console,PREFIX.$console->path[1],$settingLang);
if($console->conn->GetArray("desc ".PREFIX.$console->path[1]."_class__".str_replace("-","_",$settingLang))){
	$dataClass = new MTsung\dataClass($console,PREFIX.$console->path[1]."_class",$settingLang);
	$data["class"]["maxFloor"] = $dataClass->getMaxFloor();
	$data["class"]["list"] = $dataClass->getData("where status=1 order by step asc");
}

$designName = $console->path[1]."_one";

//欄位白名單 = 需要必填的欄位 = 需要轉陣列的欄位 = 搜尋key
$checkArray = $requiredArray = $explodeArray = $searchKey = array();


/**設定**/
switch ($console->path[1]) {
	case 'index_product':
	case 'index_news':

	    if($_POST && isset($_POST["detail"]) && is_array($_POST["detail"])){
        	$_POST["detail"] = array_unique($_POST["detail"]);
        }
		$explodeArray = array("detail");
		break;
	case 'contact':
		$data["typeOption"] = array(
			"text" => "FORM_TEXT",
			"textarea" => "FORM_TEXTAREA",
			"email" => "FORM_EMAIL",
			"password" => "FORM_PASSWORD",
			"address" => "FORM_ADDRESS",
			"date" => "FORM_DATE",
			"file" => "FORM_FILE",
			"select" => "FORM_SELECT",
			"radio" => "FORM_RADIO",
			"checkbox" => "FORM_CHECKBOX",
		);
		$explodeArray = array("dataName","dataType","dataOption","dataRequired");
		break;
	default:
		$explodeArray = array("class");
		$searchKey = array("name");
		break;
}
/**設定**/



/**模組**/
switch ($console->path[1]) {
	case 'edm':

		$module["tinemceEditor"][0]["name"] = 'detail';
		$module["uploadImg"][0]["name"] = "picture";
		$module["uploadImg"][0]["max"] = 10;
		$module["uploadImg"][0]["suggestText"] = "1920x576";
		// $module["uploadImg"][0]["textOther"] = array("Alt");
		// $module["uploadImg"][0]["textOtherText"] = array($console->serbackLabel["ALT"]);

		break;
	default:

		$module["tinemceEditor"][0]["name"] = 'detail';
		$module["uploadImg"][0]["name"] = "picture";
		$module["uploadImg"][0]["max"] = 10;
		$module["uploadImg"][0]["suggestText"] = $console->serbackLabel["ALL_SIZE"];
		$module["uploadImg"][0]["textOther"] = array("Title","Alt","Href");
		$module["uploadImg"][0]["textOtherText"] = array($console->serbackLabel["TITLE"],$console->serbackLabel["ALT"],$console->serbackLabel["URL"]);

		break;
}
/**模組**/












if(isset($module["uploadImg"])){
	foreach ($module["uploadImg"] as $key => $value) {
		$basicOne->addPictureName($value["name"]);
	}
}

if($_POST){
	if($basicOne->getData("where id='1'")[0]){
		$_POST["id"] = 1;
	}
	if($basicOne->setData($_POST,false,$checkArray,$requiredArray)){
		$console->alert($basicOne->message,$_SERVER["REQUEST_URI"]);
	}else{
		$console->alert($basicOne->message,-1);
	}
}else{
	$data["one"] = $basicOne->getData("where id='1'")[0];
	if(isset($module["uploadImg"])){
		foreach ($module["uploadImg"] as $key => $value) {
			if(isset($data["one"][$value["name"]])){
				$data["one"][$value["name"]] = explode("|__|", $data["one"][$value["name"]]);
			}

			if(isset($value["textOther"])){
				foreach ($value["textOther"] as $key1 => $value1) {
					if(isset($data["one"][$value["name"].$value1])){
						$data["one"][$value["name"].$value1] = json_encode(explode("|__|", $data["one"][$value["name"].$value1]));
					}
				}
			}
			if(isset($value["textareaOther"])){
				foreach ($value["textareaOther"] as $key1 => $value1) {
					if(isset($data["one"][$value["name"].$value1])){
						$data["one"][$value["name"].$value1] = json_encode(explode("|__|", $data["one"][$value["name"].$value1]));
					}
				}
			}
		}
	}

	if(isset($explodeArray)){
		foreach ($explodeArray as $key => $value) {
			if(($value != "") && !is_array($data["one"][$value])){
				$data["one"][$value] = explode("|__|", $data["one"][$value]);
			}
		}
	}
	
}


/**
 * 複製到所有語系
 */
if(isset($_GET['copyAllLang']) &&  ($_GET['copyAllLang'] === '1')){
	$basicOne->copyAllLang();
	$console->alert($basicOne->message,$data["listUrl"]);
}


/**
 * 複製語系
 */
if(isset($_GET['copyLang'])){	
	$basicOne->copyLang($_GET['copyLang']);
	$console->alert($basicOne->message,$data["listUrl"]);
}

?>