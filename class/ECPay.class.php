<?php


/**
 * 綠界串接
 * MTsung by 20180903
 */
namespace MTsung{

	include_once(APP_PATH.'include/ECPay/AioSDK/sdk/ECPay.Payment.Integration.php');
	include_once(APP_PATH.'include/ECPay/PayLogisticSDK/SDK/ECPay.Logistics.Integration.php');

	class ECPay{
		use userDeviceInfomation;
		var $serviceURL;
		var $returnURL = HTTP_PATH."ECPayResponse/";
		var $merchantID;
		var $hashKey;
		var $hashIV;
		var $rand;

		var $Device = array(
			"Tablet" => \Device::MOBILE,
			"Mobile" => \Device::MOBILE,
			"Desktop" => \Device::PC
		);

		var $ChoosePayment = array(
			paymentMethodType::ONLINE_CARD_ECPAY => \ECPay_PaymentMethod::Credit,//信用卡付費
			paymentMethodType::INTERNET_ATM_TRANSFER_ECPAY => \ECPay_PaymentMethod::WebATM,//網路 ATM
			paymentMethodType::PHYSICAL_ATM_TRANSFER_ECPAY => \ECPay_PaymentMethod::ATM,//自動櫃員機
			paymentMethodType::CVS_ECPAY => \ECPay_PaymentMethod::CVS,//超商代碼
			paymentMethodType::BARCODE_ECPAY => \ECPay_PaymentMethod::BARCODE//超商條碼
		);

		var $LogisticsType = array(
			shipmentMethodType::TCAT_BLACK_CAT => \LogisticsType::HOME,//宅配(綠界) 黑貓
			shipmentMethodType::ECAN_HOME_DELIVERY => \LogisticsType::HOME,//宅配(綠界) 宅配通
			shipmentMethodType::FAMI => \LogisticsType::CVS,//超商取貨(綠界) 全家
			shipmentMethodType::UNIMART => \LogisticsType::CVS,//超商取貨(綠界) 統一超商
			shipmentMethodType::HILIFE => \LogisticsType::CVS,//超商取貨(綠界) 萊爾富
			shipmentMethodType::FAMIC2C => \LogisticsType::CVS,//超商取貨付款(綠界) 全家店到店
			shipmentMethodType::UNIMARTC2C => \LogisticsType::CVS,//超商取貨付款(綠界) 統一超商店到店
			shipmentMethodType::HILIFEC2C  => \LogisticsType::CVS,//超商取貨付款(綠界) 萊爾富店到店
			shipmentMethodType::FAMI_COLLECTION_Y => \LogisticsType::CVS,//取貨付款
			shipmentMethodType::UNIMART_COLLECTION_Y => \LogisticsType::CVS,//取貨付款
			shipmentMethodType::HILIFE_COLLECTION_Y => \LogisticsType::CVS,//取貨付款
			shipmentMethodType::FAMIC2C_COLLECTION_Y => \LogisticsType::CVS,//取貨付款
			shipmentMethodType::UNIMARTC2C_COLLECTION_Y => \LogisticsType::CVS,//取貨付款
			shipmentMethodType::HILIFEC2C_COLLECTION_Y => \LogisticsType::CVS//取貨付款
		);

