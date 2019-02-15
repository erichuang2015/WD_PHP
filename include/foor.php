<?php	
	/**
	 * 每個controller的下版
	 */
	//把沒POST的圖片刪除
	if(isset($_SESSION[FRAME_NAME]["PICTURE_TEMP"])){
		foreach ($_SESSION[FRAME_NAME]["PICTURE_TEMP"] as $key => $value) {
			while(strpos($value,'../')!==false || strpos($value,'..\\')!==false){
				$value = str_replace('../',"",$value);
				$value = str_replace('..\\',"",$value);
			}
			$path = APP_PATH.UPLOAD_PATH;
			if(is_file($path.$value)){
	            unlink($path.$value);
	            $minImgPath = $path.str_replace(".".pathinfo($value, PATHINFO_EXTENSION),"_min.".pathinfo($value, PATHINFO_EXTENSION),$value);
	            if(is_file($minImgPath)){
	            	unlink($minImgPath);
	            }
	        }
			unset($_SESSION[FRAME_NAME]["PICTURE_TEMP"][$key]);
		}	
	}
	
	if($console->langSessionName!="Serback"){
		if(isset($data["one"])){
			if(isset($data["one"]['pageTitle']) && $data["one"]['pageTitle']){
				$web_set['title'] = $data["one"]['pageTitle'];
			}
			if(isset($data["one"]['pageMeta']) && $data["one"]['pageMeta']){
				$web_set['meta'] = $data["one"]['pageMeta'];
			}
			if(isset($data["one"]['pageImage']) && $data["one"]['pageImage']){
				$web_set['ogImage'] = $data["one"]['pageImage'];
			}
			if(isset($data["one"]['pageDescription']) && $data["one"]['pageDescription']){
				$web_set['ogDescription'] = $data["one"]['pageDescription'];
			}
		}

		if(isset($data["list"]) && $data["list"]){
			$data["list"] = $console->urlKey($data["list"]);
		}

		if(isset($data["class"]) && $data["class"]){
			$data["class"] = $console->urlKey($data["class"]);
		}
	}


	$message = $console->message;
	$label_back = $console->serbackLabel;

	//多國語標籤
	$web_set['hreflang'] = $console->getHreflang();

	//css js用
	$web_set['main_path'] = WEB_PATH;
	$web_set['serback_path'] = SERBACK_PATH;
	$web_set['data_path'] = WEB_PATH.DATA_PATH;
	if($web_set['data_path'][0]!="/") $web_set['data_path'] = "/".$web_set['data_path'];

	//完整網址(不含控制器)
	$web_set['http_path'] = HTTP_PATH;

	//自身網址(不含?以後)
	$web_set["thisUrl"] = explode('?',HTTP.$_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"])[0];

	//管理語系
	if(isset($_SESSION[FRAME_NAME]["SETTING_LANG"])){
		$web_set['setting_lang'] = $_SESSION[FRAME_NAME]["SETTING_LANG"];
	}
	
	//語言
	$web_set['lang'] = count($console->getLanguageArray("array"))==1?'':$console->getLanguage();
	
	//一般連結用
	$web_set['main_url'] = $web_set['main_path'].($web_set['lang']?'/'.$web_set['lang']:'');
	$web_set['serback_url'] = $web_set['main_url'].'/serback';

	//現在時間
	$web_set['date'] = DATE;

	//
	if(!isset($web_set['title'])){
		$web_set['title'] = $console->webSetting->getValue('webTitle');
		if($web_set["titlePrefix"]){
			$web_set['title'] = $web_set["titlePrefix"]."-".$web_set['title'];
		}
	}
	if(!isset($web_set['meta'])){
		$web_set['meta'] = htmlspecialchars_decode($console->webSetting->getValue('webMeta'));
	}
	if(!isset($web_set['ogImage'])){
		$web_set['ogImage'] = $console->webSetting->getValue('webImage');
	}
	if(!isset($web_set['ogDescription'])){
		$web_set['ogDescription'] = $console->webSetting->getValue('webDescription');
	}

	$web_set['serverName'] = MAIN_SERVER_NAME;

	//網站圖示
	$web_set['icon'] = $console->setting->getValue('icon');

	$web_set = array_merge($web_set,$console->webSetting->getValue());
	$web_set = array_merge($web_set,$console->setting->getValue());

	$web_set = array_map("htmlspecialchars_decode",$web_set);

	$console->design->setData("message", @$message);
	$console->design->setData("label_back", @$label_back);
	$console->design->setData("module", @$module);//模組
	$console->design->setData("data", @$data);
	$console->design->setData("setting", @$console->setting->getValue());
	if(isset($memberInfo)){
		$console->design->setData("member", $memberInfo);
	}else if(isset($member) && isset($_SESSION[FRAME_NAME]["member"][$member->sessionName]['id'])){
		$console->design->setData("member", $member->getUser($_SESSION[FRAME_NAME]["member"][$member->sessionName]['id']));
	}
	$console->design->setData("web", @$web_set);
	$console->design->setData("lang", @$console->getLanguage());//語言
	$console->design->setData("langDefault", LANG);//預設語言
	$console->design->setData("switch", @$switch);//開關
	$console->design->setData("breadcru", @$breadcru);//麵包屑
	$console->design->setData("console", $console);
	//沒設定$designName則使用controller當樣板名稱
	if($_GET) $_GET = $console->XXSDataVerifty($_GET);//最後再htmlspecialchars
	if($_POST) $_POST = $console->XXSDataVerifty($_POST);//最後再htmlspecialchars
	if(isset($designName) && $designName){
		$console->design->loadDisplay($designName.'.html');
	}else{
		$console->design->loadDisplay($console->controller.'.html');
	}
?>