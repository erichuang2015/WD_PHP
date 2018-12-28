<?php
/**
 * 硬碟空間圖表 (總網站,上傳資料夾,輸出資料夾,資料庫)
 */
$data['dataSize'] = array();
if($console->setting->getValue('sizeSwitch')=='1'){
	$data['dataSize'] = array(	$console->getDataSizeArray(APP_PATH,$console->setting->getValue("webMaxSize")),
								$console->getDataSizeArray(APP_PATH.UPLOAD_PATH,$console->setting->getValue("uploadMaxSize")),
								$console->getDataSizeArray(APP_PATH.OUTPUT_PATH,$console->setting->getValue("outputMaxSize")),
								$console->getSqlSizeArray('',$console->setting->getValue("sqlMaxSize"))
							);
}

// cpanel本月頻寬
// global $dbUser,$dbPass;
// $cPanel = new MTsung\cPanel($console,$dbUser,$dbPass);
// $data["bandwidth"] = 0;
// if($temp = $cPanel->getBandwidth("year_month","",strtotime(date('Y-m-01', strtotime(DATE))))){
// 	$data["bandwidth"] = $array_shift($temp);
// }

//最新消息
$ch = curl_init();
curl_setopt_array($ch,array(
	CURLOPT_URL => "www.104portal.com.tw/info.html",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_SSL_VERIFYPEER => false,
	CURLOPT_TIMEOUT => 4
));

$newData = strtotime(DATE);
if(isset($_SESSION["SERBACK_NEW"]) && $_SESSION["SERBACK_NEW"]){
	foreach ($_SESSION["SERBACK_NEW"] as $key => $value) {
		if($newData-$key > 3600*24*3){
			unset($_SESSION["SERBACK_NEW"][$key]);
			$_SESSION["SERBACK_NEW"][$newData] = curl_exec($ch);
		}
		$data["news"] = $data["info"] = $data["version"] = $value;
	}
}else{
	$data["news"] = $data["info"] = $data["version"] = $_SESSION["SERBACK_NEW"][$newData] = curl_exec($ch);
}
curl_close($ch);

$data["news"] = explode("網動消息與資訊</th>",$data["news"])[1];
$data["news"] = explode("</table>",$data["news"])[0];
$data["news"] = str_replace('align="center"',"",$data["news"]);

$data["info"] = explode("開發訊息</th>",$data["info"])[1];
$data["info"] = explode("</table>",$data["info"])[0];
$data["info"] = str_replace('align="center"',"",$data["info"]);

$data["version"] = explode("版本訊息</th>",$data["version"])[1];
$data["version"] = explode("</table>",$data["version"])[0];
$data["version"] = str_replace('align="center"',"",$data["version"]);
$data["version"] = str_replace('({if $info.version})({$info.version})({else})後台系統 PHP 5.3 版 (sv1.4)({/if})',"後台系統 PHP 7.0 版",$data["version"]);

?>