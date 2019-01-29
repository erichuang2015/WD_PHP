<?php
/**
 * 樣板管理
 */

$switch["buttonBox"] = 1;
$data["listUrl"] = $web_set['serback_url'].'/'.$console->path[0].'/'.$console->path[1];

$fileTemplate = new MTsung\fileTemplate($console);

$module["aceEditor"]["name"] = '_aceEditor';//POST欄位名稱，不可使用"aceEditor"


$tables = $this->conn->GetArray("SHOW TABLES");
foreach ($tables as $key => $value) {
	if(isset(explode("__",$value[0])[1]) && explode("__",$value[0])[1]==str_replace("-","_",$settingLang)){
		$tables[$key] = explode(PREFIX,explode("__",$value[0])[0])[1];
	}else{
		unset($tables[$key]);
	}
}
$data["tables"] = array_values($tables);
foreach ($data["tables"] as $key => $value) {
	if(!$menu->getData("where tablesName=? and status=1",array($value))){
		unset($data["tables"][$key]);
	}
}
$data["tables"] = array_values($data["tables"]);

if(isset($console->path[2])){
	switch ($console->path[2]) {
		case 'edit':
			if(isset($console->path[3]) && is_numeric($console->path[3])){
				//修改
				if($_POST){
					if(!$fileTemplate->setFile($console->path[3],$_POST[$module["aceEditor"]["name"]])){
						$console->alert($fileTemplate->message,-1);
					}
					unset($_POST[$module["aceEditor"]["name"]]);
					if($_POST["useTables"]){
						$_POST["useTables"] = implode("|__|",$_POST["useTables"]);
					}
					if($fileTemplate->setData($console->path[3],$_POST)){
						$console->alert($fileTemplate->message,$_SERVER["REQUEST_URI"]);
					}else{
						$console->alert($fileTemplate->message,-1);
					}
				}else{						
					if(!$data["one"] = $fileTemplate->getData($console->path[3])){
						$console->alert($fileTemplate->message,-1);
					}
					$data[$module["aceEditor"]["name"]] = $fileTemplate->getFile($console->path[3]);
					$data["one"]["useTables"] = explode("|__|",$data["one"]["useTables"]);
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
				if($_POST["name"]){
					$_POST["type"] = $console->path[1];
					$_POST["useTables"] = implode("|__|",$_POST["useTables"]);
					if(!$fileTemplate->addFile($_POST["name"],$_POST[$module["aceEditor"]["name"]])){
						$console->alert($fileTemplate->message,-1);
					}
					unset($_POST[$module["aceEditor"]["name"]]);
					if($fileTemplate->addData($_POST)){
						$console->alert($fileTemplate->message,$data["listUrl"]);
					}else{
						$console->alert($fileTemplate->message,-1);
					}
				}else{
					$console->alert($console->getMessage("INPUT_NULL"),-1);
				}
			}
			$data[$module["aceEditor"]["name"]] = '';


			$switch["addButton"] = 1;
			$data["addOnClick"] = "formSubmit();";
			$switch["backButton"] = 1;
			$switch["addList"] = 1;
			break;
		case 'delete':
			//刪除
			if($_POST && isset($_POST["checkElement"])){
				$fileTemplate->rmData($_POST["checkElement"]);
				$console->alert($fileTemplate->message,$data["listUrl"]);
			}
		default:
			//網址參數錯誤
			$console->alert($console->getMessage("NOT_AUTHORITY"),$data["listUrl"]);
			break;
	}
}else{//列表頁

	/**
	 * 修改全部
	 */
	if($_POST){
		if($fileTemplate->setDataAll($_POST)){
			$console->alert($fileTemplate->message,$_SERVER["REQUEST_URI"]);
		}else{
			$console->alert($fileTemplate->message,-1);
		}
	}

	$data["list"] = $fileTemplate->getListData($console->path[1]);


	$switch["saveButton"] = 1;
	$switch["deleteButton"] = 1;

	$switch["addButton"] = 1;
	$data["addOnClick"] = "window.location.href='".$data["listUrl"]."/add';";

	$switch["listList"] = 1;
}


?>