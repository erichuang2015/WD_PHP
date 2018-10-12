<?php

$switch["buttonBox"] = 1;
$data["listUrl"] = $web_set['serback_url'].'/'.$console->path[0];

$memberList = new MTsung\member($console,PREFIX.'admin',$memberSessionName);
$memberGroupList = new MTsung\memberGroup($console,PREFIX.'admin_group');

if($memberInfo["group"]["id"]!=1){
	$data["group"] = $memberGroupList->getData("where control>".$memberInfo["group"]["control"]." order by create_date desc");
}else{
	$data["group"] = $memberGroupList->getData("order by create_date desc");
}

//有權限操作的群組id
$groupIDs = array();
if($data["group"]){
	foreach ($data["group"] as $key => $value) {
		$groupIDs[] = $value["id"];
	}
}

/**
 * 上傳圖片模組
 */
$module["uploadImg"][0]["name"] = "picture";
$module["uploadImg"][0]["max"] = 1;


if(isset($console->path[1])){
//動作
	switch ($console->path[1]) {
		case 'edit':
			//修改
			if(isset($console->path[2]) && is_numeric($console->path[2])){
				if($_POST){
					if(!in_array($_POST["groupID"], $groupIDs)){
						$console->alert($console->getMessage("NOT_AUTHORITY"),$data["listUrl"]);
					}

					if($memberList->setUser($console->path[2],$_POST)){
						$console->alert($memberList->message,$_SERVER["REQUEST_URI"]);
					}else{
						$console->alert($memberList->message,-1);
					}
				}else{
					if($console->path[2] != $memberInfo["id"]){
						$temp = $memberList->getUser($console->path[2]);
						if($temp){
							if(!in_array($temp["groupID"], $groupIDs)){
								$console->alert($console->getMessage("NOT_AUTHORITY"),$data["listUrl"]);
							}
							
							$data["one"] = $temp;
							$data[$module["uploadImg"][0]["name"]] = $data["one"][$module["uploadImg"][0]["name"]];
						}else{
							$console->alert($memberList->message,$data["listUrl"]);
						}
						unset($temp);
					}else{
						//修改自己用profile
						$console->alert($console->getMessage("NOT_AUTHORITY"),$data["listUrl"]);
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
				//取得所有比自己權限小的帳號ID
				$temp = $memberList->getData("where groupID in ('".implode("','", $groupIDs)."')");
				$tmep1 = array();
				foreach ($temp as $key => $value) {
					$temp1[] = $value["id"];
				}

				foreach ($_POST["checkElement"] as $key => $value) {
					//自己以外&&比自己權限小的
					if($value!=$member->getInfo("id") && in_array($value,$temp1)){
						$memberList->rmUser($value);
					}
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
						"account"
						);
	$data["list"] = $memberList->getListData("and groupID in ('".implode("','", $groupIDs)."') order by create_date desc",$searchKey);



	$data["pageNumber"] = $memberList->pageNumber;

	$switch["deleteButton"] = 1;

	$switch["addButton"] = 1;
	$data["addOnClick"] = "window.location.href='".$web_set['serback_url'].'/'.$console->path[0]."/add';";

	$switch["saveButton"] = 1;
	$switch["listList"] = 1;
	$switch["searchBox"] = 1;
}



?>