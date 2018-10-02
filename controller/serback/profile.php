<?php

$switch["buttonBox"] = 1;
$switch["saveButton"] = 1;

$module["uploadImg"][0]["name"] = "picture";
$module["uploadImg"][0]["max"] = 1;
$data[$module["uploadImg"][0]["name"]] = $memberInfo[$module["uploadImg"][0]["name"]];


/**
 * 修改會員資料
 */
if($_POST){
	$member->setUser($memberInfo["id"],$_POST,array('name','email','newPassword','checkNewPassword','picture','doingTime'),array('name'));
	$console->alert($member->message,$console->path[0]);
}


/**
 * 社群link $_GET["socialLogin"] = (fb,ling,google)
 */
if(isset($_GET["socialLogin"]) && isset($_SESSION[FRAME_NAME][strtoupper($_GET['socialLogin']).'_LOGIN'])){
	if($member->link($memberInfo["id"],$_GET["socialLogin"],$_SESSION[FRAME_NAME][strtoupper($_GET['socialLogin']).'_LOGIN'])){
		unset($_SESSION[FRAME_NAME][strtoupper($_GET['socialLogin']).'_LOGIN']);
	}
	$console->alert($member->message,$console->path[0].'?tab=link');
}

/**
 * 社群unlink $_GET["unLink"] = (fb,ling,google)
 */
if(isset($_GET["unLink"])){
	$member->unLink($memberInfo["id"],$_GET["unLink"]);
	$console->alert($member->message,$console->path[0].'?tab=link');
}
?>