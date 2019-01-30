<?php 
	include_once('header.php');
	include_once('__otherData.php');

	//404檔案路徑轉換
	$dirArray = array("css","js","images","fonts","svg");
	if(in_array($console->path[0], $dirArray)){
		$fileName = DATA_PATH.substr($_SERVER['REQUEST_URI'], strlen(WEB_PATH)+1,strlen($_SERVER['REQUEST_URI']));
		if(is_file($fileName)){
			$console->HTTPStatusCode("301",HTTP_PATH.$fileName);
		}else{
			$console->to404($data);
		}
		exit;
	}


	//該頁資料載入
	$menu = new MTsung\menu($console,PREFIX."menu");

	if($temp = $menu->getData("where alias=? and status=1",array($console->path[0]))){
		usort($temp,function($a,$b){//把class放到最前面
			if ($a["features"]!="_other_calss") return 1;
			return 0;
		});
		foreach ($temp as $key => $value) {
			if(($console->path[0]!="index") && ($value["features"]!="_other_form") && (!$web_set["titlePrefix"] || ($web_set["titlePrefix"] && $value["features"]!="_other_calss"))){
				$web_set["titlePrefix"] = $console->getLabel($value["name"]);
			}

			//取得搜尋功能的鍵值與使用功能別名
			$search = getSystemKey($value,'search');
			$youtube = getSystemKey($value,'youtube');
			$imageModule = getSystemKey($value,'imageModule');

			$explodeArray = getExplode($value);
			$explodeArray[] = "class";
			$explodeArray[] = "specificationsID";
			$explodeArray[] = "specifications";
			$explodeArray[] = "stock";
			$explodeArray[] = "addProduct";
			$explodeArray[] = "addProductSpecifications";
			$explodeArray[] = "maxCount";
			$explodeArray[] = "addProductMaxCount";
			$explodeArray[] = "addProductMoney";

			$basic = new MTsung\dataList($console,PREFIX.$console->path[0],$lang);
			switch ($value["features"]) {
				case '_other_basicOne':

					$data["one"] = $basic->getOne("",array(),$explodeArray);
					break;
				case 'basic/product':
				case '_other_basic':

					//資料
					if(isset($console->path[2])){
						$key = $console->path[2];
						if(!$data["one"] = $basic->getOne("and (id=? or urlKey=?) ".$findClassSql,array($key,$key),$explodeArray)){
							$console->to404();
						}
						if(isset($data["one"]["class"]) && $class){
							foreach ($data["one"]["class"] as $oneKey => $oneValue) {
								$data["one"]["class"][$oneKey] = $class->getData("where id='".$oneValue."'")[0];
							}
						}
						if($console->path[0] == "product"){//產品金額
							$data["one"]["price"] = $product->getPrice($data["one"]["id"],$member->isLogin());
						}
					}else{
						if($data["list"] = $basic->getListData("and status='1' ".$findClassSql." order by sort",explode("|__|", $value["dataKey"]),$value["count"])){
							foreach ($data["list"] as $listKey => $listValue) {
								if(isset($data["list"][$listKey]["class"]) && $class){
									$data["list"][$listKey]["class"] = explode("|__|",$listValue["class"]);
									foreach ($data["list"][$listKey]["class"] as $listKey1 => $listValue1) {
										$data["list"][$listKey]["class"][$listKey1] = $class->getData("where id='".$listValue1."'")[0];
									}
								}
							}
						}
						$data["page"] = $basic->pageNumber->getHTML1();
					}
					break;
				case 'class/product':
				case '_other_class':
					$class = new MTsung\dataClass($console,PREFIX.$console->path[0]."_class",$lang);
					$data["class"] = $class->getData("where status='1' order by step",array(),$explodeArray);
					//class
					if(isset($console->path[1])){
						//網址轉換
						if($console->path[1] == "detail"){
							$key = $console->path[2];
							$console->HTTPStatusCode(301,WEB_PATH."/".$console->path[0]."/".explode("|__|",$product->getOne("and (id=? or urlKey=?)",array($key,$key))["class"])[0]."/".$key);
							exit;
						}else if($console->path[1] == "all"){
							$data["oneClass"]["id"] = 0;
						}else{
							$key = $console->path[1];
							if(!$data["one"] = $data["oneClass"] = $class->getOne("and (id=? or urlKey=?)",array($key,$key),$explodeArray)){
								$console->to404();
							}
						}

					}else{
						$data["oneClass"] = $data["class"][0];
					}
					$data["oneClass"] = $console->urlKey($data["oneClass"]);
					if($data["oneClass"]["id"]!=0){
						$_GET["class"] = $data["oneClass"]["id"];
					}

					//所有子節點一並搜尋
					$classArray[] = $data["oneClass"]["id"];
					if($tempC = $class->findChildren($data["oneClass"]["id"])){
						foreach ($tempC as $valueC) {
							$classArray[] = $valueC;
						}
					}
					$findClassSql = $basic->findArrayString("class",$classArray);

					$data["class"] = $class->getTree();//樣板使用的陣列方式

					break;
				default:
					# code...
					break;
			}

			
			if($data["one"]){
				foreach ($data["one"] as $keyOne => $valueOne) {
					//搜尋模組的內容 不做字串陣列轉換
					if(isset($search[$keyOne])){
						$basicOne = new MTsung\dataList($console,PREFIX.$search[$keyOne],$lang);
						foreach ($data["one"][$keyOne] as $keyOne1 => $valueOne1) {
							$data["one"][$keyOne][$keyOne1] = $basicOne->getOne("and id=?",array($valueOne1));
						}
					}
					//YT連結轉換
					if(isset($youtube[$keyOne])){
						$data["one"][$keyOne] = $console->youtubeLink($valueOne);
					}
					//圖片縮圖網址
					if(isset($imageModule[$keyOne])){
						if($data["one"][$keyOne."__min"] = $valueOne){
							foreach ($data["one"][$keyOne."__min"] as $key3 => $value3) {
								$imgTemp = explode(".",$value3);
								$typeTemp = array_pop($imgTemp);
								$data["one"][$keyOne."__min"][$key3] = implode($imgTemp)."_min.".$typeTemp;
							}
						}
					}
				}
			}

			if($value["formData"] && $data["one"]){

				if($_POST){

					//驗證部份(reCAPTCHA沒成功就判斷傳統)
					$Verify = false;
					if(isset($_POST['g-recaptcha-response'])){
						$Verify = $console->checkreCAPTCHA($_POST['g-recaptcha-response']);
					}

					if(!$Verify && isset($_POST['verifycode'])){
						$Verify = $console->checkVerifyCode($_POST['verifycode']);
					}

					if(!$Verify){
						$console->alert($console->getMessage('VERIFY_ERROR'),-1);
					}

					foreach ($_POST as $key => $value) {
						if(strpos($key,"key") !== 0){
							unset($_POST[$key]);
						}
					}

					$input["keyName"] = $data["one"]["dataName"];
					foreach ($_POST as $key => $value) {
						if(is_array($value)){
							$_POST[$key] = implode(",",$value);
						}
					}

					if(isset($_FILES)){
						$allowMIME = array('image/jpeg', 'image/png', 'image/gif', 'image/bmp' , 'image/x-icon' ,'video/mp4', 'audio/mpeg' , 'audio/mp3' ,'application/pdf' ,'application/msword');
						$allowExt = array('jpeg', 'jpg', 'bmp', 'gif', 'png' , 'pdf' , 'ico' , 'mp3' , 'mp4');
						$maxSize = 1048576;//1MB
						$upload = new MTsung\Upload($allowMIME,$allowExt,$maxSize,false,UPLOAD_PATH.'form/'.$console->path[0]);
						$upload->callUploadFile();
						$temp = $upload->getDestination();
						if((!$temp = $upload->getDestination()) && $upload->res['error']){
							$console->alert("Upload error.".$upload->res['error'],-1);
							exit;
						}
						$i = 0;
						foreach ($_FILES as $key => $value) {
							$_POST[$key] = $temp[$i++];
							$_FILES[$key]["tmp_name"] = APP_PATH.$_POST[$key];
						}
						ksort($_POST, SORT_NATURAL);

					}

					$input["keyData"] = implode("|__|",$_POST);
					$form = new MTsung\form($console,PREFIX.$console->path[0]."_form",$lang);
					if($form->setData($input)){
						$form->sendForm(array(
							"keyName" => $data["one"]["dataName"],
							"keyData" => explode("|__|", $input["keyData"])
						),WEB_PATH."/".$console->path[0]);
					}else{
						$console->alert($form->message,-1);
					}
				}

				foreach ($data["one"]["dataOption"] as $key => $value) {
					$data["one"]["dataOption"][$key] = explode(",", $value);
				}
			}
		}
	}

	/**
	 * 取得使用value功能的鍵值
	 */
	function getSystemKey($temp,$value){
		$searchTemp = array_combine(explode("|__|",$temp["dataKey"]), explode("|__|",$temp["dataType"]));
		$searchTemp1 = array_combine(explode("|__|",$temp["dataKey"]), explode("|__|",$temp["dataSearch"]));
		if($searchKey = array_keys($searchTemp,$value)){
			foreach ($searchKey as $keyS => $valueS) {
				$search[$valueS] = $searchTemp1[$valueS];
			}
		}
		return $search;
	}

	print_r($data);exit;