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

/**推播**/
if(isset($_GET["push"]) && isset($_GET["group"]) && isset($_GET["title"]) && isset($_GET["body"])){
	$fcm = new MTsung\fcm($console);
	$fcm->send($_GET["group"],$_GET["title"],$_GET["body"],$_GET["icon"],$_GET["url"]);
	$console->outputJson(true,"");
}

/**設定**/
switch ($console->path[1]) {
	case 'redirect':
		$basic = new MTsung\dataList($console,PREFIX.$console->path[1],"");
		break;
	case 'product':

	    if($_POST && isset($_POST["addProduct"]) && is_array($_POST["addProduct"])){//加價購 刪除同商品同規格
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
}
/**設定**/


/**模組**/
switch ($console->path[1]) {
	case '範例':
		$module["tinemceEditor"][0]["name"] = 'detail';

		$module["uploadImg"][0]["name"] = "picture";//欄位名稱
		$module["uploadImg"][0]["max"] = 10;//限制數量
		$module["uploadImg"][0]["watermark"] = '';//浮水印
		$module["uploadImg"][0]["textOther"] = array("Title","Alt","Href");//欄位名稱
		$module["uploadImg"][0]["textOtherText"] = array($console->getLabel("TITLE"),$console->getLabel("ALT"),$console->getLabel("URL"));//提示字
		$module["uploadImg"][0]["textareaOther"] = array("Detail");//欄位名稱
		$module["uploadImg"][0]["textareaOtherText"] = array($console->getLabel("DETAIL"));//提示字
		$module["uploadImg"][0]["suggestText"] = "1920x576";//建議尺寸


		$module["uploadFile"][0]["name"] = "file";//欄位名稱
		$module["uploadFile"][0]["max"] = 1.5;//限制數量
		$module["uploadFile"][0]["suggestText"] = "限制";//建議尺寸
		$module["uploadFile"][0]["extension"] = array("jpg");//限制附檔名
}
/**模組**/

//載入功能
if(!in_array($console->path[1],array("coupon"))){
	include_once(CONTROLLER_PATH.'serback/__about.php');
}



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
					if($temp = $basic->getData("where id='".$console->path[3]."'",array(),$explodeArray,$module)){
						$data["one"] = $temp[0];
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

			//copy
			if(isset($console->path[3]) && is_numeric($console->path[3])){
				$temp = $basic->getData("where id='".$console->path[3]."'",array(),$explodeArray,$module);
				if($temp){
					$data["one"] = $temp[0];
				}else{
					$console->alert($basic->message,$data["listUrl"]);
				}
				unset($temp);
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

/**設定**/
switch ($console->path[1]) {
	case 'coupon'://優惠卷
		if(!$data["one"]["detail"]){
			$data["one"]["detail"] = strtoupper(base_convert(rand().".".rand(),10,36));
		}
	break;
}
/**設定**/