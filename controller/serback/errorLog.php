<?php
ini_set("memory_limit",-1);

$module["aceEditor"]["name"] = '_aceEditor';//POST欄位名稱，不可使用"aceEditor"

if($_POST){
	$allowUser = array("vipadmin");
	if(!in_array($member->getInfo("account"),$allowUser)){
		$console->alert($console->getMessage("NOT_AUTHORITY"),-1);
		exit;
	}
	file_put_contents(ini_get("error_log"),$_POST[$module["aceEditor"]["name"]]);
	$console->alert("OK",$_SERVER["REQUEST_URI"]);
}

// file_put_contents(ini_get("error_log"),"");
$data[$module["aceEditor"]["name"]] = "";
if(is_file(ini_get("error_log"))){
	$data[$module["aceEditor"]["name"]] = htmlspecialchars(file_get_contents(ini_get("error_log")));
}