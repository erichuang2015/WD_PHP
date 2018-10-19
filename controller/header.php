<?php 
	//建構中 需要有登入後台才能看內頁
	if($console->setting->getValue("indexPATH")=="demo" && !(isset($_SESSION[FRAME_NAME]["member"]["serback"])&&$_SESSION[FRAME_NAME]["member"]["serback"])){
		$console->linkTo(HTTP_PATH);
		exit;
	}

	$lang = $console->getLanguage();
	$member = new MTsung\member($console,PREFIX.'member');
	$product = new MTsung\product($console,PREFIX."product",$lang);
	$order = new MTsung\shoppingCart($console,$member,$product,PREFIX."shopping_cart",$lang);
	$order->reloadCart();
	$order->setDeshprice(1);//設定折數 0~1
	$order->setPoint(//設定點數
		$console->webSetting->getValue("bonusMoney"),
		$console->webSetting->getValue("bonusPoint"),
		$console->webSetting->getValue("bonusDiscountMoney"),
		$console->webSetting->getValue("bonusDiscountPoint")
	);

	$web_set['lang'] = count($console->getLanguageArray("array"))==1?'':$console->getLanguage();
	$lang_url = ($web_set['lang']?'/'.$web_set['lang']:'');

	//購物內容數量
	$data["orderCount"] = count($order->getShoppingCartList());
	$data["foor"] = (new MTsung\dataList($console,PREFIX."foor",$lang))->getOne();

	//自身網址
	$data["thisUrl"] = HTTP_PATH.$_SERVER["REQUEST_URI"];
	if(WEB_PATH!=""){
		$data["thisUrl"] = str_replace(WEB_PATH."/","",HTTP_PATH).$_SERVER["REQUEST_URI"];
	}
?>