<?php
$switch["buttonBox"] = 1;
$data["listUrl"] = $web_set['serback_url'].'/'.$console->path[0];

$memberList = new MTsung\member($console,PREFIX.'member');

$csvKeyArray = array("account","name","email","address","phone");

if($_GET["export"] == "1"){
	$csv = new MTsung\csv();
	$temp = $memberList->getData();

	foreach ($temp as $key => $value) {
		foreach ($value as $key1 => $value1) {
			if(!in_array($key1, $csvKeyArray) || is_numeric($key1)){
				unset($temp[$key][$key1]);
			}
		}
	}
	$csv->export($temp);
}

include_once(CONTROLLER_PATH.'serback/__about.php');

if(isset($module["uploadImg"])){
	foreach ($module["uploadImg"] as $key => $value) {
		$memberList->addPictureName($value["name"]);
	}
}
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

						//定單列表
						$order = new MTsung\shoppingCart($console,$memberList,'',PREFIX."shopping_cart",$settingLang);
						$data["one"]["orderList_"] = $order->getData("where step>1 and memberId=? and status=1 order by orderNumber desc",array($data["one"]["id"]));
						$data["shipmentTitle"] = $order->getShipmentTitle();
						$data["shipmentStatus"] = array(
							MTsung\shipmentStatusType::NO => "<font color='red'>".$console->getLabel("SHIPMENT_STATUS_TYPE_NO")."</font>",
							MTsung\shipmentStatusType::OK => "<font color='blue'>".$console->getLabel("SHIPMENT_STATUS_TYPE_OK")."</font>",
						);
						$data["paymentTitle"] = $order->getPaymentTitle();
						$data["paymentStatus"] = array(
							MTsung\paymentStatusType::NO => "<font color='red'>".$console->getLabel("PAYMENT_STATUS_TYPE_NO")."</font>",
							MTsung\paymentStatusType::OK => "<font color='blue'>".$console->getLabel("PAYMENT_STATUS_TYPE_OK")."</font>",
						);

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
	$data["addOnClick"] = "window.location.href='".$web_set['serback_url'].'/'.$console->path[0]."/add';";

	$switch["saveButton"] = 1;
	$switch["listList"] = 1;
	$switch["searchBox"] = 1;
}



?>