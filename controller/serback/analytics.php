<?php
if(!$console->setting->getValue("analyticsCheck")){
	$console->alert("功能未開啟",-1);
}
$analytics = new MTsung\analytics($console);

//半年內流覽數
$monthArray = array("","Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
for ($i = -6; $i <= 0; $i++){
    $s = date('Y-m-01 00:00:00',strtotime($i." month"));
    $e = date('Y-m-01 00:00:00',strtotime(($i+1)." month"));
	$data["analytics"]["month"][] = date('Y-m',strtotime($i." month"));//$monthArray[(int)date('m',strtotime($i." month"))];
	$data["analytics"]["count"][] = $analytics->getTotalCount(false,$s,$e);
	$data["analytics"]["repeatCount"][] = $analytics->getTotalCount(true,$s,$e);
}


//裝置
$data["analytics"]["device"] = $analytics->getFieldCount("device");


//系統
$data["analytics"]["system"] = $analytics->getFieldCount("system");


//語系
$data["analytics"]["lang"] = $analytics->getFieldCount("lang");


//來源
$data["analytics"]["referer"] = $analytics->getFieldCount("referer");
if($data["analytics"]["referer"]){
	foreach ($data["analytics"]["referer"] as $key => $value) {
		if(!$value["name"]){
			unset($data["analytics"]["referer"][$key]);
		}
	}
	$data["analytics"]["referer"] = array_values($data["analytics"]["referer"]);
}


//24小時內的瀏覽數
for ($i = -23; $i <= 0; $i++){
    $s = date('Y-m-d H:00:00',strtotime($i." hour"));
    $e = date('Y-m-d H:00:00',strtotime(($i+1)." hour"));
	$data["analytics"]["hour"][] = date('H:00',strtotime($i." hour"));
	$data["analytics"]["count24H"][] = $analytics->getTotalCount(false,$s,$e);
	$data["analytics"]["repeatCount24H"][] = $analytics->getTotalCount(true,$s,$e);
}


//30天內的瀏覽數
for ($i = -30; $i <= 0; $i++){
    $s = date('Y-m-d 00:00:00',strtotime($i." day"));
    $e = date('Y-m-d 00:00:00',strtotime(($i+1)." day"));
	$data["analytics"]["day"][] = date('m/d',strtotime($i." day"));
	$data["analytics"]["countMonth"][] = $analytics->getTotalCount(false,$s,$e);
	$data["analytics"]["repeatCountMonth"][] = $analytics->getTotalCount(true,$s,$e);
}


//頁面點擊數
$data["analyticsPage"]["repeatUrl"] = $analytics->getFieldCountPage(true,"url");
usort($data["analyticsPage"]["repeatUrl"], "countSort");
$data["analyticsPage"]["repeatUrl"] = array_slice($data["analyticsPage"]["repeatUrl"],0,10);


//頁面點擊數
$data["analyticsPage"]["url"] = $analytics->getFieldCountPage(false,"url");
usort($data["analyticsPage"]["url"], "countSort");
$data["analyticsPage"]["url"] = array_slice($data["analyticsPage"]["url"],0,10);


//商品頁面點擊數
$data["analyticsPage"]["repeatUrlProduct"] = $analytics->getFieldCountPage(true,"url","","","product");
foreach ($data["analyticsPage"]["repeatUrlProduct"] as $key => $value) {
	if(count(explode("/", str_replace(WEB_PATH,"",$value["name"])))<4 || strpos($value["name"],".")!==false){//刪除分類頁
		unset($data["analyticsPage"]["repeatUrlProduct"][$key]);
	}
}
usort($data["analyticsPage"]["repeatUrlProduct"], "countSort");
$data["analyticsPage"]["repeatUrlProduct"] = array_slice($data["analyticsPage"]["repeatUrlProduct"],0,10);


//商品頁面點擊數
$data["analyticsPage"]["urlProduct"] = $analytics->getFieldCountPage(false,"url","","","product");
foreach ($data["analyticsPage"]["urlProduct"] as $key => $value) {
	if(count(explode("/", str_replace(WEB_PATH,"",$value["name"])))<4 || strpos($value["name"],".")!==false){//刪除分類頁
		unset($data["analyticsPage"]["urlProduct"][$key]);
	}
}
usort($data["analyticsPage"]["urlProduct"], "countSort");
$data["analyticsPage"]["urlProduct"] = array_slice($data["analyticsPage"]["urlProduct"],0,10);


//數量排序
function countSort($a,$b){
    if($a['count'] == $b['count']) return 0;
    return ($a['count'] < $b['count']) ? 1 : -1;
}

?>