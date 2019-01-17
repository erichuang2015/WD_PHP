<?php 
	//建構中 需要有登入後台才能看內頁
	if($console->path[0]!="demo" && $console->setting->getValue("indexPATH")=="demo" && !(isset($_SESSION[FRAME_NAME]["member"]["serback"]) && $_SESSION[FRAME_NAME]["member"]["serback"])){
		$console->linkTo(HTTP_PATH);
		exit;
	}
	
	
	$dirArray = array("css","js","images","fonts","svg","data","class","config","controller","include","language","module","sessionTemp","view");
	if(!in_array($console->path[0], $dirArray)){
		if($console->setting->getValue("analyticsCheck")){
			$analytics = new MTsung\analytics($console);
			$analytics->addLog();
		}
	}
		

	$lang = $console->getLanguage();
	$member = new MTsung\member($console,PREFIX.'member');
	$product = new MTsung\product($console,PREFIX."product",$lang);
	$order = new MTsung\shoppingCart($console,$member,$product,PREFIX."shopping_cart",$lang);
	$order->reloadCart();
	$order->setDeshprice(1);//設定折數 0~1
	if($console->setting->getValue("pointCheck")){//設定點數
		$order->setPoint(
			$console->webSetting->getValue("bonusMoney"),
			$console->webSetting->getValue("bonusPoint"),
			$console->webSetting->getValue("bonusDiscountMoney"),
			$console->webSetting->getValue("bonusDiscountPoint")
		);
	}else{
		$order->setPoint(0,0,0,0);
	}

	$web_set['lang'] = count($console->getLanguageArray("array"))==1?'':$console->getLanguage();
	$lang_url = ($web_set['lang']?'/'.$web_set['lang']:'');

	//購物內容數量
	$data["orderCount"] = count($order->getShoppingCartList());

?>