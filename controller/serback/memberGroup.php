<?php
$switch["buttonBox"] = 1;
$data["listUrl"] = $web_set['serback_url'].'/'.$console->path[0];

$basic = new MTsung\memberGroup($console,PREFIX.'member_group');

$designName = $console->path[0];

//欄位白名單 = 需要必填的欄位 = 需要轉陣列的欄位 = 搜尋key
$checkArray = $requiredArray = $explodeArray = $searchKey = array();

/**模組**/
// $module["tinemceEditor"][0]["name"] = 'detail';

// $module["uploadImg"][0]["name"] = "picture";//欄位名稱
// $module["uploadImg"][0]["max"] = 10;//限制數量
// $module["uploadImg"][0]["watermark"] = '';//浮水印
// $module["uploadImg"][0]["textOther"] = array("Title","Alt","Href");//欄位名稱
// $module["uploadImg"][0]["textOtherText"] = array($console->getLabel("TITLE"),$console->getLabel("ALT"),$console->getLabel("URL"));//提示字
// $module["uploadImg"][0]["textareaOther"] = array("Detail");//欄位名稱
// $module["uploadImg"][0]["textareaOtherText"] = array($console->getLabel("DETAIL"));//提示字
// $module["uploadImg"][0]["suggestText"] = "1920x576";//建議尺寸


// $module["uploadFile"][0]["name"] = "file";//欄位名稱
// $module["uploadFile"][0]["max"] = 1.5;//限制數量
// $module["uploadFile"][0]["suggestText"] = "限制";//建議尺寸
// $module["uploadFile"][0]["extension"] = array("jpg");//限制附檔名
/**模組**/

if(isset($module["uploadImg"])){
	foreach ($module["uploadImg"] as $key => $value) {
		$basic->addPictureName($value["name"]);
	}
}

if(isset($console->path[1])){
//動作
	switch ($console->path[1]) {
		case 'edit':
			//修改
			if(isset($console->path[2]) && is_numeric($console->path[2])){
				if($_POST){
					$_POST["id"] = $console->path[2];
					if($basic->setData($_POST,false,$checkArray,$requiredArray)){
						$console->alert($basic->message,$_SERVER["REQUEST_URI"]);
					}else{
						$console->alert($basic->message,-1);
					}
				}else{
					if($temp = $basic->getData("where id='".$console->path[2]."'",array(),$explodeArray,$module)){
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
			if(isset($console->path[2]) && is_numeric($console->path[2])){
				$temp = $basic->getData("where id='".$console->path[2]."'",array(),$explodeArray,$module);
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


	if($data["list"] = $basic->getListData("order by upMoney,id ",$searchKey)){
		foreach ($data["list"] as $key => $value) {
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

?>