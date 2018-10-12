<?php
/**
 * 語系管理
 */

$switch["buttonBox"] = 1;
$data["listUrl"] = $web_set['serback_url'].'/'.$console->path[0];

$module["aceEditor"]["name"] = '_aceEditor';//POST欄位名稱，不可使用"aceEditor"

if(isset($console->path[1])){
	switch ($console->path[1]) {
		case 'edit':
			if(isset($console->path[2]) && isset($console->languageArray[$console->path[2]])){
				//修改語系
				if($_POST){
					//寫入檔案
					$console->writeLanguageini($console->path[2],$_POST[$module["aceEditor"]["name"]]);
					if($console->path[2]!=$_POST["languageKey"]){
						//重新命名
						$console->reNameLanguageini($console->path[2],$_POST["languageKey"]);
						$console->alert($console->getMessage("EDIT_OK"),$data["listUrl"].'/'.$console->path[1].'/'.$_POST["languageKey"]);
					}else{
						$console->alert($console->getMessage("EDIT_OK"),$_SERVER["REQUEST_URI"]);
					}
				}
				$data[$module["aceEditor"]["name"]] = htmlspecialchars($console->readLanguageini($console->path[2]));
			}else{
				//網址參數錯誤
				$console->alert($console->getMessage("NOT_AUTHORITY"),$data["listUrl"]);
			}

			$switch["saveButton"] = 1;
			$switch["backButton"] = 1;
			$switch["editList"] = 1;
			break;
		case 'add':
			//新增語系
			if($_POST){
				if($_POST["languageKey"]){
					if(!isset($console->languageArray[$_POST["languageKey"]])){
						$console->writeLanguageini($_POST["languageKey"],$_POST[$module["aceEditor"]["name"]]);
						$console->alert($console->getMessage("ADD_OK"),$data["listUrl"]);
					}else{
						$console->alert($console->getMessage("LANGUAGE_REPEAT"),-1);
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
			//刪除語系
			if($_POST && isset($_POST["checkElement"])){
				foreach ($_POST["checkElement"] as $key => $value) {
					$console->deleteLanguageini($value);
				}
				$console->alert($console->getMessage("DELETE_OK"),$data["listUrl"]);
			}
		default:
			//網址參數錯誤
			$console->alert($console->getMessage("NOT_AUTHORITY"),$data["listUrl"]);
			break;
	}
}else{//列表頁
	$data["list"] = $console->getLanguageArray('array');


	$switch["deleteButton"] = 1;

	$switch["addButton"] = 1;
	$data["addOnClick"] = "window.location.href='".$data["listUrl"]."/add';";

	$switch["listList"] = 1;
}


?>