<?php

$switch["buttonBox"] = 1;
$data["listUrl"] = $web_set['serback_url'].'/'.$console->path[0];

$memberList = new MTsung\member($console,PREFIX.'admin',$memberSessionName);
$memberGroupList = new MTsung\memberGroup($console,PREFIX.'admin_group');


$menu = new MTsung\menu($console,PREFIX."menu");
//系統管理員
if($memberInfo["group"]["id"]==1){
	$data["menu"] = $menu->getData("where status='1' order by step asc");
}else{
	$data["menu"] = $menu->getData("where status='1' and id in ('".str_replace(",","','", $memberInfo["group"]["auth"])."') order by step asc");
}
if(isset($console->path[1])){
//動作
	switch ($console->path[1]) {
		case 'edit':
			//修改
			if(isset($console->path[2]) && is_numeric($console->path[2])){
				if($_POST){
					if($memberInfo["group"]["id"]!=1){
						if ($memberInfo["group"]["control"]*1 >= $_POST["control"]){
							$console->alert($console->getMessage("CONTROL_MAX",array($memberInfo["group"]["control"]*1+1)),-1);
						}

						//將該群組看不到的auth加回去
						$temp = $memberGroupList->getData("where id='".$console->path[2]."'")[0];
						$temp = explode(",", $temp["auth"]);
						$temp1 = explode(",", $memberInfo["group"]["auth"]);
						$diff = array_diff($temp,$temp1);
						if($diff){
							foreach ($diff as $key => $value) {
								$_POST["auth"][] = $value;
							}
						}
						//將該群組看不到的auth加回去
					}
					$_POST["id"] = $console->path[2];
					if(is_array($_POST["auth"])){
						$_POST["auth"] = implode(",",$_POST["auth"]);
					}
					if($memberGroupList->setData($_POST)){
						$console->alert($memberGroupList->message,$_SERVER["REQUEST_URI"]);
					}else{
						$console->alert($memberGroupList->message,-1);
					}
				}else{
					if($memberInfo["group"]["id"]!=1){
						$temp = $memberGroupList->getData("where id='".$console->path[2]."' and control>".$memberInfo["group"]["control"]." ");
					}else{
						$temp = $memberGroupList->getData("where id='".$console->path[2]."'");
					}
					if($temp){
						$data["one"] = $temp[0];
						$data["one"]["auth"] = explode(",", $data["one"]["auth"]);
					}else{
						$console->alert($memberGroupList->message,$data["listUrl"]);
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
				if($memberInfo["group"]["id"]!=1){
					if ($memberInfo["group"]["control"]*1 >= $_POST["control"]){
						$console->alert($console->getMessage("CONTROL_MAX",array($memberInfo["group"]["control"]*1+1)),-1);
					}
				}
				if(isset($_POST["auth"])){
					$_POST["auth"] = implode(",",$_POST["auth"]);
				}
				if($memberGroupList->setData($_POST)){
					$console->alert($memberGroupList->message,$data["listUrl"]);
				}else{
					$console->alert($memberGroupList->message,-1);
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
				$temp = $memberGroupList->getData("where control>".$memberInfo["group"]["control"]."");
				$tmep1 = array();
				foreach ($temp as $key => $value) {
					$temp1[] = $value["id"];
				}

				if($memberList->getData("where groupID in ('".implode("','", $_POST["checkElement"])."')")){
					$console->alert($console->getMessage("GROUP_IS_USING"),$data["listUrl"]);
				}

				foreach ($_POST["checkElement"] as $key => $value) {
					if($value!=$member->getInfo("groupID") && in_array($value,$temp1)){
						$memberGroupList->rmData($value);
					}
				}
				$console->alert($memberGroupList->message,$data["listUrl"]);
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
		if($memberGroupList->setDataAll($_POST)){
			$console->alert($memberGroupList->message,$_SERVER["REQUEST_URI"]);
		}else{
			$console->alert($memberGroupList->message,-1);
		}
	}

	//搜尋key
	$searchKey = array(	
						"name"
						);

	if($memberInfo["group"]["id"]!=1){
		$data["list"] = $memberGroupList->getListData("and control>".$memberInfo["group"]["control"]." order by id desc",$searchKey);
	}else{
		$data["list"] = $memberGroupList->getListData("order by id desc",$searchKey);
	}

	$data["pageNumber"] = $memberGroupList->pageNumber;

	$switch["deleteButton"] = 1;

	$switch["addButton"] = 1;
	$data["addOnClick"] = "window.location.href='".$web_set['serback_url'].'/'.$console->path[0]."/add';";

	$switch["saveButton"] = 1;
	$switch["listList"] = 1;
	$switch["searchBox"] = 1;
}



?>