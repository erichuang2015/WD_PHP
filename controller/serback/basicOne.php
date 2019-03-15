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
}
/**設定**/



/**模組**/
switch ($console->path[1]) {
}
/**模組**/


include_once(CONTROLLER_PATH.'serback/__about.php');


$switch["editList"] = 1;

if(isset($module["uploadImg"])){
	foreach ($module["uploadImg"] as $key => $value) {
		$basicOne->addPictureName($value["name"]);
	}
}

if($_POST){
	if($basicOne->getData("where id=?",array("1"))[0]){
		$_POST["id"] = 1;
		$_POST["sort"] = 0;//解決多筆轉單筆
	}
	if($basicOne->setData($_POST,false,$checkArray,$requiredArray)){
		$console->alert($basicOne->message,$_SERVER["REQUEST_URI"]);
	}else{
		$console->alert($basicOne->message,-1);
	}
}else{
	$data["one"] = $basicOne->getData("where id=?",array("1"),$explodeArray,$module)[0];
}

?>