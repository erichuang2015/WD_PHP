<?php
if(MAIN_SERVER_NAME!=$_SERVER["SERVER_NAME"]){
    $console->alert($console->getMessage("MAIN_SERVER_USE"),-1);
}

$switch["buttonBox"] = 1;
$data["listUrl"] = $web_set['serback_url'].'/'.$console->path[0];

$basic = new MTsung\dataList($console,PREFIX.$console->path[0],"");

$designName = $console->path[0];

//欄位白名單 = 需要必填的欄位 = 需要轉陣列的欄位 = 搜尋key
$checkArray = $requiredArray = $explodeArray = $searchKey = array();
$searchKey = array("name","subDomain","addonDomain");

global $dbHost,$dbUser,$dbPass;
$cPanel = new MTsung\cPanel($console,$dbUser,$dbPass,DB_PREFIX);

//ajax
if(isset($_GET["ajax"]) && $_GET["ajax"]){
	switch ($_GET["ajax"]) {
		case 'loadData'://取得最新 資料庫大小	資料使用空間	本月使用頻寬		
			if($data["list"] = $basic->getData()){
				foreach ($data["list"] as $key => $value) {
					$data["list"][$key]["bandwidth"] = 0;
					if($temp = $cPanel->getBandwidth("year_month",$value["subDomain"].".".MAIN_SERVER_NAME,strtotime(date('Y-m-01', strtotime(DATE))))){
						$data["list"][$key]["bandwidth"] = $console->formatSize(array_shift($temp));
					}
					$data["list"][$key]["dataSize"] = $console->formatSize($console->getDirSize("data/".($value["id"]+10000)."/"));
					$data["list"][$key]["dbSize"] = $console->formatSize($console->getDatabaseSize(DB_PREFIX.$value["subDomain"]));
					$basic->setData($data["list"][$key]);
				}
			}
			break;
	}
	$console->outputJson(true,"");

	exit;
}
//ajax

