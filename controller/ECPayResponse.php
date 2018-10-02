<?php 
	include_once('header.php'); 

	/**
	 * 綠界回傳資訊
	 */
	$type = "";
	if(isset($console->path[1])){
		$type = $console->path[1];
	}


    $MerchantID = $console->setting->getValue("ecpayMerchantID");
    $HashKey = $console->setting->getValue("ecpayHashKey");
    $HashIV = $console->setting->getValue("ecpayHashIV");

    $data = $_POST;
    $data["type"] = $type;
    $orderNumber = substr($data["MerchantTradeNo"], 0, ORDER_SIZE);

    if($orderNumber){
		$allLanguage = $console->getLanguageArray("array");
		foreach ($allLanguage as $key => $value) {
			$ECPayProduct = new MTsung\product($console,PREFIX."product",$key);
			$ECPayOrder = new MTsung\shoppingCart($console,$member,$ECPayProduct,PREFIX."shopping_cart",$key);
			if($ECPayOrder->getOrder($orderNumber)){
				break;
			}
		}
    }
    		// 測試用
			// $ECPayProduct = new MTsung\product($console,PREFIX."product",$lang);
			// $ECPayOrder = new MTsung\shoppingCart($console,$member,$ECPayProduct,PREFIX."shopping_cart",$lang);
			// $ECPayOrder->sendMail("WD00000005",MTsung\orderSendMailType::CONSUMERS_DID_NOT_PICK_UP_THE_GOODS_FOR_SEVEN_DAYS);
			// exit;

	switch ($type) {
		case 'aio':
			try {

		        $AL = new ECPay_AllInOne();
		        $AL->MerchantID = $MerchantID;
		        $AL->HashKey = $HashKey;
		        $AL->HashIV = $HashIV;
		        $AL->EncryptType = ECPay_EncryptType::ENC_SHA256; // SHA256
		        $AL->CheckOutFeedback();

		        $ECPayLog = new MTsung\ECPayLog($console);
		        $ECPayLog->setData($data);
		        if($data["RtnCode"] == "1"){
					$ECPayOrder->paymentIsOk($orderNumber);
					$ECPayOrder->shipment($orderNumber,true);
		        }

		        echo '1|OK';
		    } catch(Exception $e) {
		        echo '0|' . $e->getMessage();
		    }

			break;

		case 'shipment':
		    try {

		        $AL = new ECPayLogistics();
		        $AL->HashKey = $HashKey;
		        $AL->HashIV = $HashIV;
		        $AL->CheckOutFeedback($_POST);

		        $ECPayLog = new MTsung\ECPayLog($console);
		        $ECPayLog->setData($data);

				switch ($data["RtnCode"]) {
					case '300'://已收到訂單資料
						$ECPayOrder->sendMail($orderNumber,MTsung\orderSendMailType::ORDER_DATA_RECEIVED);
						break;

					case '2030'://商品已送至物流中心
					case '3024':
						$ECPayOrder->shipmentIsOk($orderNumber);
						break;

					case '2063'://商品已送達門市
					case '3018':
						$ECPayOrder->sendMail($orderNumber,MTsung\orderSendMailType::GOODS_HAVE_BEEN_DELIVERED_TO_THE_STORE);
						break;

					case '2067'://消費者成功取件
					case '3022':
						$ECPayOrder->sendMail($orderNumber,MTsung\orderSendMailType::SUCCESSFUL_CUSTOMER_PICKUP);
						$ECPayOrder->paymentIsOk($orderNumber);
						$ECPayOrder->payPoint($orderNumber);
						break;

					case '2074'://消費者七天未取件
					case '3020':
						$ECPayOrder->sendMail($orderNumber,MTsung\orderSendMailType::CONSUMERS_DID_NOT_PICK_UP_THE_GOODS_FOR_SEVEN_DAYS);
						break;
				}


		        echo '1|OK';
		    } catch(Exception $e) {
		        echo '0|' . $e->getMessage();
		    }

			break;
		case 'logisticsC2C':
		    try {

		        $AL = new ECPayLogistics();
		        $AL->HashKey = $HashKey;
		        $AL->HashIV = $HashIV;
		        $AL->CheckOutFeedback($_POST);

		        $ECPayLog = new MTsung\ECPayLog($console);
		        $ECPayLog->setData($data);


		        //更換門市

		        echo '1|OK';
		    } catch(Exception $e) {
		        echo '0|' . $e->getMessage();
		    }

			break;

		case 'map':

			// window.open(_jsPath+'/serback/setting/web?test=1'
		    //             , 'ECPayMap'
		    //             , config='height=800,width=1200,left='+(window.screen.width-1200)/2+',top='+(window.screen.height-800)/2);


            echo '
                <script>
                	window.opener.mapData=JSON.parse(\''.json_encode($_POST).'\');
                	window.opener.mapLoad();
                	window.close();
                </script>
            ';
			break;

		default:
		    echo '0|Type is Null.';
			break;
	}

	exit;
?>