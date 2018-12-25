<?php

$switch["buttonBox"] = 1;
$data["listUrl"] = $web_set['serback_url'].'/'.$console->path[0].'/'.$console->path[1];
$data["basic"]["listUrl"] = $web_set['serback_url'].'/basic/'.$console->path[1];

$dataClass = new MTsung\dataClass($console,PREFIX.$console->path[1]."_class",$settingLang);
//目前最大層數
$data["maxFloor"] = $dataClass->getMaxFloor();

$designName = $console->path[1]."_class";

//欄位白名單 = 需要必填的欄位 = 需要轉陣列的欄位 = 搜尋key
$checkArray = $requiredArray = $explodeArray = $searchKey = array();
$searchKey = array("name");

/**設定**/
switch ($console->path[1]) {
	case 'news':

		$data["addMaxFloor"] = 0;
		break;
	default:

		//限制最大層數 (0開始算)
		$data["addMaxFloor"] = 0;
		break;
}
/**設定**/



/**模組**/
switch ($console->path[1]) {
	default:

		$module["tinemceEditor"][0]["name"] = 'detail';
		$module["uploadImg"][0]["name"] = "picture";
		$module["uploadImg"][0]["max"] = 10;
		$module["uploadImg"][0]["textOther"] = array("Title","Alt","Href");
		$module["uploadImg"][0]["textOtherText"] = array($console->getLabel("TITLE"),$console->getLabel("ALT"),$console->getLabel("URL"));
		break;
}
/**模組**/









if(isset($module["uploadImg"])){
	foreach ($module["uploadImg"] as $key => $value) {
		$dataClass->addPictureName($value["name"]);
	}
}

if(isset($console->path[2])){
//動作
	switch ($console->path[2]) {
		case 'edit':
			//修改　
			if(isset($console->path[3]) && is_numeric($console->path[3])){
				$data["list"] = $dataClass->getData("where parent<>'".$console->path[3]."' and id<>'".$console->path[3]."' order by step asc");

				if($_POST){
					$_POST["id"] = $console->path[3];
					$_POST["floor"] = explode(",", $_POST["parent"])[1] + 1;
					//最大層數限制
					if(isset($data["addMaxFloor"]) && $_POST["floor"]==$data["addMaxFloor"] && $dataClass->findChildren($_POST["id"])){
						$console->alert($console->getMessage("OVER_MAX_FLOOR",array($data["addMaxFloor"]+1)),-1);
						exit;
					}
					$_POST["parent"] = explode(",", $_POST["parent"])[0];
					if($dataClass->setData($_POST,false,$checkArray,$requiredArray)){
						$console->alert($dataClass->message,$_SERVER["REQUEST_URI"]);
					}else{
						$console->alert($dataClass->message,-1);
					}
				}else{
					$temp = $dataClass->getData("where id='".$console->path[3]."'");
					if($temp){
						$data["one"] = $temp[0];
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
					}else{
						$console->alert($dataClass->message,$data["listUrl"]);
					}
					unset($temp);
				}
			}else{
				//網址參數錯誤
				$console->alert($console->getMessage("NOT_AUTHORITY"),$data["listUrl"]);
			}

			$switch["saveButton"] = 1;
			$switch["backButton"] = 1;
			$switch["editList"] = 1;
			break;
		case 'add':
			//新增
			$data["list"] = $dataClass->getData("order by step asc");

			if($_POST){
				$_POST["floor"] = explode(",", $_POST["parent"])[1] + 1;
				$_POST["parent"] = explode(",", $_POST["parent"])[0];
				if($dataClass->setData($_POST,false,$checkArray,$requiredArray)){
					$console->alert($dataClass->message,$data["listUrl"]."?".$_SERVER["QUERY_STRING"]);
				}else{
					$console->alert($dataClass->message,-1);
				}
			}


			$switch["addButton"] = 1;
			$data["addOnClick"] = "formSubmit();";
			$switch["backButton"] = 1;
			$switch["addList"] = 1;
			break;
		case 'delete':
			//刪除
			if($_POST && isset($_POST["checkElement"])){
				$dataClass->rmData($_POST["checkElement"]);
				$console->alert($dataClass->message,$data["listUrl"]."?".$_SERVER["QUERY_STRING"]);
			}
		default:
			//網址參數錯誤
			$console->alert($console->getMessage("NOT_AUTHORITY"),$data["listUrl"]);
			break;
	}
}else{
//列表頁

	/**
	 * 修改全部
	 */
	if($_POST){
		if($dataClass->setDataAll($_POST)){
			$console->alert($dataClass->message,$_SERVER["REQUEST_URI"]);
		}else{
			$console->alert($dataClass->message,-1);
		}
	}

	$data["list"] = $dataClass->getListData("order by step asc",$searchKey);

	$data["pageNumber"] = $dataClass->pageNumber;

	$switch["deleteButton"] = 1;

	$switch["addButton"] = 1;
	$data["addOnClick"] = "window.location.href='".$data["listUrl"]."/add';";
	if($_SERVER["QUERY_STRING"]){
		$data["addOnClick"] = "window.location.href='".$data["listUrl"]."/add?".$_SERVER["QUERY_STRING"]."';";
	}

	$switch["saveButton"] = 1;
	$switch["listList"] = 1;
	$switch["searchBox"] = 1;
}


/**
 * 複製到所有語系
 */
if(isset($_GET['copyAllLang']) &&  ($_GET['copyAllLang'] === '1')){
	$dataClass->copyAllLang();
	$console->alert($dataClass->message,$data["listUrl"]);
}


/**
 * 複製語系
 */
if(isset($_GET['copyLang'])){	
	$dataClass->copyLang($_GET['copyLang']);
	$console->alert($dataClass->message,$data["listUrl"]);
}


?>