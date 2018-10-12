<?php
$switch["buttonBox"] = 1;

$data["listUrl"] = $web_set['serback_url'].'/'.$console->path[0].'/'.$console->path[1];

$form = new MTsung\form($console,PREFIX.$console->path[1]."_form",$settingLang);

$designName = $console->path[1]."_form";

if(isset($console->path[2])){
//動作
	switch ($console->path[2]) {
		case 'edit':
			//修改
			if(isset($console->path[3]) && is_numeric($console->path[3])){

				if($temp = $form->getData("where id='".$console->path[3]."'")){
					$data["one"] = $temp[0];
					$data["one"]["keyName"] = explode("|__|", $data["one"]["keyName"]);
					$data["one"]["keyData"] = explode("|__|", $data["one"]["keyData"]);

					$validation = new MTsung\validation();
					foreach ($data["one"]["keyData"] as $key => $value) {
						$data["one"]["dataIsEmail"][$key] = $validation->isEmail($value);
					}
				}else{
					$console->alert($form->message,$data["listUrl"]);
				}
			}else{
				//網址參數錯誤
				$console->alert($console->getMessage("NOT_AUTHORITY"),$data["listUrl"]);
			}

			// $switch["saveButton"] = 1;
			$switch["backButton"] = 1;
			$switch["editList"] = 1;
			break;
		case 'delete':
			//刪除
			if($_POST && isset($_POST["checkElement"])){
				$form->rmData($_POST["checkElement"]);
				$console->alert($form->message,$data["listUrl"]);
			}
		default:
			//網址參數錯誤
			$console->alert($console->getMessage("NOT_AUTHORITY"),$data["listUrl"]);
			break;
	}
}else{
//列表頁

	$searchKey = array("keyData");
	$data["list"] = $form->getListData(" order by create_date desc",$searchKey);

	if($data["list"]){
		foreach ($data["list"] as $key => $value) {
			$data["list"][$key]["keyName"] = explode("|__|", $value["keyName"]);
			$data["list"][$key]["keyData"] = explode("|__|", $value["keyData"]);
		}
	}

	$data["pageNumber"] = $form->pageNumber;

	$switch["deleteButton"] = 1;

	// $switch["saveButton"] = 1;
	$switch["listList"] = 1;
	$switch["searchBox"] = 1;
}



?>