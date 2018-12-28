<?php

$switch["buttonBox"] = 1;
$data["listUrl"] = $web_set['serback_url'].'/'.$console->path[0];


$systemMenu = new MTsung\menu($console,PREFIX."menu");

$explodeArray = array("dataName","dataType","dataOption","dataRequired","dataKey","dataCount","dataExtension");

//目前最大層數
$data["maxFloor"] = $systemMenu->getMaxFloor();

//限制最大層數 (0開始算)
$data["addMaxFloor"] = 2;

if(isset($_POST["name"])){
	$_POST["name"] = strtoupper($_POST["name"]);
}

//有權限操作的id
$start = $systemMenu->getData("where id='1'")[0]["step"];//前台id必須為1
$end = $systemMenu->getData("order by floor,id limit 1,1")[0]["step"];
$data["allow"] = $systemMenu->getData("where step<'".$end."' and step>'".$start."' order by step");
$allowIDs = array();
if($data["allow"]){
	foreach ($data["allow"] as $key => $value) {
		$allowIDs[] = $value["id"];
	}
}

//保留字
$ReservedWord= array("id","urlKey","originalPrice","specialPrice","memberPrice","specificationsID","specifications","stock","maxCount","addProduct","addProductSpecifications","addProductMaxCount","addProductMoney","suggestProduct","sort","status","create_date","update_date","create_user","update_user","pictureAlt","pageTitle","pageMeta");
if(isset($_POST["dataKey"])){
	foreach ($_POST["dataKey"] as $key => $value) {
		if(in_array($value,$ReservedWord)){
			$console->alert($console->getMessage("RESERVED_WORD",array($value)),-1);
		}
	}
}

//功能
$data["featuresList"] = array(
	"_other_basic" => "單筆資料型",
	"_other_basicOne" => "多筆資料型",
	"_other_class" => "分類型",
	"_other_form" => "表單資料型",
	"class/product" => "商品分類",
	"basic/product" => "商品",
	"member" => "會員列表",
	"order" => "訂單列表",
	"memberLog/member" => "帳號紀錄",
	"setting/web" => "網站設定",
	"setting/payment" => "付款方式設定",
	"setting/shipment" => "運送方式設定",
);


//功能欄位
$data["typeOption"] = array(
	"text" => "單行文字欄位",
	"textarea" => "多行文字欄位",
	"date" => "日期選擇欄位",
	"aceEditor" => "HTML編輯器",
	"imageModule" => "上傳圖片模組",
	"fileModule" => "上傳檔案模組",
	"googleMap" => "google地圖",
);



if(isset($console->path[1])){
//動作
	switch ($console->path[1]) {
		case 'edit':
			//修改
			if(isset($console->path[2]) && is_numeric($console->path[2]) && in_array($console->path[2],$allowIDs,true)){
				$allowIDs[] = "1";
				$data["list"] = $systemMenu->getData("where parent<>'".$console->path[2]."' and id<>'".$console->path[2]."' and id in ('".implode("','", $allowIDs)."')  order by step asc");

				if($_POST){					
					$_POST["id"] = $console->path[2];
					$_POST["floor"] = explode(",", $_POST["parent"])[1] + 1;
					$_POST["parent"] = explode(",", $_POST["parent"])[0];

					if(!in_array($_POST["parent"],$allowIDs,true) || !isset($data["featuresList"][$_POST["features"]])){
						$console->alert($console->getMessage("NOT_AUTHORITY"),$data["listUrl"]);
					}

					$temp = explode("_other_",$_POST["features"]);

					if(isset($temp[1])){
						$_POST["url"] = $temp[1]."/".$_POST["alias"];
					}else{
						$_POST["url"] = $_POST["features"];
					}

					if($systemMenu->setData($_POST)){
						$console->alert($systemMenu->message,$_SERVER["REQUEST_URI"]);
					}else{
						$console->alert($systemMenu->message,-1);
					}
				}else{
					$temp = $systemMenu->getData("where id='".$console->path[2]."'");
					if($temp){
						$data["one"] = $temp[0];
					}else{
						$console->alert($systemMenu->message,$data["listUrl"]);
					}
					if(isset($explodeArray)){
						foreach ($explodeArray as $key => $value) {
							if(($value != "") && !is_array($data["one"][$value]) && $data["one"][$value]){
								$data["one"][$value] = explode("|__|", $data["one"][$value]);
							}
						}
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
			$allowIDs[] = "1";
			$data["list"] = $systemMenu->getData("where id in ('".implode("','", $allowIDs)."') order by step asc");
			if($_POST){
				$_POST["floor"] = explode(",", $_POST["parent"])[1] + 1;
				$_POST["parent"] = explode(",", $_POST["parent"])[0];

				if(!in_array($_POST["parent"],$allowIDs,true)){
					$console->alert($console->getMessage("NOT_AUTHORITY"),$data["listUrl"]);
				}

				if($systemMenu->setData($_POST)){
					$console->alert($systemMenu->message,$data["listUrl"]);
				}else{
					$console->alert($systemMenu->message,-1);
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
					if(in_array($value,$allowIDs)){
						$systemMenu->rmData(array($value));
					}
				}
				$console->alert($systemMenu->message,$data["listUrl"]);
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
		if($systemMenu->setDataAll($_POST)){
			$console->alert($systemMenu->message,$_SERVER["REQUEST_URI"]);
		}else{
			$console->alert($systemMenu->message,-1);
		}
	}

	$searchKey = array("name");
	$data["list"] = $systemMenu->getListData("and id in ('".implode("','", $allowIDs)."') order by step asc",$searchKey,50);


	$data["pageNumber"] = $systemMenu->pageNumber;

	$switch["deleteButton"] = 1;

	$switch["addButton"] = 1;
	$data["addOnClick"] = "window.location.href='".$web_set['serback_url'].'/'.$console->path[0]."/add';";

	$switch["saveButton"] = 1;
	$switch["listList"] = 1;
	// $switch["searchBox"] = 1;
}



?>