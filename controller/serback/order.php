<?php
$switch["buttonBox"] = 1;
$data["listUrl"] = $web_set['serback_url'].'/'.$console->path[0];

$order_member = new MTsung\member($console,PREFIX.'member',NULL);
$order_product = new MTsung\product($console,PREFIX."product",$settingLang);
$order = new MTsung\shoppingCart($console,$order_member,$order_product,PREFIX."shopping_cart",$settingLang);

//ajax
if(isset($_GET["ajax"]) && $_GET["ajax"]){

	if($temp = $order->getData("where step>1 and id='".$console->path[2]."'")){
		$orderNumber = $temp[0]["orderNumber"];
	}else{
		$console->outputJson(false,$order->message);
		exit;
	}

	switch ($_GET["ajax"]) {
		case 'deductionPoint'://扣除使用的點數
			$order->deductionPoint($orderNumber);
			break;

		case 'replacementPoint'://補回使用的點數
			$order->replacementPoint($orderNumber);
			break;

		case 'payPoint'://發放點數
			$order->payPoint($orderNumber);
			break;

		case 'backPoint'://回收點數
			$order->backPoint($orderNumber);
			break;

		case 'setPayment'://付款狀態
			$order->setPayment($orderNumber,$_GET["status"]);
			break;

		case 'setShipment'://出貨狀態
			$order->setShipment($orderNumber,$_GET["status"]);
			break;

		case 'reCreateOrder'://重新生程訂單
			try{
				if(!$order->shipment($orderNumber,true)){
					$console->outputJson(false,$order->message);
				}
			}catch(Exception $e){
				$console->outputJson(false,$e->getMessage());
			} 
			break;

		case 'printBill'://列印需要的單子 (這不是ajax)
			try{
				if(!$order->printBill($orderNumber)){
					$console->alert($console->getMessage("ECPAY_ERROR",array($order->message)),"CLOSE");
				}
			}catch(Exception $e){
				$console->alert($console->getMessage("ECPAY_ERROR",array($e->getMessage())),-1);
			}
			exit;
			break;

	}
	$console->outputJson(true,"");

	exit;
}
//ajax

include_once(CONTROLLER_PATH.'serback/__about.php');

//付款方式
$paymentStatus = $order->getPaymentMethodArray();
$data["paymentMethod"] = array();
foreach ($paymentStatus as $key => $value) {
	if($value){
		$data["paymentMethod"][] = array(
			"key" => $key,
			"name" => $order->getPaymentTitle($key),
			"text" => $order->getPaymentText($key)
		);
	}
}

//運送方式
$shipmentStatus = $order->getShipmentMethodArray();
$data["shipmentMethod"] = array();
foreach ($shipmentStatus as $key => $value) {
	if($value){
		$data["shipmentMethod"][] = array(
			"key" => $key,
			"name" => $order->getShipmentTitle($key),
			"text" => $order->getShipmentText($key)
		);
	}
}

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

$data["getPointStatus"] = array(
	0 => $console->getLabel("SEND"),
	1 => $console->getLabel("RECYCLING") 
);

$data["usePointStatus"] = array(
	0 => $console->getLabel("DEDUCTION"),
	1 => $console->getLabel("MAKE_UP") 
);

$data["getPointStatusText"] = array(
	0 => "<font color='red'>".$console->getLabel("UNSENT")."</font>",
	1 => "<font color='blue'>".$console->getLabel("HAS_BEEN_SENT")."</font>"
);

$data["usePointStatusText"] = array(
	0 => "<font color='red'>".$console->getLabel("UNDEDUCTED")."</font>",
	1 => "<font color='blue'>".$console->getLabel("DEDUCTED")."</font>"
);

