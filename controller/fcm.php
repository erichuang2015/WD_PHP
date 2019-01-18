<?php
	include_once('header.php');

	$fcm = new MTsung\fcm($console);

	$type = "join";
	if(isset($console->path[1]) && $console->path[1]){
		$type = $console->path[1];
	}

	switch ($type) {
		case 'join':
			$fcm->setTopics("toby",$_GET["push_token"]);exit;
			// print_r($fcm->setTopics("toby",$_GET["push_token"]));exit;
			break;
		case 'leave':
			$fcm->setTopics("toby",$_GET["push_token"],false);exit;
			// print_r($fcm->setTopics("toby",$_GET["push_token"],false));exit;
			break;
		case 'send':
			// print_r($fcm->send("toby"));exit;
			break;
	}