		var $LogisticsSubType = array(
			shipmentMethodType::TCAT_BLACK_CAT => \LogisticsSubType::TCAT,//宅配(綠界) 黑貓
			shipmentMethodType::ECAN_HOME_DELIVERY => \LogisticsSubType::ECAN,//宅配(綠界) 宅配通
			shipmentMethodType::FAMI => \LogisticsSubType::FAMILY,//超商取貨(綠界) 全家
			shipmentMethodType::UNIMART => \LogisticsSubType::UNIMART,//超商取貨(綠界) 統一超商
			shipmentMethodType::HILIFE => \LogisticsSubType::HILIFE,//超商取貨(綠界) 萊爾富
			shipmentMethodType::FAMIC2C => \LogisticsSubType::FAMILY_C2C,//超商取貨(綠界) 全家店到店
			shipmentMethodType::UNIMARTC2C => \LogisticsSubType::UNIMART_C2C,//超商取貨(綠界) 統一超商店到店
			shipmentMethodType::HILIFEC2C  => \LogisticsSubType::HILIFE_C2C,//超商取貨(綠界) 萊爾富店到店
			shipmentMethodType::FAMI_COLLECTION_Y => \LogisticsSubType::FAMILY,//取貨付款
			shipmentMethodType::UNIMART_COLLECTION_Y => \LogisticsSubType::UNIMART,//取貨付款
			shipmentMethodType::HILIFE_COLLECTION_Y => \LogisticsSubType::HILIFE,//取貨付款
			shipmentMethodType::FAMIC2C_COLLECTION_Y => \LogisticsSubType::FAMILY_C2C,//取貨付款
			shipmentMethodType::UNIMARTC2C_COLLECTION_Y => \LogisticsSubType::UNIMART_C2C,//取貨付款
			shipmentMethodType::HILIFEC2C_COLLECTION_Y => \LogisticsSubType::HILIFE_C2C//取貨付款
		);

		var $IsCollection = array(
			shipmentMethodType::TCAT_BLACK_CAT => \IsCollection::NO,//宅配(綠界) 黑貓
			shipmentMethodType::ECAN_HOME_DELIVERY => \IsCollection::NO,//宅配(綠界) 宅配通
			shipmentMethodType::FAMI => \IsCollection::NO,//超商取貨(綠界) 全家
			shipmentMethodType::UNIMART => \IsCollection::NO,//超商取貨(綠界) 統一超商
			shipmentMethodType::HILIFE => \IsCollection::NO,//超商取貨(綠界) 萊爾富
			shipmentMethodType::FAMIC2C => \IsCollection::NO,//超商取貨(綠界) 全家店到店
			shipmentMethodType::UNIMARTC2C => \IsCollection::NO,//超商取貨(綠界) 統一超商店到店
			shipmentMethodType::HILIFEC2C  => \IsCollection::NO,//超商取貨(綠界) 萊爾富店到店
			shipmentMethodType::FAMI_COLLECTION_Y => \IsCollection::YES,//取貨付款
			shipmentMethodType::UNIMART_COLLECTION_Y => \IsCollection::YES,//取貨付款
			shipmentMethodType::HILIFE_COLLECTION_Y => \IsCollection::YES,//取貨付款
			shipmentMethodType::FAMIC2C_COLLECTION_Y => \IsCollection::YES,//取貨付款
			shipmentMethodType::UNIMARTC2C_COLLECTION_Y => \IsCollection::YES,//取貨付款
			shipmentMethodType::HILIFEC2C_COLLECTION_Y => \IsCollection::YES//取貨付款
		);

		function __construct($merchantID="2000132",$hashKey="5294y06JbISpM5x9",$hashIV="v77hoKGq4kWxNNIS"){
			if($merchantID == "2000132"){
				//測試
				$this->serviceURL = "https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5";
			}else{
				$this->serviceURL = "https://payment.ecpay.com.tw/Cashier/AioCheckOut/V5";
			}
			$this->merchantID = $merchantID;
			$this->hashKey = $hashKey;
			$this->hashIV = $hashIV;
			$this->rand = strtoupper(base_convert(microtime(true) % 100000,10,36));				//訂單編號後墜 防止訂單編號重複用
		}

