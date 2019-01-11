<?php
$switch["buttonBox"] = 1;
$data["listUrl"] = $web_set['serback_url'].'/'.$console->path[0];

$basic = new MTsung\dataList($console,PREFIX.$console->path[0],$settingLang);

$designName = $console->path[0];

//欄位白名單 = 需要必填的欄位 = 需要轉陣列的欄位 = 搜尋key
$checkArray = $requiredArray = $explodeArray = $searchKey = array();
$searchKey = array("name","subDomain","addonDomain");

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
					if($temp = $basic->getData("where id=?",array($console->path[2]),$explodeArray,$module)){
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
				global $dbUser,$dbPass;
				$cPanel = new MTsung\cPanel($console,$dbUser,$dbPass);
				if($_POST["subDomain"]){
					if(!$cPanel->addDatabase($_POST["subDomain"])){
						$console->alert($cPanel->message,-1);
					}
					if(!$cPanel->addSubDomain($_POST["subDomain"])){
						$console->alert($cPanel->message,-1);
					}
					if($_POST["addonDomain"]){
						if(!$cPanel->addAddonDomain($_POST["subDomain"],$_POST["addonDomain"])){
							$console->alert($cPanel->message,-1);
						}
					}
				}else{
					$console->alert($console->getMessage("NOT_AUTHORITY"),-1);
				}
				
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

	$data["list"] = $basic->getListData("order by sort ",$searchKey);


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