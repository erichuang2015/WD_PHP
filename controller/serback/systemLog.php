<?php

$data["listUrl"] = $web_set['serback_url'].'/'.$console->path[0];
$systemLog = new MTsung\systemLog($console,PREFIX.'system_log');
if(isset($console->path[1])){
	$switch["buttonBox"] = 1;
	
	switch ($console->path[1]) {
		case 'detail':
			//查看
			if(isset($console->path[2]) && is_numeric($console->path[2])){
				if(!$data["one"] = $systemLog->getLog("where id='".(int)$console->path[2]."'")[0]){
					$console->alert($systemLog->message,$data["listUrl"]);
				}
				$data["one"]["oldData"] = array_map("htmlspecialchars",json_decode($data["one"]["oldData"], true));
				$data["one"]["newData"] = array_map("htmlspecialchars",json_decode($data["one"]["newData"], true));
			}else{
				//網址參數錯誤
				$console->alert($console->getMessage("NOT_AUTHORITY"),$data["listUrl"]);
			}

			$data["redoOnClick"] = "
				if(confirm('".$console->getMessage("REDO_ALERT_TEXT")."')){
					window.location.href='".$web_set['serback_url'].'/'.$console->path[0]."/redo/".$console->path[2]."';
				}
			";
			$switch["redoButton"] = 1;
			$switch["backButton"] = 1;
			$switch["detailList"] = 1;
			break;
		case 'redo':
			//還原
			if(isset($console->path[2]) && is_numeric($console->path[2])){
				if($systemLog->redoData($console->path[2])){
					$console->alert($systemLog->message,$data["listUrl"]);
				}else{
					$console->alert($systemLog->message,-1);
				}
			}else{
				//網址參數錯誤
				$console->alert($console->getMessage("NOT_AUTHORITY"),$data["listUrl"]);
			}

			break;
		default:
			//網址參數錯誤
			$console->alert($console->getMessage("NOT_AUTHORITY"),$data["listUrl"]);
			break;
	}
}else{
//列表頁

	//搜尋key
	$searchKey = array("id","language","dataTable","url","update_user","IP");
	$data["list"] = $systemLog->getListLog("order by id desc",$searchKey);

	if($data["list"]){
		foreach ($data["list"] as $key => $value) {
			if($data["list"][$key]["language"]){
				if(strpos($value["url"],WEB_PATH."/serback")===0){
					$data["list"][$key]["url"] = str_replace("/serback","/".$data["list"][$key]["language"]."/serback",$value["url"]);
				}
			}
		}
	}
	$data["pageNumber"] = $systemLog->pageNumber;
	$data["addOnClick"] = "window.location.href='".$web_set['serback_url'].'/'.$console->path[0]."/add';";
	$switch["listList"] = 1;
	$switch["searchBox"] = 1;
}



?>