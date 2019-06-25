<?php
$switch["buttonBox"] = 1;

$data["listUrl"] = $web_set['serback_url'].'/'.$console->path[0].'/'.$console->path[1];

$form = new MTsung\form($console,PREFIX.$console->path[1]."_form",$settingLang);

$designName = "__form";

if(isset($_GET["ajax"]) && $_GET["ajax"]){
	$mail = new MTsung\phpMailer($console);
	$mail->setMailTitle($_GET["mailTitle"]);
	$mail->setMailFrom($console->setting->getValue("senderEmail"),$console->setting->getValue("senderName"));
	$mail->setMailAddress($_GET["mailRecipient"]);
	$mail->Body = nl2br($_GET["mailDetail"]);
	if(!$mail->sendMail('','')){
		$console->outputJson(false,$console->getMessage("MAIL_SEND_ERROR"));

	}
	if(isset($console->path[3]) && is_numeric($console->path[3])){
		$temp = $form->getData("where id='".$console->path[3]."'")[0];
		if($temp["reply"]){
			$temp["reply"] = explode("|__|",$temp["reply"]);
		}
		$temp["reply"][] = $_GET["mailDetail"];
		$temp["reply"] = implode("|__|",$temp["reply"]);

		if($temp["replyDate"]){
			$temp["replyDate"] = explode("|__|",$temp["replyDate"]);
		}
		$temp["replyDate"][] = DATE;
		$temp["replyDate"] = implode("|__|",$temp["replyDate"]);

		if($temp["replyRecipient"]){
			$temp["replyRecipient"] = explode("|__|",$temp["replyRecipient"]);
		}
		$temp["replyRecipient"][] = $_GET["mailRecipient"];
		$temp["replyRecipient"] = implode("|__|",$temp["replyRecipient"]);
		$form->setData($temp);
	}
	$console->outputJson(true,$console->getMessage("MAIL_SEND_OK"));
}

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
					$data["one"]["reply"] = explode("|__|",$data["one"]["reply"]);
					$data["one"]["replyDate"] = explode("|__|",$data["one"]["replyDate"]);
					$data["one"]["replyRecipient"] = explode("|__|",$data["one"]["replyRecipient"]);
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

	if($_GET["export"] == "1"){
		$csv = new MTsung\csv();
		if($data["list"]){
			$output[] = explode("|__|", $data["list"][0]["keyName"]);
			foreach ($data["list"] as $key => $value) {
				$output[] = explode("|__|", $value["keyData"]);
			}
		}

		$csv->export($output);
		exit;
	}

	if($data["list"]){
		foreach ($data["list"] as $key => $value) {
			$data["list"][$key]["keyName"] = explode("|__|", $value["keyName"]);
			$data["list"][$key]["keyData"] = explode("|__|", $value["keyData"]);
		
			$data["list"][$key]["keyName"] = array_slice($data["list"][$key]["keyName"],0,5);
			$data["list"][$key]["keyData"] = array_slice($data["list"][$key]["keyData"],0,5);
			foreach ($data["list"][$key]["keyData"] as $key1 => $value1) {
				$data["list"][$key]["keyData"][$key1] = mb_substr($value1,0,50);
			}
		}
	}

	$data["pageNumber"] = $form->pageNumber;

	$switch["deleteButton"] = 1;
	$switch["exportButton"] = 1;

	// $switch["saveButton"] = 1;
	$switch["listList"] = 1;
	$switch["searchBox"] = 1;
}



?>