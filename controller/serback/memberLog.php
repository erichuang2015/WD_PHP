<?php
$memberLog = new MTsung\center($console,PREFIX.$console->path[1].'_logs');

$array = array(	$console->getLabel("SIGN_UP"),
				$console->getLabel("LOGIN"),
				$console->getLabel("SOCIAL_Login"),
				$console->getLabel("LOGOUT"),
				$console->getLabel("MAIL_RECIVE"),
				$console->getLabel("FORGET"),
				$console->getLabel("DELETE"),
				$console->getLabel("ACCOUNT_ERROR"),
				$console->getLabel("PASSWORD_ERROR"),
				$console->getLabel("VERIFCODE_ERROR")
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