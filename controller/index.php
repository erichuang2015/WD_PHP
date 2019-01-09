
<?php 
	include_once('header.php');

	//404檔案路徑轉換
	$dirArray = array("css","js","images","fonts","svg");
	if(in_array($console->path[0], $dirArray)){
		$fileName = DATA_PATH.substr($_SERVER['REQUEST_URI'], strlen(WEB_PATH)+1,strlen($_SERVER['REQUEST_URI']));
		if(is_file($fileName)){
			$console->HTTPStatusCode("301",HTTP_PATH.$fileName);
		}else{
			$console->to404();
		}
		exit;
	}

	//該頁資料載入
	$menu = new MTsung\menu($console,PREFIX."menu");

	if($temp = $menu->getData("where alias=?",array($console->path[0]))){
		usort($temp,function($a,$b){//把class放到最前面
			if ($a["features"]!="_other_calss") return 1;
			return 0;
		});
		foreach ($temp as $key => $value) {
			if(!$web_set["titlePrefix"] || ($web_set["titlePrefix"] && $value["features"]!="_other_calss")){
				$web_set["titlePrefix"] = $console->getLabel($value["name"]);
			}

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
				case '_other_basic':

					//資料
					if(isset($console->path[2])){
						$key = $console->path[2];
						if(!$data["one"] = $basic->getOne("and (id=? or urlKey=?) ".$findClassSql,array($key,$key),$explodeArray)){
							$console->to404();
						}
						if($console->path[0] == "product"){
							$data["one"]["price"] = $product->getPrice($data["one"]["id"]);
							// $data["one"]["specificationsID"] = explode("|__|",$data["one"]["specificationsID"])[0];
						}
					}else{
						$data["list"] = $basic->getListData("and status='1' ".$findClassSql." order by sort",array(),$value["count"]);
						$data["page"] = $basic->pageNumber->getHTML1();
					}
					break;
				case '_other_class':
					$class = new MTsung\dataClass($console,PREFIX.$console->path[0]."_class",$lang);
					$data["class"] = $class->getData("where status='1' order by step",array(),$explodeArray);
					//class
					if(isset($console->path[1])){
						//網址轉換
						if($console->path[1] == "detail"){
							$key = $console->path[2];
							$console->linkTo(explode("|__|",WEB_PATH."/".$console->path[0]."/".$basic->getOne("and (id=? or urlKey=?)",array($key,$key))["class"])[0]."/".$console->path[2]);
							exit;
						}

						$key = $console->path[1];
						if(!$data["one"] = $data["oneClass"] = $class->getOne("and (id=? or urlKey=?)",array($key,$key),$explodeArray)){
							$console->to404();
						}
					}else{
						$data["oneClass"] = $data["class"][0];
					}
					$data["oneClass"] = $console->urlKey($data["oneClass"]);
					$_GET["class"] = $data["oneClass"]["id"];

					$findClassSql = $basic->findArrayString("class",$data["oneClass"]["id"]);

					break;
				default:
					# code...
					break;
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
						$upload = new MTsung\Upload(array(),array(),1048576,false,UPLOAD_PATH.'form/'.$console->path[0]);
						$upload->callUploadFile();
						$temp = $upload->getDestination();
						if(count($temp)!=count($_FILES)){
							$console->alert("ERROR",-1);
						}
						$i = 0;
						foreach ($_FILES as $key => $value) {
							$_POST[$key] = $temp[$i++];
							$_FILES[$key]["tmp_name"] = APP_PATH.$_POST[$key];
						}
						ksort($_POST);

					}

					$input["keyData"] = implode("|__|",$_POST);
					$form = new MTsung\form($console,PREFIX.$console->path[0]."_form",$lang);
					if($form->setData($input)){
						$form->sendForm(array(
							"keyName" => explode("|__|", $data["one"]["dataName"]),
							"keyData" => explode("|__|", $input["keyData"])
						),WEB_PATH."/".$console->path[0]);
					}else{
						$console->alert($form->message,-1);
					}
				}

				$data["one"]["dataName"] = explode("|__|", $data["one"]["dataName"]);
				$data["one"]["dataType"] = explode("|__|", $data["one"]["dataType"]);
				$data["one"]["dataOption"] = explode("|__|", $data["one"]["dataOption"]);
				foreach ($data["one"]["dataOption"] as $key => $value) {
					$data["one"]["dataOption"][$key] = explode(",", $value);
				}
				$data["one"]["dataRequired"] = explode("|__|", $data["one"]["dataRequired"]);
			}
		}
	}

	//其他資料
	$fileTemplate = new MTsung\fileTemplate($console);
	$temp = $console->conn->getRow($console->conn->Prepare("select * from ".$fileTemplate->table." where name=? and type='web'"),array($console->path[0].".html"));
	if($temp["useTables"] = explode("|__|", $temp["useTables"])){
		foreach ($temp["useTables"] as $key => $value) {
			if(!$value) continue;

			if($features = $menu->getData("where tablesName=?",array($value))){
				$explodeArray = getExplode($features[0]);
			}

			if($console->isTables($value)){
				$$value = new MTsung\dataList($console,PREFIX.$value,$lang);
				if($data[$value] = $$value->getData("where status=1 order by sort",array(),$explodeArray)){
					foreach ($data[$value] as $key1 => $value1) {
						$data[$value][$key1] = array_map(function($v){
							if(!is_array($v)){
								return htmlspecialchars_decode($v);
							}
							return $v;
						},$value1);
					}
				}
				$data[$value] = $console->urlKey($data[$value]);
			}
		}
	}

	/**
	 * 取得需要轉為陣列的字串key
	 * @param  [type] $temp [description]
	 * @return [type]       [description]
	 */
	function getExplode($temp){
		$explodeArray = array();

		foreach (array(
						"dataName",
						"dataType",
						"dataKey",
						"dataCount",
						"dataExtension",
						"dataSearch",
						"dataSuggestText",
						"dataTextOther",
						"dataTextOtherText",
						"dataTextareaOther",
						"dataTextareaOtherText",
						"dataSearchCount",
					) as $key => $value) {
				$temp[$value] = explode("|__|", $temp[$value]);
		}

		foreach ($temp["dataType"] as $key => $value) {
			switch ($value) {
				case 'imageModule':
				case 'fileModule':
				case 'search':
					$explodeArray[] = $temp["dataKey"][$key];

					if($textOther = explode(",",$temp["dataTextOther"][$key])){
						foreach ($textOther as $value1) {
							$explodeArray[] = $temp["dataKey"][$key].$value1;
						}
					}

					if($textareaOther = explode(",",$temp["dataTextareaOther"][$key])){
						foreach ($textareaOther as $value1) {
							$explodeArray[] = $temp["dataKey"][$key].$value1;
						}
					}
			}
		}

		if($temp["formData"]){
			$explodeArray[] = "dataName";
			$explodeArray[] = "dataType";
			$explodeArray[] = "dataOption";
			$explodeArray[] = "dataRequired";
		}
		return $explodeArray;
	}
	

	// print_r($data);