<?php
	include_once(APP_PATH.'include/adodb5/adodb.inc.php');// 文件 http://adodb.org/dokuwiki/doku.php
	
	//資料表前墜
	define('PREFIX','database_');

	//資料庫前墜
	define('DB_PREFIX','wdtaiwan_');

	//主網server name 
	define('MAIN_SERVER_NAME','');

	//是否持續連線 
	$isPConnect = true;

	//資料庫資訊
	if(!isset($_SERVER["SERVER_NAME"]) || strpos($_SERVER["SERVER_NAME"],"localhost")!==false || $_SERVER["SERVER_NAME"]=="127.0.0.1"){
		//本機
		$dbHost = "localhost";
		$dbUser = "root";
		$dbPass = "74512345";
		$dbData = "system";
	}else{
		//線上
		$dbHost = "localhost";
		$dbUser = "";
		$dbPass = "";
		$dbData = DB_PREFIX."toby";

		if(!$dbUser){
			$dbUser = $_GET["dbUser"];
			$dbPass = $_GET["dbPass"];
			$dbData = $_GET["dbData"];
		}
	}

	//網站資料夾權限判斷
	if(!is_writeable(APP_PATH)){
		echo "unable to write file";
		exit;
	}


	$conn = ADONewConnection("mysqli");
	$connect_check = $isPConnect ? $conn->PConnect($dbHost,$dbUser,$dbPass,$dbData) : $conn->Connect($dbHost,$dbUser,$dbPass,$dbData);

	if(!$connect_check){
		if(isset($_GET["setup"])){//安裝
			$conn->close();
			$conn = ADONewConnection("mysqli");
			if(!$conn->PConnect($dbHost,$dbUser,$dbPass)){
				echo "Database connection failed.".$conn->errorMsg();
				exit;
			}

			$conn->Execute("create database ".$dbData." default character set utf8mb4 collate utf8mb4_general_ci");
			$conn->Connect($dbHost,$dbUser,$dbPass,$dbData);

			ignore_user_abort(true);
			set_time_limit(0);
			ini_set("memory_limit",-1);
			$conn->Execute("set global max_allowed_packet=200*1024*1024; ");
			$fileName = APP_PATH.'config/setup.sql';
			$errorFlag = false;
			if(is_file($fileName)){
				$file = file($fileName);
				$sql = "";
				foreach ($file as $key => $value) {
					$value = str_replace("\n","", str_replace("\r","",$value));
					if(substr($value, 0, 2) == '--' || $value == ''){
						continue;
					}
					$sql .= $value;
					if($value && $value[strlen($value)-1] == ";"){
						$conn->Execute($sql);
						if($conn->errorMsg()){
							echo "<b>Error</b>: ".$conn->errorMsg().". in <b>".$fileName."</b> on line <b>".($key+1)."</b><br><br>";
							$errorFlag = true;
						}
						$sql = "";
					}
				}
				if(!$errorFlag){
					header("Location:".HTTP_PATH);
				}
			}else{
				echo "Setup error.";
			}
			$conn->close();
			exit;
		}
		echo "Database connection failed.";
		exit;
	}

	//關閉嚴格模式
	$conn->Execute("SET sql_mode = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';");
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

	$dataPath = "";
	if(MAIN_SERVER_NAME && $_SERVER["SERVER_NAME"]!=MAIN_SERVER_NAME){
		if(!$temp = $conn->GetRow("select * from ".PREFIX."subsidiary where addonDomain like '%".str_replace('www.','',$_SERVER['SERVER_NAME'])."%' or subDomain like '%".str_replace('.'.MAIN_SERVER_NAME,'',$_SERVER['SERVER_NAME'])."%' and status=1")){
			echo "subsidiary null.";
			exit;
		}
		$dataPath = "data/".($temp["id"]+10000)."/";
		$dbData = DB_PREFIX.$temp["subDomain"];

		$connect_check = $isPConnect ? $conn->PConnect($dbHost,$dbUser,$dbPass,$dbData) : $conn->Connect($dbHost,$dbUser,$dbPass,$dbData);
		if(!$connect_check){
			echo "Database connection failed.";
			exit;
		}
	}

	define('DATA_PATH',$dataPath);
	if(!is_dir(APP_PATH.DATA_PATH)) mkdir(APP_PATH.DATA_PATH);
	
	define('UPLOAD_PATH',DATA_PATH.'upload/');
	if(!is_dir(APP_PATH.UPLOAD_PATH)) mkdir(APP_PATH.UPLOAD_PATH);

	define('OUTPUT_PATH',DATA_PATH.'output/');
	if(!is_dir(APP_PATH.OUTPUT_PATH)) mkdir(APP_PATH.OUTPUT_PATH);

	if(!is_dir(APP_PATH.DATA_PATH.'css/')) mkdir(APP_PATH.DATA_PATH.'css/');
	if(!is_dir(APP_PATH.DATA_PATH.'js/')) mkdir(APP_PATH.DATA_PATH.'js/');
	if(!is_dir(APP_PATH.DATA_PATH.'images/')) mkdir(APP_PATH.DATA_PATH.'images/');
	if(!is_dir(APP_PATH.DATA_PATH.'fonts/')) mkdir(APP_PATH.DATA_PATH.'fonts/');
	if(!is_dir(APP_PATH.DATA_PATH.'svg/')) mkdir(APP_PATH.DATA_PATH.'svg/');
	if(!is_dir(APP_PATH.DATA_PATH.'view/')) mkdir(APP_PATH.DATA_PATH.'view/');
?>