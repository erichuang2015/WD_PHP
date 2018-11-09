<?php

$analytics = new MTsung\analytics($console);


//半年內流覽數
$monthArray = array("","Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
for ($i=(DATE_M-5); $i <=DATE_M ; $i++) {
	$s = DATE_Y."-".$i."-01 ";
	$e = DATE_Y."-".($i+1)."-01 ";
	$data["analytics"]["month"][] = $monthArray[$i];
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
for ($i=date('H')+1; $i < date('H')+25 ; $i++) {
	$s = DATE_Y."-".DATE_M."-".(DATE_D-($i>23?0:1))." ".($i%24).":00:00";
	$e = DATE_Y."-".DATE_M."-".(DATE_D-(($i+1)>23?0:1))." ".(($i+1)%24).":00:00";
	$data["analytics"]["hour"][] = ($i%24).":00";
	$data["analytics"]["count24H"][] = $analytics->getTotalCount(false,$s,$e);
	$data["analytics"]["repeatCount24H"][] = $analytics->getTotalCount(true,$s,$e);
}


//30天內的瀏覽數
for ($i=29; $i >=-1 ; $i--) { 
	$month = date("m", strtotime('-'.($i+1).' day'));
	$month1 = date("m", strtotime('-'.$i.' day'));
	$day = date("d", strtotime('-'.($i+1).' day'));
	$day1 = date("d", strtotime('-'.$i.' day'));
	$s = DATE_Y."-".$month."-".$day." 00:00:00";
	$e = DATE_Y."-".$month1."-".$day1." 00:00:00";
	$data["analytics"]["day"][] = $month."/".$day;
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