if(isset($console->path[1])){
//動作
	switch ($console->path[1]) {
		case 'edit':
			//修改
			if(isset($console->path[2]) && is_numeric($console->path[2])){

				if($temp = $order->getData("where step>1 and id='".$console->path[2]."'")){
					$orderNumber = $temp[0]["orderNumber"];
				}else{
					$console->alert($order->message,$data["listUrl"]);
				}

				if($_POST){
					$_POST["id"] = $console->path[2];
					if($order->setData($_POST,false,array("id","status"))){
						$console->alert($order->message,$_SERVER["REQUEST_URI"]);
					}else{
						$console->alert($order->message,-1);
					}
				}else{
					$data["order"] = $order->getOrder($orderNumber);
					$data["order"]["member"] = $order_member->getUser($data["order"]["memberId"]);
					$data["order"]["formData"] = json_decode($data["order"]["formData"],true);
					$data["orderList"] = $order->orderListReload($order->getOrderList($orderNumber));
			        $ECPayLog = new MTsung\ECPayLog($console);
			        $data["orderECPayLog"] = $ECPayLog->getData("where MerchantTradeNo like '".$orderNumber."%' order by create_date desc");
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
				$order->rmData($_POST["checkElement"]);
				$console->alert($order->message,$data["listUrl"]);
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
		if($order->setDataAll($_POST,array("id","status"))){
			$console->alert($order->message,$_SERVER["REQUEST_URI"]);
		}else{
			$console->alert($order->message,-1);
		}
	}
	$sql = "";

	if($_GET["paymentMethod"] != ''){
		$sql .= " and paymentMethod='".$_GET["paymentMethod"]."' ";
	}
	if($_GET["shipmentMethod"] != ''){
		$sql .= " and shipmentMethod='".$_GET["shipmentMethod"]."' ";
	}
	if($_GET["paymentStatus"] != ''){
		$sql .= " and paymentStatus='".$_GET["paymentStatus"]."' ";
	}
	if($_GET["shipmentStatus"] != ''){
		$sql .= " and shipmentStatus='".$_GET["shipmentStatus"]."' ";
	}


	$searchKey = array("orderNumber","formData");
	if($_GET["export"] == "1"){
	    unset($_GET["per"]);
		$csv = new MTsung\csv();
		if($data["list"] = $order->getListData($sql." and step>1 order by create_date desc",$searchKey,0)){
			foreach ($data["list"] as $key => $value) {
				$data["list"][$key]["formData"] = json_decode(htmlspecialchars_decode($value["formData"]),true);
				$data["list"][$key]["orderList"] = $order->orderListReload($order->getOrderList($value["orderNumber"]));
				$data["list"][$key]["memberId"] = $order_member->getUser($value["memberId"]);
			}

			$output = array(array(
				$console->getLabel("訂單編號"),
				$console->getLabel("會員帳號"),
				$console->getLabel("會員名稱"),
				$console->getLabel("商品總額"),
				$console->getLabel("運費"),
				$console->getLabel("訂單總額"),
				$console->getLabel("付款方式"),
				$console->getLabel("付款狀態"),
				$console->getLabel("出貨方式"),
				$console->getLabel("出貨狀態"),
				$console->getLabel("購買人姓名"),
				$console->getLabel("購買人電話號碼"),
				$console->getLabel("購買人手機號碼"),
				$console->getLabel("購買人性別"),
				$console->getLabel("購買人電子郵件"),
				$console->getLabel("購買人郵遞區號"),
				$console->getLabel("購買人地址"),
				$console->getLabel("備註"),
				$console->getLabel("收件人姓名"),
				$console->getLabel("收件人電話"),
				$console->getLabel("收件人性別"),
				$console->getLabel("收件人電子郵件"),
				$console->getLabel("收件人郵遞區號"),
				$console->getLabel("收件人地址"),
				$console->getLabel("收件人電話號碼"),
				$console->getLabel("收件人手機號碼"),
				$console->getLabel("配送時間"),
				$console->getLabel("發票郵遞區號"),
				$console->getLabel("發票地址"),
				$console->getLabel("索取發票"),
				$console->getLabel("公司抬頭"),
				$console->getLabel("公司統編"),
				$console->getLabel("超商店號"),
				$console->getLabel("超商名稱"),
				$console->getLabel("超商地址"),
				$console->getLabel("建立時間"),
				$console->getLabel("是否為加價購商品"),
				$console->getLabel("商品名稱"),
				$console->getLabel("規格名稱"),
				$console->getLabel("單件金額"),
				$console->getLabel("數量"),
				$console->getLabel("金額")
			));
			foreach ($data["system"]["dataName"] as $key => $value) {
				$output[0][] = $console->getLabel($value);
			}
			foreach ($data["list"] as $key => $value) {
				foreach ($value["orderList"] as $keyList => $valueList) {
					$tempArray = array();
					$tempArray[] = $value["orderNumber"];
					$tempArray[] = $value["memberId"]["account"];
					$tempArray[] = $value["memberId"]["name"];
					$tempArray[] = $value["total"]+$value["deshpriceMoney"]+$value["pointDownMoney"]+$value["couponMoney"];
					$tempArray[] = $value["freight"];
					$tempArray[] = $value["total"]+$value["freight"];
					$tempArray[] = $data["paymentTitle"][$value["paymentMethod"]];
					$tempArray[] = strip_tags($data["paymentStatus"][$value["paymentStatus"]]);
					$tempArray[] = $data["shipmentTitle"][$value["shipmentMethod"]];
					$tempArray[] = strip_tags($data["shipmentStatus"][$value["shipmentStatus"]]);
					$tempArray[] = $value["formData"]["BuyName"];
					$tempArray[] = $value["formData"]["BuyCellPhone"];
					$tempArray[] = $value["formData"]["BuyPhone"];
					$tempArray[] = $value["formData"]["BuySex"];
					$tempArray[] = $value["formData"]["BuyEmail"];
					$tempArray[] = $value["formData"]["BuyZip"];
					$tempArray[] = implode(",",$value["formData"]["BuyAddress"]);
					$tempArray[] = $value["formData"]["memo"];
					$tempArray[] = $value["formData"]["ReceiverName"];
					$tempArray[] = $value["formData"]["ReceiverPhone"];
					$tempArray[] = $value["formData"]["ReceiverCellPhone"];
					$tempArray[] = $value["formData"]["ReceiverSex"];
					$tempArray[] = $value["formData"]["ReceiverEmail"];
					$tempArray[] = $value["formData"]["ReceiverZip"];
					$tempArray[] = implode(",",$value["formData"]["ReceiverAddress"]);
					$tempArray[] = $value["formData"]["ReceiverPhone"];
					$tempArray[] = $value["formData"]["contact_time"];
					$tempArray[] = $value["formData"]["vehicleZip"];
					$tempArray[] = $value["formData"]["vehicleAddress"];
					$tempArray[] = $value["formData"]["invoiceType"];
					$tempArray[] = $value["formData"]["companyName"];
					$tempArray[] = $value["formData"]["companyGUINumber"];
					$tempArray[] = $value["formData"]["CVSStoreID"];
					$tempArray[] = $value["formData"]["CVSStoreName"];
					$tempArray[] = $value["formData"]["CVSAddress"];
					$tempArray[] = $value["create_date"];
					$tempArray[] = $valueList["parentId"]?"yes":"no";
					$tempArray[] = $valueList["name"];
					$tempArray[] = $valueList["specificationsName"];
					$tempArray[] = $valueList["price"];
					$tempArray[] = $valueList["count"];
					$tempArray[] = $valueList["count"]*$valueList["price"];
					foreach ($data["system"]["dataKey"] as $data_key => $data_value) {
						$tempArray[] = $value["formData"][$data_value];
					}

					$output[] = $tempArray;
				}
			}
			$csv->export_xls($output);
		}
	}else{
		if($data["list"] = $order->getListData($sql." and step>1 order by create_date desc",$searchKey)){
			foreach ($data["list"] as $key => $value) {
				$data["list"][$key]["formData"] = json_decode(htmlspecialchars_decode($value["formData"]),true);
				$data["list"][$key]["orderList"] = $order->orderListReload($order->getOrderList($value["orderNumber"]));
			}
		}
	}

	$data["pageNumber"] = $order->pageNumber;

	// $switch["deleteButton"] = 1;

	$switch["exportButton"] = 1;
	$switch["saveButton"] = 1;
	$switch["listList"] = 1;
	$switch["searchBox"] = 1;
}



?>