		/**
		 * 創建金流訂單
		 * @return [type] [description]
		 */
		function createOrder($order,$orderList){


			if(!isset($this->ChoosePayment[$order["paymentMethod"]])){
				return false;
			}


	    	$obj = new \ECPay_AllInOne();
	   
	        //服務參數
	        $obj->ServiceURL  = $this->serviceURL;   										   	//服務位置
	        $obj->HashKey     = $this->hashKey;                                           		//測試用Hashkey，請自行帶入ECPay提供的HashKey
	        $obj->HashIV      = $this->hashIV;                                           		//測試用HashIV，請自行帶入ECPay提供的HashIV
	        $obj->MerchantID  = $this->merchantID;                                              //測試用MerchantID，請自行帶入ECPay提供的MerchantID
	        $obj->EncryptType = '1';                                                            //CheckMacValue加密類型，請固定填入1，使用SHA256加密


	        //基本參數(請依系統規劃自行調整)
	        $obj->Send['ReturnURL']         = $this->returnURL."aio";    					//付款完成通知回傳的網址
	        $obj->Send['MerchantTradeNo']   = $order["orderNumber"].$this->rand;                //訂單編號
	        $obj->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');                       		//交易時間
	        $obj->Send['TotalAmount']       = $order["total"] + $order["freight"];       		//交易金額
	        $obj->Send['TradeDesc']         = "金流付款";                        		 		//交易描述
	        $obj->Send['ChoosePayment']     = $this->ChoosePayment[$order["paymentMethod"]];    //付款方式
	        $obj->Send['OrderResultURL']    = HTTP_PATH."shopping/aio";

	        //訂單的商品資料
	        foreach ($orderList as $key => $value) {
	        	array_push($obj->Send['Items'], 
	        		array(
		        		'Name' => $value["name"],
		        	 	'Price' => (int)$value["price"],
		        	 	'Currency' => "元",
		        	 	'Quantity' => (int)$value["count"],
		        	 	'URL' => HTTP_PATH."product/".$value["productId"]
		        	)
	        	);
	        }

/*
	        //ATM 延伸參數(可依系統需求選擇是否代入)
	        $obj->SendExtend['ExpireDate'] = 3 ;     //繳費期限 (預設3天，最長60天，最短1天)
	        $obj->SendExtend['PaymentInfoURL'] = ""; //伺服器端回傳付款相關資訊。

	        //BARCODE超商條碼延伸參數(可依系統需求選擇是否代入)
    		//CVS超商代碼延伸參數(可依系統需求選擇是否代入)
	        $obj->SendExtend['Desc_1']            = '';      //交易描述1 會顯示在超商繳費平台的螢幕上。預設空值
	        $obj->SendExtend['Desc_2']            = '';      //交易描述2 會顯示在超商繳費平台的螢幕上。預設空值
	        $obj->SendExtend['Desc_3']            = '';      //交易描述3 會顯示在超商繳費平台的螢幕上。預設空值
	        $obj->SendExtend['Desc_4']            = '';      //交易描述4 會顯示在超商繳費平台的螢幕上。預設空值
	        $obj->SendExtend['PaymentInfoURL']    = '';      //預設空值
	        $obj->SendExtend['ClientRedirectURL'] = '';      //預設空值
	        $obj->SendExtend['StoreExpireDate']   = '';      //預設空值

	        //Credit信用卡分期付款延伸參數(可依系統需求選擇是否代入)
	        //以下參數不可以跟信用卡定期定額參數一起設定
	        $obj->SendExtend['CreditInstallment'] = '' ;    //分期期數，預設0(不分期)，信用卡分期可用參數為:3,6,12,18,24
	        $obj->SendExtend['Redeem'] = false ;           //是否使用紅利折抵，預設false
	        $obj->SendExtend['UnionPay'] = false;          //是否為聯營卡，預設false;
*/

    		$obj->CheckOut();
		}

