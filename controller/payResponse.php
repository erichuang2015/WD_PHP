<?php 
	include_once('header.php'); 

	/**
	 * 金流回傳資訊
	 */
	if(!isset($console->path[1])){
		exit;
	}
	$type = $console->path[1];

    $data = $_POST;

    //取得定單編號
	switch ($type) {
		case 'fisc':
    		$orderNumber = substr($data["lidm"], 0, ORDER_SIZE);
			break;
		default:
		    echo 'Type is Null.';
			break;
	}
	
	//取得該編號的語系class
	$allLanguage = $console->getLanguageArray("array");
	foreach ($allLanguage as $key => $value) {
		$payProduct = new MTsung\product($console,PREFIX."product",$key);
		$payOrder = new MTsung\shoppingCart($console,$member,$payProduct,PREFIX."shopping_cart",$key);
		if($payOrder->getOrder($orderNumber)){
			break;
		}
	}

	$payLog = new MTsung\payLog($console,$type);

	//處理
	switch ($type) {
		case 'fisc':
			$fisc = new MTsung\payFisc(
				$console->setting->getValue("fiscMerID"),
				$console->setting->getValue("fiscMerchantID"),
				$console->setting->getValue("fiscTerminalID")
			);

			if(!$fisc->checkRespToken($data)){//檢查碼
				$console->alert("Check code error.",HTTP_PATH);
			}

			if($data["status"]==0){//成功
		        $payLog->setData($data);
				$payOrder->paymentIsOk($orderNumber);
				$payOrder->shipment($orderNumber,true);
				$console->alert($console->getMessage("PAYMENT_COMPLETED")."\n".$payOrder->message,HTTP_PATH."member/order");
			}

			$console->alert("error:".$data["errcode"].". ".$data["errDesc"],HTTP_PATH);

			break;

		default:
		    echo 'Type is Null.';
			break;
	}

	exit;