<?php

	include_once(APP_PATH.'include/adodb5/adodb.inc.php');// 文件 http://adodb.org/dokuwiki/doku.php
	
	//資料表前墜
	define('PREFIX','database_');

	//是否持續連線 
	$isPConnect = true;

	//資料庫資訊
	if(!isset($_SERVER["HTTP_HOST"]) || strpos($_SERVER["HTTP_HOST"],"localhost")!==false || strpos($_SERVER["HTTP_HOST"],"127.0.0.1")!==false){
		//本機
		$dbHost = "localhost";
		$dbUser = "root";
		$dbPass = "74512345";
		$dbData = "database";
	}else{
		//線上
		$dbHost = "localhost";
		$dbUser = "MTsung";
		$dbPass = "t7h;4i4T&6)z";
		$dbData = "database_MTsung";
	}


	$conn = ADONewConnection("mysqli");
	$connect_check = $isPConnect ? $conn->PConnect($dbHost,$dbUser,$dbPass,$dbData) : $conn->Connect($dbHost,$dbUser,$dbPass,$dbData);

	if(!$connect_check){
		//create database 資料庫名稱;
		print_r("資料庫連接失敗");exit;
	}else{
		//設定utf8mb4編碼
		$conn->Execute("SET NAMES utf8mb4;");
		$conn->Execute("SET CHARACTER_SET_CLIENT=utf8mb4;");
		$conn->Execute("SET CHARACTER_SET_RESULTS=utf8mb4;");
		$conn->Execute("SET CHARACTER_SET_CONNECTION=utf8mb4;");
		//時區
		$conn->Execute("SET GLOBAL time_zone = '+08:00';");
		$conn->Execute("SET time_zone = '+08:00';");

		//utf8轉utf8mb4
		// $tables = $conn->GetArray("SHOW TABLES");
		// foreach ($tables as $key => $value) {

		// 	$conn->Execute("ALTER TABLE `".$value[0]."` DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;");
		// 	$temp = $conn->GetArray("SHOW FULL FIELDS FROM ".$value[0]);
		// 	foreach ($temp as $key1 => $value1) {
		// 		if(strtolower($value1["Type"])=="varchar(255)"){
		// 			$conn->Execute("ALTER TABLE `".$value[0]."` MODIFY `".$value1["Field"]."` VARCHAR(191) COMMENT '".$value1["Comment"]."';");
		// 		}
		// 	}
		// 	$conn->Execute("ALTER TABLE `".$value[0]."` CONVERT TO CHARACTER SET utf8mb4;");
		// }
	}

?>