<?php 
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

	//購物內容數量
	$data["orderCount"] = count($order->getShoppingCartList());
?>