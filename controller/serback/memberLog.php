<?php
$memberLog = new MTsung\center($console,PREFIX.$console->path[1].'_logs');

$array = array(	$console->serbackLabel["SIGN_UP"],
				$console->serbackLabel["LOGIN"],
				$console->serbackLabel["SOCIAL_Login"],
				$console->serbackLabel["LOGOUT"],
				$console->serbackLabel["MAIL_RECIVE"],
				$console->serbackLabel["FORGET"],
				$console->serbackLabel["DELETE"],
				$console->serbackLabel["ACCOUNT_ERROR"],
				$console->serbackLabel["PASSWORD_ERROR"],
				$console->serbackLabel["VERIFCODE_ERROR"]
			);
//搜尋key
$searchKey = array("id","account","IP");
$data["list"] = $memberLog->getListData("order by id desc",$searchKey);
	if($data["list"]){
	foreach ($data["list"] as $key => $value) {
		$data["list"][$key]["type"] = $array[$value["type"]];
	}
}

$data["pageNumber"] = $memberLog->pageNumber;
$switch["listList"] = 1;
$switch["searchBox"] = 1;



?>