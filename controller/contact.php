<?php 
	include_once('header.php');
	$web_set["titlePrefix"] = "聯絡我們";

	$temp = new MTsung\dataList($console,PREFIX.$console->path[0],$lang);
	$data["one"] = $temp->getOne();

	if($_POST){
		if(!$console->checkVerifyCode($_POST['verifycode'])){
			$console->alert($console->getMessage('VERIFY_ERROR'),-1);
		}

		foreach ($_POST as $key => $value) {
			if(strpos($key,"key") !== 0){
				unset($_POST[$key]);
			}
		}

		$input["keyName"] = $data["one"]["dataName"];
		foreach ($_POST as $key => $value) {
			if(is_array($value)){
				$_POST[$key] = implode(",",$value);
			}
		}
		$input["keyData"] = implode("|__|",$_POST);
		$form = new MTsung\form($console,PREFIX.$console->path[0]."_form",$lang);
		if($form->setData($input)){
			$form->sendForm(array(
				"keyName" => explode("|__|", $data["one"]["dataName"]),
				"keyData" => explode("|__|", $input["keyData"])
			),WEB_PATH."/".$console->path[0]);
		}else{
			$console->alert($form->message,-1);
		}
	}

	$data["one"]["dataName"] = explode("|__|", $data["one"]["dataName"]);
	$data["one"]["dataType"] = explode("|__|", $data["one"]["dataType"]);
	$data["one"]["dataOption"] = explode("|__|", $data["one"]["dataOption"]);
	foreach ($data["one"]["dataOption"] as $key => $value) {
		$data["one"]["dataOption"][$key] = explode(",", $value);
	}
	$data["one"]["dataRequired"] = explode("|__|", $data["one"]["dataRequired"]);
?>