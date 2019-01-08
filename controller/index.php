
<?php 
	include_once('header.php');

	$menu = new MTsung\menu($console,PREFIX."menu");

	if($temp = $menu->getData("where alias=?",array($console->path[0]))){
		foreach ($temp as $key => $value) {
			if(!$web_set["titlePrefix"] || ($web_set["titlePrefix"] && $value["features"]!="_other_calss")){
				$web_set["titlePrefix"] = $console->getLabel($value["name"]);
			}

			$explodeArray = getExplode($value);

			switch ($value["features"]) {
				case '_other_basicOne':
					$temp = new MTsung\dataList($console,PREFIX.$console->path[0],$lang);
					$data["one"] = $temp->getOne("",array(),$explodeArray);
					break;
				case '_other_basic':
					break;
				case '_other_calss':
					# code...
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
	print_r($data);
	exit;

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
							$explodeArray[] = $value1;
						}
					}

					if($textareaOther = explode(",",$temp["dataTextareaOther"][$key])){
						foreach ($textareaOther as $value1) {
							$explodeArray[] = $value1;
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
	