if(isset($console->path[1])){
//動作
	switch ($console->path[1]) {
		case 'edit':
			//修改
			if(isset($console->path[2]) && is_numeric($console->path[2])){
				if($_POST){
					$_POST["id"] = $console->path[2];
					if($basic->setData($_POST,false,$checkArray,$requiredArray)){
						$console->alert($basic->message,$_SERVER["REQUEST_URI"]);
					}else{
						$console->alert($basic->message,-1);
					}
				}else{					
					if($temp = $basic->getData("where id=?",array($console->path[2]),$explodeArray,$module)){
						$data["one"] = $temp[0];
					}else{
						$console->alert($basic->message,$data["listUrl"]);
					}
					unset($temp);
				}
			}else{
				//網址參數錯誤
				$console->alert($console->getMessage("NOT_AUTHORITY"),$data["listUrl"]);
			}

			$switch["saveButton"] = 1;
			$switch["backButton"] = 1;
			$switch["editList"] = 1;
			break;
		case 'add':
			//新增
			if($_POST){
				if($_POST["subDomain"]){

					$dbData = DB_PREFIX.$_POST["subDomain"];
					if($_SERVER["SERVER_NAME"]=="localhost" || $_SERVER["SERVER_NAME"]=="127.0.0.1"){
						$console->conn->Execute("create database ".$dbData." default character set utf8mb4 collate utf8mb4_general_ci");
					}else{
						if($_POST["addonDomain"]){
							if(!$cPanel->addAddonDomain($_POST["subDomain"],$_POST["addonDomain"])){
								$console->alert($cPanel->message,-1);
							}
						}else{
    						if(!$cPanel->addSubDomain($_POST["subDomain"])){
    							$console->alert($cPanel->message,-1);
    						}
						}

						if(!$cPanel->addDatabase($_POST["subDomain"])){
							$console->alert($cPanel->message,-1);
						}
					}

					$tempConn = ADONewConnection("mysqli");
					$connect_check = $tempConn->PConnect($dbHost,$dbUser,$dbPass,$dbData);
					if(!$connect_check){
						$console->alert("Database connection failed.",-1);
					}
					//關閉嚴格模式
					$tempConn->Execute("SET sql_mode = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';");
					//設定utf8mb4編碼
					$tempConn->Execute("SET NAMES utf8mb4;");
					$tempConn->Execute("SET CHARACTER_SET_CLIENT=utf8mb4;");
					$tempConn->Execute("SET CHARACTER_SET_RESULTS=utf8mb4;");
					$tempConn->Execute("SET CHARACTER_SET_CONNECTION=utf8mb4;");
					//時區
					$tempConn->Execute("SET GLOBAL time_zone = '+08:00';");
					$tempConn->Execute("SET time_zone = '+08:00';");
					ignore_user_abort(true);
					set_time_limit(0);
					ini_set("memory_limit",-1);
					$tempConn->Execute("set global max_allowed_packet=200*1024*1024; ");
					$fileName = APP_PATH.'config/setup.sql';//安裝檔
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
								$tempConn->Execute($sql);
								if($tempConn->errorMsg()){
									echo "<b>Error</b>: ".$tempConn->errorMsg().". in <b>".$fileName."</b> on line <b>".($key+1)."</b><br><br>";
									$errorFlag = true;
								}
								$sql = "";
							}
						}
						if($errorFlag){
							exit;
						}
					}else{
						$console->alert("Setup error.",-1);
					}
				}else{
					$console->alert($console->getMessage("NOT_AUTHORITY"),-1);
				}
				
				//新增郵件帳號
				$emailPwd = md5(DATE).md5(DATE);
				$cPanel->addEmailUser($_POST["subDomain"]."@".MAIN_SERVER_NAME,$emailPwd);

				if($tempId = $basic->setData($_POST,false,$checkArray,$requiredArray)){
					$dataPath = "data/".($tempId+10000)."/";
					recurse_copy(APP_PATH."data/10000/",APP_PATH.$dataPath);
					$tempConn->Execute("UPDATE database_menu SET url='file/".$dataPath."css' WHERE url='file/css'");
					$tempConn->Execute("UPDATE database_menu SET url='file/".$dataPath."fonts' WHERE url='file/fonts'");
					$tempConn->Execute("UPDATE database_menu SET url='file/".$dataPath."images' WHERE url='file/images'");
					$tempConn->Execute("UPDATE database_menu SET url='file/".$dataPath."js' WHERE url='file/js'");
					$tempConn->Execute("UPDATE database_menu SET url='file/".$dataPath."output' WHERE url='file/output'");
					$tempConn->Execute("UPDATE database_menu SET url='file/".$dataPath."' WHERE url='file/svg'");
					$tempConn->Execute("UPDATE database_menu SET url='file/".$dataPath."upload' WHERE url='file/upload'");

					$tempConn->Execute("UPDATE database_setting SET detail='ssl' WHERE name='smtpSMTPSecure'");
					$tempConn->Execute("UPDATE database_setting SET detail='mail.".MAIN_SERVER_NAME."' WHERE name='smtpHost'");
					$tempConn->Execute("UPDATE database_setting SET detail='465' WHERE name='smtpPort'");
					$tempConn->Execute("UPDATE database_setting SET detail='".$_POST["subDomain"]."@".MAIN_SERVER_NAME."' WHERE name='smtpUsername' or name='senderEmail'");
					$tempConn->Execute("UPDATE database_setting SET detail='".$emailPwd."' WHERE name='smtpPassword'");
					$tempConn->close();
					$console->alert($basic->message,$data["listUrl"]."?".$_SERVER["QUERY_STRING"]);
				}else{
					$console->alert($basic->message,-1);
				}
			}


			$switch["addButton"] = 1;
			$data["addOnClick"] = "formSubmit();";
			$switch["backButton"] = 1;
			$switch["addList"] = 1;
			break;
		case 'delete':
			//刪除
			if($_POST && isset($_POST["checkElement"])){
				foreach ($_POST["checkElement"] as $key => $value) {
					$basic->rmData($value);
				}
				$console->alert($basic->message,$data["listUrl"]."?".$_SERVER["QUERY_STRING"]);
			}
		default:
			//網址參數錯誤
			$console->alert($console->getMessage("NOT_AUTHORITY"),$data["listUrl"]);
			break;
	}
}else{
//列表頁

	/**
	 * 修改全部
	 */
	if($_POST){
		if($basic->setDataAll($_POST)){
			$console->alert($basic->message,$_SERVER["REQUEST_URI"]);
		}else{
			$console->alert($basic->message,-1);
		}
	}

	$data["list"] = $basic->getListData("order by sort ",$searchKey);
	$data["pageNumber"] = $basic->pageNumber;

	$switch["deleteButton"] = 1;

	$switch["addButton"] = 1;
	$data["addOnClick"] = "window.location.href='".$data["listUrl"]."/add';";
	if($_SERVER["QUERY_STRING"]){
		$data["addOnClick"] = "window.location.href='".$data["listUrl"]."/add?".$_SERVER["QUERY_STRING"]."';";
	}

	$switch["saveButton"] = 1;
	$switch["listList"] = 1;
	$switch["searchBox"] = 1;
}


/**
 * 資料夾複製
 * @param  [type] $src [description]
 * @param  [type] $dst [description]
 * @return [type]      [description]
 */
function recurse_copy($src,$dst) {
	$dir = opendir($src);
	@mkdir($dst);
	while(false !== ( $file = readdir($dir)) ) {
	    if (( $file != '.' ) && ( $file != '..' )) {
	        if ( is_dir($src . '/' . $file) ) {
	            recurse_copy($src . '/' . $file,$dst . '/' . $file);
	        }
	        else {
	            copy($src . '/' . $file,$dst . '/' . $file);
	        }
	    }
	}
	closedir($dir);
}

?>