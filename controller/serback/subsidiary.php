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
						$data["one"]["bandwidth"] = 0;
						if($temp = $cPanel->getBandwidth("year_month",$data["one"]["subDomain"].".".MAIN_SERVER_NAME,strtotime(date('Y-m-01', strtotime(DATE))))){
							$data["one"]["bandwidth"] = $console->formatSize(array_shift($temp));
						}
						$data["one"]["dataSize"] = $console->formatSize($console->getDirSize("data/".($data["one"]["id"]+10000)."/"));
			            $data["one"]["dbSize"] = $console->formatSize($console->getDatabaseSize(DB_PREFIX.$data["one"]["subDomain"]));

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
						if(!$cPanel->addSubDomain($_POST["subDomain"])){
							$console->alert($cPanel->message,-1);
						}

						if($_POST["addonDomain"]){
							if(!$cPanel->addAddonDomain($_POST["subDomain"],$_POST["addonDomain"])){
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
					ignore_user_abort(true);
					set_time_limit(0);
					ini_set("memory_limit",-1);
					$tempConn->Execute("set global max_allowed_packet=200*1024*1024; ");
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
				
				if($tempId = $basic->setData($_POST,false,$checkArray,$requiredArray)){
					$dataPath = "data/".($tempId+10000)."/";
					recurse_copy(APP_PATH."data/10000/",APP_PATH.$dataPath);
					$tempConn->Execute("UPDATE database_menu SET url='file/".$dataPath."css' WHERE url='file/css'");
					$tempConn->Execute("UPDATE database_menu SET url='file/".$dataPath."fonts' WHERE url='file/fonts'");
					$tempConn->Execute("UPDATE database_menu SET url='file/".$dataPath."images' WHERE url='file/images'");
					$tempConn->Execute("UPDATE database_menu SET url='file/".$dataPath."js' WHERE url='file/js'");
					$tempConn->Execute("UPDATE database_menu SET url='file/".$dataPath."output' WHERE url='file/output'");
					$tempConn->Execute("UPDATE database_menu SET url='file/".$dataPath."svg' WHERE url='file/svg'");
					$tempConn->Execute("UPDATE database_menu SET url='file/".$dataPath."upload' WHERE url='file/upload'");
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

	if($data["list"] = $basic->getListData("order by sort ",$searchKey)){
		foreach ($data["list"] as $key => $value) {
			$data["list"][$key]["bandwidth"] = 0;
			if($temp = $cPanel->getBandwidth("year_month",$value["subDomain"].".".MAIN_SERVER_NAME,strtotime(date('Y-m-01', strtotime(DATE))))){
				$data["list"][$key]["bandwidth"] = $console->formatSize(array_shift($temp));
			}
			$data["list"][$key]["dataSize"] = $console->formatSize($console->getDirSize("data/".($value["id"]+10000)."/"));
			$data["list"][$key]["dbSize"] = $console->formatSize($console->getDatabaseSize(DB_PREFIX.$value["subDomain"]));
		}
	}

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