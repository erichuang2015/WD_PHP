<?php 
	//其他資料
	$menu = new MTsung\menu($console,PREFIX."menu");
	$fileTemplate = new MTsung\fileTemplate($console);
	$temp = $console->conn->getRow($console->conn->Prepare("select * from ".$fileTemplate->table." where name=? and type='web'"),array($console->controller.".html"));
	$temp["useTables"] = explode("|__|", $temp["useTables"]);
	//全域其他資料
	$temp1 = $console->conn->getRow("select * from ".$fileTemplate->table." where name='top.html' and type='web'");
	if($temp1["useTables"] = explode("|__|", $temp1["useTables"])){
		$temp["useTables"] = array_merge($temp["useTables"],$temp1["useTables"]);
	}
	if($temp["useTables"]){
		foreach ($temp["useTables"] as $key => $value) {
			if(!$value) continue;
			$isClass = substr($value,-6) == "_class"; //是否為分類

			if($features = $menu->getData("where tablesName=?",array($value))){
				$features = $features[0];
				$explodeArray = getExplode($features);
				$search = getSystemKey($features,'search');
				$youtube = getSystemKey($features,'youtube');
				$imageModule = getSystemKey($features,'imageModule');
				$aceEditor = getSystemKey($features,'aceEditor');
				$googleMap = getSystemKey($features,'googleMap');
				$textarea = getSystemKey($features,'textarea');
			}


			if($console->isTables($value)){
				$$value = new MTsung\dataList($console,PREFIX.$value,$lang);
				if($isClass){
					//分類全取出
					$data[$value] = $$value->getData("where 1=1 ".$statusSql." order by step desc",array(),$explodeArray);
				}else{
					//limit 50 取全部會佔用太多記憶體
					$data[$value] = $$value->getData("where 1=1 ".$statusSql." order by sort limit 50",array(),$explodeArray);
				}
				 
				if($data[$value]){
					foreach ($data[$value] as $key1 => $value1) {
						$data[$value][$key1] = array_map(function($v){
							if(!is_array($v)){
								return htmlspecialchars_decode($v);
							}
							return $v;
						},$value1);

						foreach ($data[$value][$key1] as $key2 => $value2) {
							//搜尋模組的內容 不做字串陣列轉換
							if(isset($search[$key2]) && $data[$value][$key1][$key2]){
								$basicOne = new MTsung\dataList($console,PREFIX.$search[$key2],$lang);
								foreach ($data[$value][$key1][$key2] as $keyOne => $valueOne) {
									$data[$value][$key1][$key2][$keyOne] = $console->urlKey($basicOne->getOne("and id=?",array($valueOne)));
								}
							}
							//YT連結轉換
							if(isset($youtube[$key2]) && $data[$value][$key1][$key2]){
								$data[$value][$key1][$key2] = $console->youtubeLink($value2);
								$data[$value][$key1][$key2."_img"] = $console->youtubeImg($value2);
							}
							//圖片縮圖網址
							if(isset($imageModule[$key2]) && $data[$value][$key1][$key2]){
								if($data[$value][$key1][$key2."__min"] = $value2){
									foreach ($data[$value][$key1][$key2."__min"] as $key3 => $value3) {
										$imgTemp = explode(".",$value3);
										$typeTemp = array_pop($imgTemp);
										$data[$value][$key1][$key2."__min"][$key3] = implode($imgTemp)."_min.".$typeTemp;
									}
								}
							}

							//非html編輯器跟googlemap htmlspecialchars
							if(!isset($aceEditor[$key2]) && !isset($googleMap[$key2]) && $data[$value][$key1][$key2] && !is_array($data[$value][$key1][$key2])){
								$data[$value][$key1][$key2] = htmlspecialchars($data[$value][$key1][$key2]);
							}

							//textarea
							if(isset($textarea[$key2]) && $data[$value][$key1][$key2]){
								$data[$value][$key1][$key2] = nl2br($data[$value][$key1][$key2]);
							}
						}
						
		                if($data[$value][$key1]["dataOption"]){
		    				foreach ($data[$value][$key1]["dataOption"] as $keyOption => $valueOption) {
		    					$data[$value][$key1]["dataOption"][$keyOption] = explode(",", $valueOption);
		    				}
		                }
					}
				}
				$data[$value] = $console->urlKey($data[$value]);
				if($isClass){//是分類時
					$idToKey = [];//id對應的key
					// print_r($data[$value]);exit;
					foreach ($data[$value] as $keyClass => $valueClass) {
						$data[$value][$keyClass]["next"] = [];
						$idToKey[$valueClass["id"]] = $keyClass;
					}
					foreach ($data[$value] as $keyClass => $valueClass) {
						if($valueClass["parent"]){
							array_unshift($data[$value][$idToKey[$valueClass["parent"]]]["next"],$data[$value][$keyClass]);
							unset($data[$value][$keyClass]);
						}
					}
					$data[$value] = array_reverse($data[$value]);
				}
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
						"dataFa",
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
			$explodeArray[] = "dataFa";
			$explodeArray[] = "dataRequired";
		}
		return $explodeArray;
	}
	/**
	 * 取得使用value功能的鍵值
	 */
	function getSystemKey($temp,$value){
		$searchTemp = array_combine(explode("|__|",$temp["dataKey"]), explode("|__|",$temp["dataType"]));
		$searchTemp1 = array_combine(explode("|__|",$temp["dataKey"]), explode("|__|",$temp["dataSearch"]));
		if($searchKey = array_keys($searchTemp,$value)){
			foreach ($searchKey as $keyS => $valueS) {
				$search[$valueS] = $searchTemp1[$valueS]?$searchTemp1[$valueS]:"";
			}
		}
		return $search;
	}