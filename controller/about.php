<?php 
	include_once('header.php');
	$web_set["titlePrefix"] = "公司簡介";
	
	$temp = new MTsung\dataList($console,PREFIX.$console->path[0],$lang);
	$data["one"] = $temp->getOne();
?>