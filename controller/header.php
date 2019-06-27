<?php 
	//建構中 需要有登入後台才能看內頁
	// if($console->path[0]!="demo" && $console->setting->getValue("indexPATH")=="demo" && !(isset($_SESSION[FRAME_NAME]["member"]["serback"]) && $_SESSION[FRAME_NAME]["member"]["serback"])){
	// 	$console->linkTo(HTTP_PATH);
	// 	exit;
	// }
	
	//強制www
	if($console->setting->getValue("forceWww") && (0!==strpos($_SERVER["SERVER_NAME"],"www."))){
		if(!MAIN_SERVER_NAME || (MAIN_SERVER_NAME && (strpos($_SERVER["SERVER_NAME"],MAIN_SERVER_NAME)===false))){
			$console->HTTPStatusCode(301,HTTP."www.".$_SERVER["SERVER_NAME"].$_SERVER['REQUEST_URI']);
		}
	}

	//網址重定向
	$redirect = new MTsung\dataList($console,PREFIX."redirect","");
	if($temp = $redirect->getData("where status=1")){
		foreach ($temp as $key => $value) {
			if($value["url"] == substr($_SERVER['REQUEST_URI'],1)){
				$console->HTTPStatusCode(301,$value["newUrl"]);
			}
		}
	}
	

	$dirArray = array("css","js","images","fonts","svg","data","upload","class","config","controller","include","language","module","sessionTemp","view");
	if(!in_array($console->path[0], $dirArray)){
		if($console->setting->getValue("analyticsCheck")){
			$analytics = new MTsung\analytics($console);
			$analytics->addLog();
		}
	}
		

	$lang = $console->getLanguage();
	$member = new MTsung\member($console,PREFIX.'member');
	$memberGroup = new MTsung\memberGroup($console,PREFIX.'member_group');
	$product = new MTsung\product($console,PREFIX."product",$lang);
	$order = new MTsung\shoppingCart($console,$member,$product,PREFIX."shopping_cart",$lang);
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
	$order->reloadCart();
	$order->setDeshprice(1);//設定折數 0~1
	
	//會員等級 折扣
	if($groupID = $member->getInfo("groupID")){
		if($temp = $memberGroup->getData("where id=? and status='1'",array($groupID))){
			$order->setDeshprice((100-$temp[0]["deshprice"])/100);
		}
	}

	$web_set['lang'] = count($console->getLanguageArray("array"))==1?'':$console->getLanguage();
	$lang_url = ($web_set['lang']?'/'.$web_set['lang']:'');

	//購物內容數量
	$data["orderCount"] = count($order->getShoppingCartList());

	//狀態sql
	$statusSql = " and status='1' and (release_date<='".DATE."' or release_date is null or release_date='') and (expire_date>='".DATE."' or expire_date is null or expire_date='') ";
	//其他資料
	include_once('__otherData.php');

	//麵包屑
	$breadcruI = 0;
	$breadcru[$breadcruI++] = array(
		"name" => $console->getLabel("INDEX"),
		"url" => "/"
	);