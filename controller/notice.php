<?php 
	include_once('header.php');
	$web_set["titlePrefix"] = "購物須知";
	$temp = new MTsung\dataList($console,PREFIX.$console->path[0],$lang);
	$data["one"] = $temp->getOne();
?>