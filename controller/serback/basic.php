<?php
$switch["buttonBox"] = 1;
$data["listUrl"] = $web_set['serback_url'].'/'.$console->path[0].'/'.$console->path[1];

if($console->path[1]=="product"){
	$basic = new MTsung\product($console,PREFIX.$console->path[1],$settingLang);
}else{
	$basic = new MTsung\dataList($console,PREFIX.$console->path[1],$settingLang);
}

//分類
if($console->conn->GetArray("desc ".PREFIX.$console->path[1]."_class__".str_replace("-","_",$settingLang))){
	$dataClass = new MTsung\dataClass($console,PREFIX.$console->path[1]."_class",$settingLang);
	$data["class"]["maxFloor"] = $dataClass->getMaxFloor();
	$data["class"]["list"] = $dataClass->getData("where status=1 order by step asc");
}

$designName = $console->path[1];

//欄位白名單 = 需要必填的欄位 = 需要轉陣列的欄位 = 搜尋key
$checkArray = $requiredArray = $explodeArray = $searchKey = array();

/**設定**/
switch ($console->path[1]) {

	case 'product':

	    if($_POST && isset($_POST["addProduct"]) && is_array($_POST["addProduct"])){
	    	$temp = array();
        	foreach ($_POST["addProduct"] as $key => $value) {
        		$temp[] = $value."|__|".$_POST["addProductSpecifications"][$key];
        		$temp1[] = $_POST["addProductMaxCount"][$key]."|__|".$_POST["addProductMoney"][$key];
        	}
        	unset($_POST["addProduct"],$_POST["addProductSpecifications"],$_POST["addProductMaxCount"],$_POST["addProductMoney"]);
        	$temp = array_unique($temp);
        	foreach ($temp as $key => $value) {
        		$_POST["addProduct"][] = explode("|__|",$value)[0];
        		$_POST["addProductSpecifications"][] = explode("|__|",$value)[1];
        		$_POST["addProductMaxCount"][] = explode("|__|",$temp1[$key])[0];
        		$_POST["addProductMoney"][] = explode("|__|",$temp1[$key])[1];
        	}
        }

		$requiredArray = array("name","class","specificationsID","specifications","stock","maxCount");
		$explodeArray = array("class","specificationsID","specifications","stock","addProduct","addProductSpecifications","maxCount","addProductMaxCount","addProductMoney");
		$searchKey = array("name");
		break;
	default:
		$explodeArray = array("class");
		$searchKey = array("name");
		break;
}
/**設定**/



/**模組**/
switch ($console->path[1]) {
	case 'product':
		
		$module["tinemceEditor"][0]["name"] = 'detail';
		$module["tinemceEditor"][0]["watermark"] = 0; //中間
		$module["tinemceEditor"][1]["name"] = 'memo';
		$module["uploadImg"][0]["name"] = "picture";
		$module["uploadImg"][0]["max"] = 1.5;
		$module["uploadImg"][0]["watermark"] = 0; //中間
		$module["uploadImg"][0]["textOther"] = array("Alt");
		$module["uploadImg"][0]["textOtherText"] = array($console->serbackLabel["ALT"]);

		break;
	default:
		
		$module["tinemceEditor"][0]["name"] = 'detail';
		$module["uploadImg"][0]["name"] = "picture";
		$module["uploadImg"][0]["max"] = 10;
		$module["uploadImg"][0]["watermark"] = '';
		$module["uploadImg"][0]["textOther"] = array("Title","Alt","Href");
		$module["uploadImg"][0]["textOtherText"] = array($console->serbackLabel["TITLE"],$console->serbackLabel["ALT"],$console->serbackLabel["URL"]);

		break;
}
/**模組**/











if(isset($module["uploadImg"])){
	foreach ($module["uploadImg"] as $key => $value) {
		$basic->addPictureName($value["name"]);
	}
}

if(isset($console->path[2])){
//動作
	switch ($console->path[2]) {
		case 'edit':
			//修改
			if(isset($console->path[3]) && is_numeric($console->path[3])){
				if($_POST){
					$_POST["id"] = $console->path[3];
					if($basic->setData($_POST,false,$checkArray,$requiredArray)){
						$console->alert($basic->message,$_SERVER["REQUEST_URI"]);
					}else{
						$console->alert($basic->message,-1);
					}
				}else{
					if($temp = $basic->getData("where id='".$console->path[3]."'")){
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
						$console->alert($basic->message,$data["listUrl"]);
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
			if($_POST){
				if($basic->setData($_POST,false,$checkArray,$requiredArray)){
					$console->alert($basic->message,$data["listUrl"]."?".$_SERVER["QUERY_STRING"]);
				}else{
					$console->alert($basic->message,-1);
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
				foreach ($_POST["checkElement"] as $key => $value) {
					$basic->rmData($value);
				}
				$console->alert($basic->message,$data["listUrl"]."?".$_SERVER["QUERY_STRING"]);
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
		if($basic->setDataAll($_POST)){
			$console->alert($basic->message,$_SERVER["REQUEST_URI"]);
		}else{
			$console->alert($basic->message,-1);
		}
	}

	if($data["list"] = $basic->getListData("order by sort ",$searchKey)){
		foreach ($data["list"] as $key => $value) {
			if(isset($data["list"][$key]["class"]) && $dataClass){
				$data["list"][$key]["class"] = explode("|__|",$value["class"]);
				$temp = array();
				foreach ($data["list"][$key]["class"] as $key1 => $value1) {
					$temp[] = $dataClass->getData("where id='".$value1."'")[0]["name"];
				}
				$data["list"][$key]["class"] = implode("/",$temp);
			}
		}
	}

	$data["pageNumber"] = $basic->pageNumber;

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
	$basic->copyAllLang();
	$console->alert($basic->message,$data["listUrl"]);
}


/**
 * 複製語系
 */
if(isset($_GET['copyLang'])){	
	$basic->copyLang($_GET['copyLang']);
	$console->alert($basic->message,$data["listUrl"]);
}

?>