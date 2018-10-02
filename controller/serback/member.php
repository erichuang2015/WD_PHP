<?php
$switch["buttonBox"] = 1;
$data["listUrl"] = $console->MT_web["serback_path"].'/'.$console->path[0];

$memberList = new MTsung\member($console,PREFIX.'member');

$explodeArray = array("address");
if(isset($console->path[1])){
//動作
	switch ($console->path[1]) {
		case 'edit':
			//修改
			if(isset($console->path[2]) && is_numeric($console->path[2])){
				if($_POST){
					if($memberList->setUser($console->path[2],$_POST)){
						$console->alert($memberList->message,$_SERVER["REQUEST_URI"]);
					}else{
						$console->alert($memberList->message,-1);
					}
				}else{
					$temp = $memberList->getUser($console->path[2]);
					if($temp){
						$data["one"] = $temp;
						if(isset($explodeArray)){
							foreach ($explodeArray as $key => $value) {
								if(($value != "") && !is_array($data["one"][$value])){
									$data["one"][$value] = explode("|__|", $data["one"][$value]);
								}
							}
						}
					}else{
						$console->alert($memberList->message,$data["listUrl"]);
					}
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
				if($memberList->addUser($_POST) === true){
					$console->alert($console->getMessage('ADD_OK'),$data["listUrl"]);
				}else{
					$console->alert($memberList->message,-1);
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
					$memberList->rmUser($value);
				}
				$console->alert($memberList->message,$data["listUrl"]);
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
		if($memberList->setDataAll($_POST)){
			$console->alert($memberList->message,$_SERVER["REQUEST_URI"]);
		}else{
			$console->alert($memberList->message,-1);
		}
	}


	//搜尋key
	$searchKey = array(	
						"name",
						"account",
						"email"
						);
	$data["list"] = $memberList->getListData(" order by create_date desc",$searchKey);



	$data["pageNumber"] = $memberList->pageNumber;

	$switch["deleteButton"] = 1;

	$switch["addButton"] = 1;
	$data["addOnClick"] = "window.location.href='".$console->MT_web["serback_path"].'/'.$console->path[0]."/add';";

	$switch["saveButton"] = 1;
	$switch["listList"] = 1;
	$switch["searchBox"] = 1;
}



?>