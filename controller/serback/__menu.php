<?php

$switch["buttonBox"] = 1;
$data["listUrl"] = $web_set['serback_url'].'/'.$console->path[0];


$systemMenu = new MTsung\menu($console,PREFIX."menu");

$explodeArray = array(
	"dataName",
	"dataType",
	"dataOption",
	"dataRequired",
	"dataKey",
	"dataCount",
	"dataExtension",
	"dataSearch",
	"dataSuggestText",
	"dataTextOther",
	"dataTextOtherText",
	"dataTextareaOther",
	"dataTextareaOtherText",
	"dataSearchCount",
);

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
$ReservedWord= array("id","name","urlKey","originalPrice","specialPrice","memberPrice","specificationsID","specifications","stock","maxCount","addProduct","addProductSpecifications","addProductMaxCount","addProductMoney","suggestProduct","sort","status","create_date","update_date","create_user","update_user","pictureAlt","pageTitle","pageMeta");
if(isset($_POST["dataKey"])){
	foreach ($_POST["dataKey"] as $key => $value) {
		if(in_array($value,$ReservedWord)){
			$console->alert($console->getMessage("RESERVED_WORD",array($value)),-1);
		}
	}
}

//功能
$data["featuresList"] = array(
	"_null" => "無",
	"_other_basicOne" => "單筆資料型",
	"_other_basic" => "多筆資料型",
	"_other_class" => "分類型",
	"_other_form" => "表單資料",
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
	"color" => "顏色選擇欄位",
	"date" => "日期選擇欄位",
	"aceEditor" => "HTML編輯器",
	"imageModule" => "上傳圖片模組",
	"fileModule" => "上傳檔案模組",
	"googleMap" => "google地圖",
	"search" => "搜尋模組",
);
