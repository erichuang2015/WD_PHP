<?php 
	include_once('header.php');

	//404檔案路徑轉換
	$dirArray = array("css","js","images","fonts","svg","upload");
	$tempRequest = explode("?",substr($_SERVER['REQUEST_URI'], strlen(WEB_PATH)+1,strlen($_SERVER['REQUEST_URI'])));
	if(in_array($console->controller, $dirArray)){
		$fileName = DATA_PATH.$tempRequest[0];
		if(is_file($fileName)){
			$console->HTTPStatusCode("301",HTTP_PATH.$fileName."?".$tempRequest[1]);
		}else{
			if(is_file(APP_PATH."images/nodata.jpg")){
				$console->HTTPStatusCode("301",HTTP_PATH."images/nodata.jpg");
			}
			$console->to404($data);
		}
	}
	if($console->controller == "data"){
		$fileName = APP_PATH.$tempRequest[0];
		if(!is_file($fileName)){
			if(is_file(APP_PATH."images/nodata.jpg")){
				$console->HTTPStatusCode("301",HTTP_PATH."images/nodata.jpg");
			}
			$console->to404($data);
		}
	}


	//該頁資料載入
	$menu = new MTsung\menu($console,PREFIX."menu");

	if($temp = $menu->getData("where alias=? and status=1 and features!='_other_form'",array($console->controller))){
		$tempSort = array();
		foreach ($temp as $keySort => $valueSort) {//把class放到最前面
			if ($valueSort["features"]=="_other_class"){
				array_unshift($tempSort,$valueSort);
			}else{
				$tempSort[] = $valueSort;
			}
		}
		$temp = $tempSort;
		foreach ($temp as $key => $value) {
			if(($console->controller!="index") && ($value["features"]!="_other_form") && ($value["features"]!="_other_calss") && ($value["features"]!="class/product") && !$web_set["titlePrefix"]){
				$web_set["titlePrefix"] = $console->getLabel(trim(explode("-",$value["name"])[0]));
				$breadcru[$breadcruI++] = array(
					"name" => $console->getLabel(trim(explode("-",$value["name"])[0])),
					"url" => "/".$console->controller
				);
			}
		}
		foreach ($temp as $key => $value) {

			//取得搜尋功能的鍵值與使用功能別名
			$search = getSystemKey($value,'search');
			$youtube = getSystemKey($value,'youtube');
			$imageModule = getSystemKey($value,'imageModule');
			$aceEditor = getSystemKey($value,'aceEditor');
			$googleMap = getSystemKey($value,'googleMap');
			$grapesjs = getSystemKey($value,'grapesjs');
			$textarea = getSystemKey($value,'textarea');

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

			$basic = new MTsung\dataList($console,PREFIX.$console->controller,$lang);
			switch ($value["features"]) {
				case '_other_basicOne':

					$data["one"] = $basic->getOne("",array(),$explodeArray);
					break;
				case 'basic/product':
				case '_other_basic':

					//資料
					if($findClassSql){
						if(isset($console->path[2])){
							$key = $console->path[2];
							if(!$data["one"] = $basic->getOne("and (id=? or urlKey=?) ".$findClassSql,array($key,$key),$explodeArray)){
								$console->to404($data);
							}
							$web_set["titlePrefix"] = $data["one"]["name"]."-".$web_set["titlePrefix"];
							$breadcru[$breadcruI++] = array(
								"name" => $data["one"]["name"],
								"url" => "/".$console->controller."/".$console->path[1]."/".$console->path[2]
							);
							if(isset($data["one"]["class"]) && $class){
								foreach ($data["one"]["class"] as $oneKey => $oneValue) {
									$data["one"]["class"][$oneKey] = $console->urlKey($class->getData("where id='".$oneValue."'")[0]);
								}
							}
						}
						if(isset($console->path[1]) && $console->path[1]=="all"){
							$findClassSql = "";
						}
						if($data["list"] = $basic->getListData($statusSql.$findClassSql." order by sort",explode("|__|", $value["dataKey"]),$value["count"])){
							foreach ($data["list"] as $listKey => $listValue) {
								if(isset($data["list"][$listKey]["class"]) && $class){
									$data["list"][$listKey]["class"] = explode("|__|",$listValue["class"]);
									foreach ($data["list"][$listKey]["class"] as $listKey1 => $listValue1) {
										$data["list"][$listKey]["class"][$listKey1] = $console->urlKey($class->getData("where id='".$listValue1."'")[0]);
									}
								}
							}
						}
						$data["page"] = $basic->pageNumber->getHTML1();
					}else{
						if(isset($console->path[1])){
							$key = $console->path[1];
							if(!$data["one"] = $basic->getOne("and (id=? or urlKey=?) ",array($key,$key),$explodeArray)){
								$console->to404($data);
							}
							$web_set["titlePrefix"] = $data["one"]["name"]."-".$web_set["titlePrefix"];
							$breadcru[$breadcruI++] = array(
								"name" => $data["one"]["name"],
								"url" => "/".$console->controller."/".$console->path[1]
							);
						}
						$data["list"] = $basic->getListData($statusSql." order by sort",explode("|__|", $value["dataKey"]),$value["count"]);
						$data["page"] = $basic->pageNumber->getHTML1();
						
						//預設第一筆
						if(!$data["one"]){
						    $data["one"] = $basic->getOne("and id=? ",array($data["list"][0]["id"]),$explodeArray);
						}
					}
					break;
				case 'class/product':
				case '_other_class':
					$class = new MTsung\dataClass($console,PREFIX.$console->controller."_class",$lang);
					$data["class"] = $class->getData("where 1=1 ".$statusSql." order by step",array(),$explodeArray);
					//class
					if(isset($console->path[1])){
						//網址轉換
						if($console->path[1] == "detail" || $console->path[1] == "0"){
							$key = $console->path[2];
							$console->HTTPStatusCode(301,WEB_PATH."/".$console->controller."/".explode("|__|",$basic->getOne("and (id=? or urlKey=?)",array($key,$key))["class"])[0]."/".$key);
							exit;
						}else if($console->path[1] == "all"){
							$data["oneClass"]["id"] = 0;
						}else{
							$key = $console->path[1];
							if(!$data["one"] = $data["oneClass"] = $class->getOne("and (id=? or urlKey=?)",array($key,$key),$explodeArray)){
								$console->to404($data);
							}
						}

					}else{
						$data["oneClass"] = $class->getOne("and id=?",array($data["class"][0]["id"]),$explodeArray);
					}

					if($value["findChildren"] && !$console->path[2]){
						$newUrl = "";
						//下一層有資料時自動進入下一層第一筆
						while($data["oneClass"]["id"] && $class->findChildren($data["oneClass"]["id"])){
							$data["oneClass"] = $class->getOne("and parent=? order by sort limit 1",array($data["oneClass"]["id"]),$explodeArray);
							$newUrl = $data["oneClass"]["urlKey"] ?: $data["oneClass"]["id"];
						}
						if($newUrl){
							$console->HTTPStatusCode("301",HTTP_PATH.$console->path[0]."/".$newUrl);
						}
					}

					$data["one"] = $data["oneClass"] = $console->urlKey($data["oneClass"]);
					if($data["oneClass"]["id"]!=0){
						$web_set["titlePrefix"] = $data["one"]["name"]."-".$web_set["titlePrefix"];

						//麵包屑
						$tempBreadcru = array();
						$parent = $data["one"]["parent"];
						while ($parent) {
							$tempClass = $console->urlKey($class->getOne("and id=?",array($parent)));
							$parent = $tempClass["parent"];
							array_unshift(
								$tempBreadcru,
								array(
									"name" => $tempClass["name"],
									"url" => "/".$console->controller."/".$tempClass["urlKey"]
								)
							);
						}
						foreach ($tempBreadcru as $valueBreadcru) {
							$breadcru[$breadcruI++] = $valueBreadcru;
						}
						$breadcru[$breadcruI++] = array(
							"name" => $data["one"]["name"],
							"url" => "/".$console->controller."/".$console->path[1]
						);
						//麵包屑
						
						$_GET["class"] = $data["oneClass"]["id"];
					}

					//所有子節點一並搜尋
					$classArray[] = $data["oneClass"]["id"];
					if($tempC = $class->findChildren1($data["oneClass"]["id"])){
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

			$classFeatures = array("class/product","_other_class");
			$basicFeatures = array("basic/product","_other_basic","_other_basicOne");

			foreach (array("one","oneClass") as $value__) {
			    if((in_array($value["features"], $classFeatures) && $value__=="oneClass")
			    	|| (in_array($value["features"], $basicFeatures) && $value__=="one")){
			        
    				if($data[$value__]){
    					foreach ($data[$value__] as $keyOne => $valueOne) {
    						//搜尋模組的內容 不做字串陣列轉換
    						if(isset($search[$keyOne]) && $data[$value__][$keyOne]){
    							$basicOne = new MTsung\dataList($console,PREFIX.$search[$keyOne],$lang);
    							foreach ($data[$value__][$keyOne] as $keyOne1 => $valueOne1) {
    								$data[$value__][$keyOne][$keyOne1] = $console->urlKey($basicOne->getOne("and id=?",array($valueOne1)));
    							}
    						}
    						//YT連結轉換
    						if(isset($youtube[$keyOne]) && $data[$value__][$keyOne]){
    							$data[$value__][$keyOne] = $console->youtubeLink($valueOne);
    							$data[$value__][$keyOne."_img"] = $console->youtubeImg($valueOne);
    						}
    						//圖片縮圖網址
    						if(isset($imageModule[$keyOne]) && $data[$value__][$keyOne]){
    							if($data[$value__][$keyOne."__min"] = $valueOne){
    								foreach ($data[$value__][$keyOne."__min"] as $key3 => $value3) {
    									$imgTemp = explode(".",$value3);
    									$typeTemp = array_pop($imgTemp);
    									$data[$value__][$keyOne."__min"][$key3] = implode($imgTemp)."_min.".$typeTemp;
    								}
    							}
    						}
    
    						//非html編輯器跟googlemap htmlspecialchars
    						if(!isset($aceEditor[$keyOne]) && !isset($googleMap[$keyOne])&& !isset($grapesjs[$keyOne]) && $data[$value__][$keyOne] && !is_array($data[$value__][$keyOne])){
    							$data[$value__][$keyOne] = htmlspecialchars($data[$value__][$keyOne]);
    						}

    						//textarea
    						if(isset($textarea[$keyOne]) && $data[$value__][$keyOne]){
    							$data[$value__][$keyOne] = nl2br($data[$value__][$keyOne]);
    						}
    					}
    				}
			    }
			}

			if($data["list"] && in_array($value["features"], $basicFeatures)){
				foreach ($data["list"] as $key1 => $value1) {
					$data["list"][$key1] = array_map(function($v){
						if(!is_array($v)){
							return htmlspecialchars_decode($v);
						}
						return $v;
					},$value1);

					foreach ($data["list"][$key1] as $key2 => $value2) {
						//搜尋模組的內容 不做字串陣列轉換
						if(isset($search[$key2]) && $data["list"][$key1][$key2]){
							$basicOne = new MTsung\dataList($console,PREFIX.$search[$key2],$lang);
							foreach ($data["list"][$key1][$key2] as $keyOne => $valueOne) {
								$data["list"][$key1][$key2][$keyOne] = $console->urlKey($basicOne->getOne("and id=?",array($valueOne)));
							}
						}
						//YT連結轉換
						if(isset($youtube[$key2]) && $data["list"][$key1][$key2]){
							$data["list"][$key1][$key2] = $console->youtubeLink($value2);
							$data["list"][$key1][$key2."_img"] = $console->youtubeImg($value2);
						}
						//圖片縮圖網址
						if(isset($imageModule[$key2]) && is_array($data["list"][$key1][$key2])){
							if($data["list"][$key1][$key2."__min"] = $value2){
								foreach ($data["list"][$key1][$key2."__min"] as $key3 => $value3) {
									$imgTemp = explode(".",$value3);
									$typeTemp = array_pop($imgTemp);
									$data["list"][$key1][$key2."__min"][$key3] = implode($imgTemp)."_min.".$typeTemp;
								}
							}
						}

						//非html編輯器跟googlemap htmlspecialchars
						if(!isset($aceEditor[$key2]) && !isset($googleMap[$key2]) && !isset($grapesjs[$key2]) && $data["list"][$key1][$key2] && !is_array($data["list"][$key1][$key2])){
							$data["list"][$key1][$key2] = htmlspecialchars($data["list"][$key1][$key2]);
						}

						//textarea
						if(isset($textarea[$key2]) && $data["list"][$key1][$key2]){
							$data["list"][$key1][$key2] = nl2br($data["list"][$key1][$key2]);
						}
					}
					
	                if($data["list"][$key1]["dataOption"]){
	    				foreach ($data["list"][$key1]["dataOption"] as $keyOption => $valueOption) {
	    					$data["list"][$key1]["dataOption"][$keyOption] = explode(",", $valueOption);
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
						$upload = new MTsung\Upload($allowMIME,$allowExt,$maxSize,false,UPLOAD_PATH.'form/'.$console->controller);
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
					$form = new MTsung\form($console,PREFIX.$console->controller."_form",$lang);
					if($form->setData($input)){
						$form->sendForm(array(
							"keyName" => $data["one"]["dataName"],
							"keyData" => explode("|__|", $input["keyData"])
						),$_SERVER['HTTP_REFERER'],$data["one"]["recipientEmail"]);
					}else{
						$console->alert($form->message,-1);
					}
				}

                if($data["one"]["dataOption"]){
    				foreach ($data["one"]["dataOption"] as $key => $value) {
    					$data["one"]["dataOption"][$key] = explode(",", $value);
    				}
                }
			}
		}
	}

	//自動生成class list html
	$html_path = WEB_PATH.$lang_url."/".$console->path[0]."/";

	if($data["class"]){
		$data["class"] = $console->urlKey($data["class"]);
		$data["class_li_html"] = getClassHtml($data["class"],$html_path,$data["oneClass"]["id"],$class);
	}
	function getClassHtml($data,$html_path,$oneId,$class){
		foreach ($data as $key => $value) {
			$aClass = '';
			if($oneId == $value["id"] || in_array($oneId,$class->findChildren($value["id"]))){
				$aClass = 'class="current"';
			}
			$classHtml .= '<li><a href="'.$html_path.$value["urlKey"].'" '.$aClass.'>'.$value["name"].'</a>';
			if($value["next"]){
				$classHtml .= '<ul>'.getClassHtml($value["next"],$html_path,$oneId,$class).'</ul>';
			}
			$classHtml .= '</li>';
		}
		return $classHtml;
	}
	
	if($data["list"]){
		$data["list"] = $console->urlKey($data["list"]);

		if($data["oneClass"]){
			$html_path .= $data["oneClass"]["urlKey"]."/";
		}
		
		$data["list_li_html"] = "";
		foreach ($data["list"] as $key => $value) {
			if($data["one"]["id"] == $value["id"]){
				$data["list_li_html"] .= '<li><a href="'.$html_path.$value["urlKey"].'" class="current">'.$value["name"].'</a></li>';
			}else{
				$data["list_li_html"] .= '<li><a href="'.$html_path.$value["urlKey"].'">'.$value["name"].'</a></li>';
			}
			$data["list"][$key]["__href"] = $html_path.$value["urlKey"];
		}
	}


	// print_r($breadcru);exit;
	// print_r($data);exit;