		/**
		 * 物流訂單生成
		 * formData須包含
		 * 		ReceiverName
		 *   	ReceiverPhone
		 *    	ReceiverCellPhone
		 *     	ReceiverEmail
		 *      CVSStoreID 超商
		 *      ReceiverZipCode 宅配
		 *      ReceiverAddress	宅配
		 * @param  [type] $order     [description]
		 * @param  [type] $orderList [description]
		 * @return [type]            [description]
		 */
		function createShippingOrder($order,$orderList,$isBG=false){


			if(!isset($this->LogisticsSubType[$order["shipmentMethod"]])){
				return false;
			}

			/**
			 * 尚未付款
			 */
			if($this->IsCollection[$order["shipmentMethod"]] == \IsCollection::NO && $order["paymentStatus"] == paymentStatusType::NO){
				return false;
			}

			/**
			 * 商品名稱
			 */
			$goodsName = "";
			foreach ($orderList as $k => $v){
				if($goodsName!=''){
					$goodsName .= ',';
				}
				$goodsName .= $v["name"];			
			}

			$receiver = json_decode($order["formData"],true);

			global $webSetting;

			$AL = new \ECPayLogistics();
	        $AL->HashKey = $this->hashKey;
	        $AL->HashIV = $this->hashIV;
	        $AL->Send = array(
	            'MerchantID' => $this->merchantID,
	            'MerchantTradeNo' => $order["orderNumber"].$this->rand,
	            'MerchantTradeDate' => date('Y/m/d H:i:s'),
				'LogisticsType' => $this->LogisticsType[$order["shipmentMethod"]],
	            'LogisticsSubType' => $this->LogisticsSubType[$order["shipmentMethod"]],
	            'GoodsAmount' => $order["total"] + $order["freight"],
	            'CollectionAmount' => $order["total"] + $order["freight"],
	            'IsCollection' => $this->IsCollection[$order["shipmentMethod"]],
	            'GoodsName' => $this->stringFilter($goodsName,25),
	            'SenderName' => $webSetting->getValue("senderName"),
	            'SenderPhone' => $webSetting->getValue("senderPhone"),
	            'SenderCellPhone' => $webSetting->getValue("senderCellPhone"),
	            'ReceiverName' => $receiver["ReceiverName"],
	            'ReceiverPhone' => $receiver["ReceiverPhone"],
	            'ReceiverCellPhone' => $receiver["ReceiverCellPhone"],
	            'ReceiverEmail' => $receiver["ReceiverEmail"],
	            'TradeDesc' => '物流',
	            'ServerReplyURL' => $this->returnURL.'shipment',								//物流狀態會傳到這
	            'ClientReplyURL' => $isBG?"":HTTP_PATH.'shopping/shipment',						//物流訂單生成後會連到這
	            'LogisticsC2CReplyURL' => $this->returnURL.'logisticsC2C',						//門市要更新的話會連到這
            	'Remark' => $this->stringFilter($goodsName,200,""),
	            'PlatformID' => '',
	        );


			switch ($this->LogisticsType[$order["shipmentMethod"]]) {
				case \LogisticsType::CVS:

			        $AL->SendExtend = array(
			            'ReceiverStoreID' => $receiver["CVSStoreID"],
			            'ReturnStoreID' => '',
			        );
					break;
				case \LogisticsType::HOME:

			        $AL->SendExtend = array(
			            'SenderZipCode' => $webSetting->getValue("senderZipCode"),
			            'SenderAddress' => $webSetting->getValue("senderAddress"),
			            'ReceiverZipCode' => $receiver["ReceiverZipCode"],
			            'ReceiverAddress' => $receiver["ReceiverAddress"],
			            'Temperature' => \Temperature::FREEZE, 									//溫層
			            'Distance' => \Distance::SAME,											//距離
			            'Specification' => \Specification::CM_120,								//大小
			            'ScheduledDeliveryTime' => $receiver["ScheduledDeliveryTime"] ? $receiver["ScheduledDeliveryTime"] : \ScheduledDeliveryTime::UNLIMITED				//時間
			        );
					break;
			}

			//幕前幕後
			if(!$isBG){
        		echo $AL->CreateShippingOrder("");
			}else{
        		return $AL->BGCreateShippingOrder();
			}
		}

		/**
		 * 選擇超商
		 * @param  [type] $order [description]
		 * @return [type]        [description]
		 */
		function cvsMap($order){
	        $AL = new \ECPayLogistics();
	        $AL->Send = array(
	            'MerchantID' => $this->merchantID,
            	'MerchantTradeNo' => $order["orderNumber"],
	            'LogisticsSubType' => $this->LogisticsSubType[$order["shipmentMethod"]],
	            'IsCollection' => $this->IsCollection[$order["shipmentMethod"]],
	            'ServerReplyURL' => $this->returnURL.'map',								//門市選完會回傳到這
	            'Device' => $this->Device[$this->getDevice()]
	        );
	        echo $AL->CvsMap('');
		}

