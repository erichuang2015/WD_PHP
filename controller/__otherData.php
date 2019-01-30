<?php 
	//其他資料
	$menu = new MTsung\menu($console,PREFIX."menu");
	$fileTemplate = new MTsung\fileTemplate($console);
	$temp = $console->conn->getRow($console->conn->Prepare("select * from ".$fileTemplate->table." where name=? and type='web'"),array($console->path[0].".html"));
	$temp["useTables"] = explode("|__|", $temp["useTables"]);
	//全域其他資料
	$temp1 = $console->conn->getRow("select * from ".$fileTemplate->table." where name='top.html' and type='web'");
	if($temp1["useTables"] = explode("|__|", $temp1["useTables"])){
		$temp["useTables"] = array_merge($temp["useTables"],$temp1["useTables"]);
	}
	if($temp["useTables"]){
		foreach ($temp["useTables"] as $key => $value) {
			if(!$value) continue;

			if($features = $menu->getData("where tablesName=?",array($value))){
				$explodeArray = getExplode($features[0]);
			}

			$search = getSystemKey($features[0],'search');
			$youtube = getSystemKey($features[0],'youtube');
			$imageModule = getSystemKey($features[0],'imageModule');

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

						foreach ($data[$value][$key1] as $key2 => $value2) {
							//搜尋模組的內容 不做字串陣列轉換
							if(isset($search[$key2])){
								$basicOne = new MTsung\dataList($console,PREFIX.$search[$key2],$lang);
								foreach ($data[$value][$key1][$key2] as $keyOne => $valueOne) {
									$data[$value][$key1][$key2][$keyOne] = $basicOne->getOne("and id=?",array($valueOne));
								}
							}
							//YT連結轉換
							if(isset($youtube[$key2])){
								$data[$value][$key1][$key2] = $console->youtubeLink($value2);
							}
							//圖片縮圖網址
							if(isset($imageModule[$key2])){
								if($data[$value][$key1][$key2."__min"] = $value2){
									foreach ($data[$value][$key1][$key2."__min"] as $key3 => $value3) {
										$imgTemp = explode(".",$value3);
										$typeTemp = array_pop($imgTemp);
										$data[$value][$key1][$key2."__min"][$key3] = implode($imgTemp)."_min.".$typeTemp;
									}
								}
							}
						}
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