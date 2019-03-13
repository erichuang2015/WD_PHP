<?php
$switch["buttonBox"] = 1;
$data["listUrl"] = $web_set['serback_url'].'/'.$console->path[0];

$systemMenu = new MTsung\menu($console,PREFIX."menu");
//功能_
$data["featuresList"] = array(
	"1" => "單筆文章型",// 內容 頁面設定
	"2" => "有分類多筆文章型",//分類 標題 上下架 內容 圖片 頁面設定
	"3" => "無分類多筆文章型",//標題 上下架 內容 圖片 頁面設定
	"4" => "聯絡表單型",//內容 map 頁面設定
	"5" => "自訂排板",//自訂排板
);

$basic = new MTsung\dataList($console,PREFIX."menuAuto","");

$designName = "menuAuto";

//欄位白名單 = 需要必填的欄位 = 需要轉陣列的欄位 = 搜尋key
$checkArray = $requiredArray = $explodeArray = $searchKey = array();


if(isset($console->path[1])){
//動作
	switch ($console->path[1]) {
		case 'edit':
			//修改
			if(isset($console->path[2]) && is_numeric($console->path[2])){
				if($_POST){
					$_POST["id"] = $console->path[2];
					if($basic->setData($_POST,false,$checkArray,$requiredArray)){
						getInputData($systemMenu,$_POST["id"]);
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
				if($temp = $basic->setData($_POST,false,$checkArray,$requiredArray)){
					getInputData($systemMenu,$temp);
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
					$systemMenu->conn->Execute("delete from ".$systemMenu->table." where autoId='".$value."'");
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


function getInputData($systemMenu,$autoId=''){
	if($autoId){
		$temp = $systemMenu->getData("where autoId=?",array($autoId));
	}
	$inputData["name"] = $_POST["name"];
	$inputData["alias"] = $_POST["alias"];//別名
	$inputData["pageSetting"] = 1;//頁面設定
	$inputData["formData"] = 0;//表單資料
	$inputData["releaseAndExpire"] = 0;//上下架功能
	$inputData["count"] = 12;//顯示筆數
	$inputData["floor"] = 2;
	$inputData["parent"] = 96;//頁面管理menu id
	$inputData["autoId"] = $autoId;

	switch ($_POST["features"]) {
		case '1'://單筆文章型
			if($temp){
				$inputData["id"] = $temp[0]["id"];
			}
			$inputData["features"] = "_other_basicOne";
			$inputData["dataName"][0] = "內容";
			$inputData["dataKey"][0] = "detail";
			$inputData["dataType"][0] = "aceEditor";
			$inputData["url"] = "basicOne/".$inputData["alias"];
			$addId = $systemMenu->setData($inputData);
			break;
		
		case '2'://有分類多筆文章型
			if($temp){
				foreach ($temp as $key => $value) {
					if($value["features"]=="_other_class"){
						$inputData["id"] = $temp[0]["id"];
						$inputData["features"] = "_other_class";
						$inputData["dataName"][0] = "內容";
						$inputData["dataKey"][0] = "detail";
						$inputData["dataType"][0] = "textarea";
						$inputData["url"] = "class/".$inputData["alias"];
						$addId = $systemMenu->setData($inputData);

					}else if($value["features"]=="_other_basic"){
						$inputData["id"] = $temp[0]["id"];
						$inputData["releaseAndExpire"] = 1;//上下架功能
						$inputData["features"] = "_other_basic";
						$inputData["dataName"][0] = "內容";
						$inputData["dataKey"][0] = "detail";
						$inputData["dataType"][0] = "aceEditor";
						$inputData["url"] = "basic/".$inputData["alias"];
						$addId = $systemMenu->setData($inputData);
					}
				}
			}else{
				$inputData["releaseAndExpire"] = 1;//上下架功能
				$inputData["features"] = "_other_basic";
				$inputData["dataName"][0] = "內容";
				$inputData["dataKey"][0] = "detail";
				$inputData["dataType"][0] = "aceEditor";
				$inputData["url"] = "basic/".$inputData["alias"];
				$addId = $systemMenu->setData($inputData);
				if($addId && ($addId !== true)){
					$systemMenu->console->conn->Execute("UPDATE ".PREFIX.'admin_group'." SET auth=CONCAT(auth,',".$addId."') where id=1 or id=2");
				}

				$inputData["releaseAndExpire"] = 0;//上下架功能
				$inputData["features"] = "_other_class";
				$inputData["dataName"][0] = "內容";
				$inputData["dataKey"][0] = "detail";
				$inputData["dataType"][0] = "textarea";
				$inputData["url"] = "class/".$inputData["alias"];
				$addId = $systemMenu->setData($inputData);
			}

			break;
		
		case '3'://無分類多筆文章型
			if($temp){
				$inputData["id"] = $temp[0]["id"];
			}
			$inputData["releaseAndExpire"] = 1;//上下架功能
			$inputData["features"] = "_other_basic";
			$inputData["dataName"][0] = "內容";
			$inputData["dataKey"][0] = "detail";
			$inputData["dataType"][0] = "aceEditor";
			$inputData["url"] = "basic/".$inputData["alias"];
			$addId = $systemMenu->setData($inputData);

			break;
		
		case '4'://聯絡表單型


			if($temp){
				foreach ($temp as $key => $value) {
					if($value["features"]=="_other_basicOne"){
						$inputData["id"] = $temp[0]["id"];
						$inputData["formData"] = 1;//表單資料
						$inputData["features"] = "_other_basicOne";
						$inputData["dataName"][0] = "內容";
						$inputData["dataKey"][0] = "detail";
						$inputData["dataType"][0] = "aceEditor";
						$inputData["dataName"][1] = "google地圖";
						$inputData["dataKey"][1] = "googleMap";
						$inputData["dataType"][1] = "googleMap";
						$inputData["url"] = "basicOne/".$inputData["alias"];
						$addId = $systemMenu->setData($inputData);


					}else if($value["features"]=="_other_form"){
						$inputData["id"] = $temp[0]["id"];
						$inputData["parent"] = 97;
						$inputData["features"] = "_other_form";
						$inputData["url"] = "form/".$inputData["alias"];
						$addId = $systemMenu->setData($inputData);
					}
				}
			}else{
				$inputData["formData"] = 1;//表單資料
				$inputData["features"] = "_other_basicOne";
				$inputData["dataName"][0] = "內容";
				$inputData["dataKey"][0] = "detail";
				$inputData["dataType"][0] = "aceEditor";
				$inputData["dataName"][1] = "google地圖";
				$inputData["dataKey"][1] = "googleMap";
				$inputData["dataType"][1] = "googleMap";
				$inputData["url"] = "basicOne/".$inputData["alias"];
				$addId = $systemMenu->setData($inputData);
				if($addId && ($addId !== true)){
					$systemMenu->console->conn->Execute("UPDATE ".PREFIX.'admin_group'." SET auth=CONCAT(auth,',".$addId."') where id=1 or id=2");
				}

				$inputData["parent"] = 97;
				$inputData["features"] = "_other_form";
				$inputData["url"] = "form/".$inputData["alias"];
				$addId = $systemMenu->setData($inputData);
			}

			break;
	}
	if($addId && ($addId !== true)){
		$systemMenu->console->conn->Execute("UPDATE ".PREFIX.'admin_group'." SET auth=CONCAT(auth,',".$addId."') where id=1 or id=2");
	}
}