		/**
		 * 將上限後的字&陣列內的字過濾
		 * @param  [type] $value [description]
		 * @param  [type] $size  [description] 中文
		 * @param  [type] $array [description]
		 * @return [type]        [description]
		 */
		function stringFilter($value,$size,$array=array("^","'","`","!","@","#","%","&","*","+","\\","\"","<",">","｜","_","[","]")){
			$value =  str_replace($array,"",$value);
			return mb_strwidth($value,"utf-8")>$size ? mb_substr($value,0,$size, "utf-8") : $value;
		}




		/**
		 * 產生托運單(宅配)/一段標(超商取貨B2C)
		 * @param  [type] $AllPayLogisticsID [description]
		 * @return [type] [description]
		 */
		function printTradeDoc($AllPayLogisticsID){
			$AL = new \ECPayLogistics();
	        $AL->HashKey = $this->hashKey;
	        $AL->HashIV = $this->hashIV;
	        $AL->Send = array(
	            'MerchantID' => $this->merchantID,
	            'AllPayLogisticsID' => $AllPayLogisticsID,
	            'PlatformID' => ''//由綠界科技提供，此參數為專案合作的平台商使用，一般廠商介接請放空值。若為專案合作的平台商使用時，MerchantID 請帶賣家所綁定的MerchantID。

	        );
	        echo $AL->PrintTradeDoc('','');
		}

		/**
		 * 列印繳款單(統一超商C2C)
		 * @param  [type] $AllPayLogisticsID [description]
		 * @param  [type] $CVSPaymentNo      [description]
		 * @param  [type] $CVSValidationNo   [description]
		 * @return [type]                    [description]
		 */
		function printUnimartC2CBill($AllPayLogisticsID,$CVSPaymentNo,$CVSValidationNo){
	        $AL = new \ECPayLogistics();
	        $AL->HashKey = $this->hashKey;
	        $AL->HashIV = $this->hashIV;
	        $AL->Send = array(
	            'MerchantID' => $this->merchantID,
	            'AllPayLogisticsID' => $AllPayLogisticsID,
	            'CVSPaymentNo' => $CVSPaymentNo,
	            'CVSValidationNo' => $CVSValidationNo,
	            'PlatformID' => ''//由綠界科技提供，此參數為專案合作的平台商使用，一般廠商介接請放空值。若為專案合作的平台商使用時，MerchantID 請帶賣家所綁定的MerchantID。
	        );
	        echo $AL->PrintUnimartC2CBill('','');
		}

		/**
		 * 萊爾富列印小白單(萊爾富超商C2C)
		 * @param  [type] $AllPayLogisticsID [description]
		 * @param  [type] $CVSPaymentNo      [description]
		 * @return [type]                    [description]
		 */
		function printHiLifeC2CBill($AllPayLogisticsID,$CVSPaymentNo){
	        $AL = new \ECPayLogistics();
	        $AL->HashKey = $this->hashKey;
	        $AL->HashIV = $this->hashIV;
	        $AL->Send = array(
	            'MerchantID' => $this->merchantID,
	            'AllPayLogisticsID' => $AllPayLogisticsID,
	            'CVSPaymentNo' => $CVSPaymentNo,
	            'PlatformID' => ''
	        );
	        echo $AL->PrintHiLifeC2CBill('','');
		}

		/**
		 * 全家列印小白單(全家超商C2C)
		 * @param  [type] $AllPayLogisticsID [description]
		 * @param  [type] $CVSPaymentNo      [description]
		 * @return [type]                    [description]
		 */
		function printFamilyC2CBill($AllPayLogisticsID,$CVSPaymentNo){
	        $AL = new \ECPayLogistics();
	        $AL->HashKey = $this->hashKey;
	        $AL->HashIV = $this->hashIV;
	        $AL->Send = array(
	            'MerchantID' => $this->merchantID,
	            'AllPayLogisticsID' => $AllPayLogisticsID,
	            'CVSPaymentNo' => $CVSPaymentNo,
	            'PlatformID' => ''
	        );
	        echo $AL->PrintFamilyC2CBill('','');
		}





	}
}