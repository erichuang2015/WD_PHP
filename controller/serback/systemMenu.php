<?php

include_once(CONTROLLER_PATH.'serback/__menu.php');

if($member->getInfo("account") != "vipadmin"){
	$console->alert($console->getMessage("NOT_AUTHORITY").",工程師帳號限定功能",SERBACK_PATH);
}

if(isset($console->path[1])){
//動作
	switch ($console->path[1]) {
		case 'edit':
			//修改
			if(isset($console->path[2]) && is_numeric($console->path[2])){
				$data["list"] = $systemMenu->getData("where parent<>'".$console->path[2]."' and id<>'".$console->path[2]."' order by step asc");

				if($_POST){
					$_POST["id"] = $console->path[2];
					$_POST["floor"] = explode(",", $_POST["parent"])[1] + 1;
					$_POST["parent"] = explode(",", $_POST["parent"])[0];
					if($_POST["features"]){
						$temp = explode("_other_",$_POST["features"]);
						if(isset($temp[1])){
							$_POST["url"] = $temp[1]."/".$_POST["alias"];
						}else{
							$_POST["url"] = $_POST["features"];
						}
					}
					
					if($systemMenu->setData($_POST)){
						$console->alert($systemMenu->message,$_SERVER["REQUEST_URI"]);
					}else{
						$console->alert($systemMenu->message,-1);
					}
				}else{
					$temp = $systemMenu->getData("where id='".$console->path[2]."'");
					if($temp){
						$data["one"] = $temp[0];
						if($data["one"]["features"]){
							$designName = "systemMenuFront";
						}
						if(isset($explodeArray)){
							foreach ($explodeArray as $key => $value) {
								if(($value != "") && !is_array($data["one"][$value]) && $data["one"][$value]){
									$data["one"][$value] = explode("|__|", $data["one"][$value]);
								}
							}
						}
					}else{
						$console->alert($systemMenu->message,$data["listUrl"]);
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
			$data["list"] = $systemMenu->getData("order by step asc");

			if($_POST){
				$_POST["floor"] = explode(",", $_POST["parent"])[1] + 1;
				$_POST["parent"] = explode(",", $_POST["parent"])[0];

				if($systemMenu->setData($_POST)){
					$console->alert($systemMenu->message,$data["listUrl"]);
				}else{
					$console->alert($systemMenu->message,-1);
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
				$systemMenu->rmData($_POST["checkElement"]);
				$console->alert($systemMenu->message,$data["listUrl"]);
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
		if($systemMenu->setDataAll($_POST)){
			$console->alert($systemMenu->message,$_SERVER["REQUEST_URI"]);
		}else{
			$console->alert($systemMenu->message,-1);
		}
	}

	$searchKey = array("name");
	$data["list"] = $systemMenu->getListData("order by step asc",$searchKey,50);


	$data["pageNumber"] = $systemMenu->pageNumber;

	$switch["deleteButton"] = 1;

	$switch["addButton"] = 1;
	$data["addOnClick"] = "window.location.href='".$web_set['serback_url'].'/'.$console->path[0]."/add';";

	$switch["saveButton"] = 1;
	$switch["listList"] = 1;
	// $switch["searchBox"] = 1;
}



?>