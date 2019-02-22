<?php
$switch["buttonBox"] = 1;
$data["listUrl"] = $web_set['serback_url'].'/'.$console->path[0];

$order_member = new MTsung\member($console,PREFIX.'member');
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
	$data["list"] = $order->getListData($sql." and step>1 order by orderNumber desc",$searchKey);
	$data["total"] = (int)$console->conn->getRow("select sum(total+freight) from ".$order->table." ".$order->getSqlWhere($searchKey).$sql." and step>1")[0];

	$data["pageNumber"] = $order->pageNumber;

	$switch["deleteButton"] = 1;

	$switch["saveButton"] = 1;
	$switch["listList"] = 1;
	$switch["searchBox"] = 1;
}



?>