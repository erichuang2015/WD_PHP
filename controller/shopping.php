<?php 
	include_once('header.php');

	$web_set["titlePrefix"] = $console->getLabel("SHOPPING_CART");
	$breadcru[$breadcruI++] = array(
		"name" => $console->getLabel("SHOPPING_CART"),
		"url" => "/shopping"
	);

	define("SHOPPING_PATH",WEB_PATH.$lang_url."/".$console->path[0]."/");

	//預設第一步
	$step = "1";
	if(isset($console->path[1]) && $console->path[1]){
		$step = $console->path[1];
	}

	//判斷登入
	if(!$member->isLogin() && !isset($_GET["ajax"]) && !$console->webSetting->getValue("noLoginOrder")){
		$_SESSION[FRAME_NAME]["MEMBER_BACK_URI"] = $_SERVER["REQUEST_URI"];
		$console->linkTo(WEB_PATH.$lang_url."/member/login");
		exit;
	}
	
	//設定運費
	$order->setFreight($console->webSetting->getValue("freight"));
	if($order->getShoppingCart("total") >= $console->webSetting->getValue("freeFreightMoney")){//滿額免運
		$order->setFreight(0);
	}
	if($order->getShoppingCart("shipmentMethod")==MTsung\shipmentMethodType::FACE_TO_FACE){//面交不用算運費
		$order->setFreight(0);
	}

	//購物車資訊
	$data["order"] = $order->getShoppingCart();
	$data["order"]["paymentTitle"] = $order->getPaymentTitle($data["order"]["paymentMethod"]);
	$data["order"]["paymentText"] = $order->getPaymentText($data["order"]["paymentMethod"]);
	$data["order"]["shipmentTitle"] = $order->getShipmentTitle($data["order"]["shipmentMethod"]);
	$data["order"]["shipmentText"] = $order->getShipmentText($data["order"]["shipmentMethod"]);

	$data["orderList"] = $order->getShoppingCartList();

	//付款方式
	$shipmentStatus = $order->getPaymentMethodArray();
	$data["paymentMethod"] = array();
	foreach ($shipmentStatus as $key => $value) {
		if($value){
			$data["paymentMethod"][] = array(
				"key" => $key,
				"name" => $order->getPaymentTitle($key),
				"text" => $order->getPaymentText($key)
			);
		}
	}

	//運送方式
	$data["shipmentMethod"] = array();
	if($data["order"]["paymentMethod"]){
		$shipmentStatus = $order->getShipmentMethodArray($data["order"]["paymentMethod"]);
		foreach ($shipmentStatus as $key => $value) {
			if($value){
				$data["shipmentMethod"][] = array(
					"key" => $key,
					"name" => $order->getShipmentTitle($key),
					"text" => $order->getShipmentText($key)
				);
			}
		}
	}

	$data["needMap"] = false;
	switch ($data["order"]["shipmentMethod"]) {
		case MTsung\shipmentMethodType::FAMI:									//超商取貨(綠界) 全家
		case MTsung\shipmentMethodType::UNIMART:								//超商取貨(綠界) 統一超商
		case MTsung\shipmentMethodType::HILIFE:								//超商取貨(綠界) 萊爾富
		case MTsung\shipmentMethodType::FAMIC2C:								//超商取貨(綠界) 全家店到店
		case MTsung\shipmentMethodType::UNIMARTC2C:							//超商取貨(綠界) 統一超商店到店
		case MTsung\shipmentMethodType::HILIFEC2C:								//超商取貨(綠界) 萊爾富店到店
		case MTsung\shipmentMethodType::FAMI_COLLECTION_Y:						//超商取貨付款(綠界) 全家
		case MTsung\shipmentMethodType::UNIMART_COLLECTION_Y:					//超商取貨付款(綠界) 統一超商
		case MTsung\shipmentMethodType::HILIFE_COLLECTION_Y:					//超商取貨付款(綠界) 萊爾富
		case MTsung\shipmentMethodType::FAMIC2C_COLLECTION_Y:					//超商取貨付款(綠界) 全家店到店
		case MTsung\shipmentMethodType::UNIMARTC2C_COLLECTION_Y:				//超商取貨付款(綠界) 統一超商店到店
		case MTsung\shipmentMethodType::HILIFEC2C_COLLECTION_Y:				//超商取貨付款(綠界) 萊爾富店到店
			$data["needMap"] = true;
	}

	//ajax
	if(isset($_GET["ajax"]) && $_GET["ajax"]){

		if(!$member->isLogin() && !$console->webSetting->getValue("noLoginOrder")){
			$_SESSION[FRAME_NAME]["MEMBER_BACK_URI"] = $_SERVER["HTTP_REFERER"];
			$console->outputJson(false,$console->getMessage("PLEASE_LOGIN"),array("toUrl" => WEB_PATH.$lang_url."/member/login"));
		}

		switch ($_GET["ajax"]) {
			case 'usePoint'://使用點數
				$temp = $order->usePoint($_GET["point"]);
				$console->outputJson($temp,$order->message);
				break;

			case 'getShipment'://取得運送方式
				//運送方式
				$data["shipmentMethod"] = array();
				if($_GET["value"]){
					$shipmentStatus = $order->getShipmentMethodArray($_GET["value"]);
					foreach ($shipmentStatus as $key => $value) {
						if($value){
							$data["shipmentMethod"][] = array(
								"key" => $key,
								"name" => $order->getShipmentTitle($key),
								"text" => $order->getShipmentText($key)
							);
						}
					}
				}
				$console->outputJson(true,"",$data["shipmentMethod"]);
				break;

			case 'setPaymentMethod'://設定付款方式
				$order->setPaymentMethod($_GET["value"]);
				break;

			case 'setShipmentMethod'://設定運送方式
				$order->setShipmentMethod($_GET["value"]);
				break;

			case 'addProduct'://新增商品
				$temp = $order->addProduct($_GET["productid"],$_GET["count"],$_GET["specifications"]);
				$console->outputJson($temp,$order->message,$order->getShoppingCartList());
				break;

			case 'editSpecifications'://修改規格
				if($temp = $order->addProduct($_GET["productid"],$_GET["count"],$_GET["newSpecifications"])){
					$order->message = $console->getLabel("EDIT_SPECIFICATIONS_ERROR");
					if($temp = $order->rmProduct($_GET["productid"],$_GET["specifications"])){
						$order->message = $console->getLabel("EDIT_SPECIFICATIONS_OK");
					}
				}
				$console->outputJson($temp,$order->message,$order->getShoppingCartList());
				break;
				
			case 'editCount'://修改數量
				$temp = $order->editProductCount($_GET["productid"],$_GET["count"],$_GET["specifications"]);
				$console->outputJson($temp,$order->message,$order->getShoppingCartList());
				break;

			case 'rmProduct'://刪除商品
				$temp = $order->rmProduct($_GET["productid"],$_GET["specifications"]);
				$console->outputJson($temp,$order->message,$order->getShoppingCartList());
				break;

			case 'addAddProduct'://新增加價購商品
				$temp = $order->addAddProduct($_GET["productid"],$_GET["addProductid"],$_GET["count"],$_GET["specifications"]);
				$console->outputJson($temp,$order->message,$order->getShoppingCartList());
				break;


			case 'editAddCount'://修改加價購數量
				$temp = $order->editAddProductCount($_GET["productid"],$_GET["addProductid"],$_GET["count"],$_GET["specifications"]);
				$console->outputJson($temp,$order->message,$order->getShoppingCartList());
				break;


			case 'rmAddProduct'://刪除加價購商品
				$temp = $order->rmAddProduct($_GET["productid"],$_GET["addProductid"],$_GET["specifications"]);
				$console->outputJson($temp,$order->message,$order->getShoppingCartList());
				break;
				
			case 'shoppingList'://取得購物車內容
				$console->outputJson(true,"",$order->getShoppingCartList());
				break;

			case 'shoppingCart'://取得購物車
				$console->outputJson(true,"",$order->getShoppingCart());
				break;
		}
		$console->outputJson(true,"");

		exit;
	}
	//ajax


	//自訂欄位
	$tempField = new MTsung\dataList($console,PREFIX."orderField","");
	if($tempSystem = $tempField->getData()){
		$tempSystem = $tempSystem[0];
		foreach (array(
						"dataName",
						"dataType",
						"dataKey",
						"dataCount",
						"dataFa",
						"dataRequired",
						"dataOption",
					) as $key => $value) {
				$tempSystem[$value] = explode("|__|", $tempSystem[$value]);
		}
		
		if(is_array($tempSystem["dataOption"])){
			foreach ($tempSystem["dataOption"] as $key => $value) {
				$tempSystem["dataOption"][$key] = explode(",", $value);
			}
		}

		$data["otherField"] = $tempSystem;

		//必填欄位
		foreach ($data["otherField"]["dataKey"] as $key => $value) {
			if($data["otherField"]["dataRequired"][$key]){
				$data["otherField"]["dataRequiredKey"][] = $value;
			}
		}
	}

	//必填欄位
	$requiredArray = array_merge(array("ReceiverName","BuyName","ReceiverEmail","BuyEmail","ReceiverAddress","BuyAddress","ReceiverCellPhone","BuyPhone"),$data["otherField"]["dataRequiredKey"]?$data["otherField"]["dataRequiredKey"]:array());


	switch ($step) {
		case '1':
			break;

		case '2':
			//判斷登入
			if(!$member->isLogin() && !$console->webSetting->getValue("noLoginPostOrder")){
				$_SESSION[FRAME_NAME]["MEMBER_BACK_URI"] = $_SERVER["REQUEST_URI"];
				$console->linkTo(WEB_PATH.$lang_url."/member/login");
				exit;
			}
			try {
				if(is_array($data["order"]["paymentTitle"]) || is_array($data["order"]["shipmentTitle"]) || count($data["orderList"])<1){
					$console->linkto(SHOPPING_PATH);
				}

				switch ($data["order"]["shipmentMethod"]) {

					case MTsung\shipmentMethodType::FAMI:									//超商取貨(綠界) 全家
					case MTsung\shipmentMethodType::UNIMART:								//超商取貨(綠界) 統一超商
					case MTsung\shipmentMethodType::HILIFE:								//超商取貨(綠界) 萊爾富
					case MTsung\shipmentMethodType::FAMIC2C:								//超商取貨(綠界) 全家店到店
					case MTsung\shipmentMethodType::UNIMARTC2C:							//超商取貨(綠界) 統一超商店到店
					case MTsung\shipmentMethodType::HILIFEC2C:								//超商取貨(綠界) 萊爾富店到店
					case MTsung\shipmentMethodType::FAMI_COLLECTION_Y:						//超商取貨付款(綠界) 全家
					case MTsung\shipmentMethodType::UNIMART_COLLECTION_Y:					//超商取貨付款(綠界) 統一超商
					case MTsung\shipmentMethodType::HILIFE_COLLECTION_Y:					//超商取貨付款(綠界) 萊爾富
					case MTsung\shipmentMethodType::FAMIC2C_COLLECTION_Y:					//超商取貨付款(綠界) 全家店到店
					case MTsung\shipmentMethodType::UNIMARTC2C_COLLECTION_Y:				//超商取貨付款(綠界) 統一超商店到店
					case MTsung\shipmentMethodType::HILIFEC2C_COLLECTION_Y:				//超商取貨付款(綠界) 萊爾富店到店
						if(($data["order"]["total"]+$data["order"]["freight"])>20000){
							$console->alert($console->getMessage('MONEY_MAX_20000'),-1);
							exit;
						}
						break;
				}
				
				if($_POST){
					if($order->setFormData($_POST,'',$requiredArray)){
						$console->alert($order->message,-1);
					}

					if(!$orderNumber = $order->payBill()){
						$console->alert($order->message,-1);
					}

					if(!$order->payment($orderNumber)){
						$console->alert($order->message,-1);
					}
					
					if($data["order"]["paymentMethod"] == MTsung\paymentMethodType::PHYSICAL_ATM_TRANSFER){
						$console->alert($console->getMessage("PHYSICAL_ATM_TRANSFER_MAIL_TEXT"),SHOPPING_PATH."3");
					}

					if(!$order->shipment($orderNumber)){
						$console->alert($order->message,-1);
					}

					$console->linkto(SHOPPING_PATH."3");
				}
		    } catch(Exception $e) {
				$console->alert("error: ".$e->getMessage(),-1);
		    }

			break;

		case '3':
			break;

		case 'map':
			$order->cvsMap($data["order"]["orderNumber"]);
			exit;
			break;
		case 'aio':
			// try {

			//     $MerchantID = $console->setting->getValue("ecpayMerchantID");
			//     $HashKey = $console->setting->getValue("ecpayHashKey");
			//     $HashIV = $console->setting->getValue("ecpayHashIV");

		 //        $AL = new ECPay_AllInOne();
		 //        $AL->MerchantID = $MerchantID;
		 //        $AL->HashKey = $HashKey;
		 //        $AL->HashIV = $HashIV;
		 //        $AL->EncryptType = ECPay_EncryptType::ENC_SHA256; // SHA256
		 //        $AL->CheckOutFeedback();

			// 	$orderNumber = substr($_POST["MerchantTradeNo"], 0, ORDER_SIZE);
		 //        $ECPayLog = new MTsung\ECPayLog($console);
		 //        $ECPayLog->setData($_POST);
		 //        if($_POST["RtnCode"] == "1"){
			// 		$order->paymentIsOk($orderNumber);
			// 		$order->shipment($orderNumber);
		 //        }
		 //    } catch(Exception $e) {
		 //        echo $e->getMessage();
		 //        exit;
		 //    }

			$console->linkto(SHOPPING_PATH."3");

			break;
		case 'shipment':

		  //   try {

			 //    $HashKey = $console->setting->getValue("ecpayHashKey");
			 //    $HashIV = $console->setting->getValue("ecpayHashIV");

		  //       $AL = new ECPayLogistics();
		  //       $AL->HashKey = $HashKey;
		  //       $AL->HashIV = $HashIV;
		  //       $AL->CheckOutFeedback($_POST);

		  //       $ECPayLog = new MTsung\ECPayLog($console);
		  //       $_POST["type"] = "shipment";
		  //       $ECPayLog->setData($_POST);

		  //       if($_POST["RtnCode"]=="300"){
				// 	$order->sendMail(substr($data["MerchantTradeNo"], 0, ORDER_SIZE),MTsung\orderSendMailType::ORDER_DATA_RECEIVED);
				// }
		  //   } catch(Exception $e) {
		  //       echo $e->getMessage();
		  //       exit;
		  //   }
			$console->linkto(SHOPPING_PATH."3");

			break;

		default:

			$console->to404();

			break;
	}

	$designName = $console->path[0]."_".$step;