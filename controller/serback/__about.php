<?php
if($console->path[0]=="member"){
	$tempField = new MTsung\dataList($console,PREFIX."memberField","");
	$temp = $tempField->getData();
}else if($console->path[0]=="order"){
	$tempField = new MTsung\dataList($console,PREFIX."orderField","");
	$temp = $tempField->getData();
}else{
	if($temp = $menu->getData("where url='".$console->path[0]."/".$console->path[1]."' and (features!='' or features IS NOT NULL)")){
		$designName = "__about";
	}
}
/**後台開出**/
if($temp){
	$temp = $temp[0];
	foreach (array(
					"dataName",
					"dataType",
					"dataKey",
					"dataCount",
					"dataFa",
					"dataOption",
					"dataExtension",
					"dataSearch",
					"dataSuggestText",
					"dataTextOther",
					"dataTextOtherText",
					"dataTextareaOther",
					"dataTextareaOtherText",
					"dataSearchCount",
				) as $key => $value) {
		if(isset($temp[$value])){
			$temp[$value] = explode("|__|", $temp[$value]);
		}
	}
	
	if(is_array($temp["dataOption"])){
		foreach ($temp["dataOption"] as $key => $value) {
			$temp["dataOption"][$key] = explode(",", $value);
		}
	}

	$imgI = $fileI = $aceI = $grapesjsI = 0;
	foreach ($temp["dataType"] as $key => $value) {
		switch ($value) {
			case 'grapesjs':
				$module["grapesjs"][$grapesjsI]["name"] = $temp["dataKey"][$key];
				$grapesjsI++;
				break;
			case 'aceEditor':
				$module["tinemceEditor"][$aceI]["name"] = $temp["dataKey"][$key];
				$aceI++;
				break;
			case 'imageModule':
				$module["uploadImg"][$imgI]["name"] = $temp["dataKey"][$key];//欄位名稱
				$module["uploadImg"][$imgI]["max"] = $temp["dataCount"][$key];//限制數量

				if($temp["dataTextOther"][$key]){
					$module["uploadImg"][$imgI]["textOther"] = explode(",",$temp["dataTextOther"][$key]);//欄位名稱
					$module["uploadImg"][$imgI]["textOtherText"] = array_map("getLabel_",explode(",",$temp["dataTextOtherText"][$key]));//提示字
				}
				
				if($temp["dataTextareaOther"][$key]){
					$module["uploadImg"][$imgI]["textareaOther"] = explode(",",$temp["dataTextareaOther"][$key]);//欄位名稱
					$module["uploadImg"][$imgI]["textareaOtherText"] = array_map("getLabel_",explode(",",$temp["dataTextareaOtherText"][$key]));//提示字
				}

				$module["uploadImg"][$imgI]["suggestText"] = $console->getLabel($temp["dataSuggestText"][$key]);//提示文字
				$imgI++;
				break;
			case 'fileModule':
				$module["uploadFile"][$fileI]["name"] = $temp["dataKey"][$key];//欄位名稱
				$module["uploadFile"][$fileI]["max"] = $temp["dataCount"][$key];//限制數量

				if($temp["dataTextOther"][$key]){
					$module["uploadFile"][$fileI]["textOther"] = explode(",",$temp["dataTextOther"][$key]);//欄位名稱
					$module["uploadFile"][$fileI]["textOtherText"] = array_map("getLabel_",explode(",",$temp["dataTextOtherText"][$key]));//提示字
				}

				if($temp["dataTextareaOther"][$key]){
					$module["uploadFile"][$fileI]["textareaOther"] = explode(",",$temp["dataTextareaOther"][$key]);//欄位名稱
					$module["uploadFile"][$fileI]["textareaOtherText"] = array_map("getLabel_",explode(",",$temp["dataTextareaOtherText"][$key]));//提示字
				}

				$module["uploadFile"][$fileI]["suggestText"] = $console->getLabel($temp["dataSuggestText"][$key]);//提示文字
				$module["uploadFile"][$fileI]["extension"] = explode(",",$temp["dataExtension"][$key]);//限制附檔名
				$fileI++;
				break;
			case 'search':
			    if($_POST && isset($_POST[$temp["dataKey"][$key]]) && is_array($_POST[$temp["dataKey"][$key]])){
		        	$_POST[$temp["dataKey"][$key]] = array_unique($_POST[$temp["dataKey"][$key]]);
		        }
				$explodeArray[] = $temp["dataKey"][$key];
				break;
			case 'address':
				$explodeArray[] = $temp["dataKey"][$key];
				break;
		}
	}
	$data["system"] = $temp;
	$data["addMaxFloor"] = (int)$data["system"]["addMaxFloor"];
}

$data["typeOption"] = array(
	"text" => "FORM_TEXT",
	"textarea" => "FORM_TEXTAREA",
	"email" => "FORM_EMAIL",
	"password" => "FORM_PASSWORD",
	"address" => "FORM_ADDRESS",
	"date" => "FORM_DATE",
	"file" => "FORM_FILE",
	"select" => "FORM_SELECT",
	"radio" => "FORM_RADIO",
	"checkbox" => "FORM_CHECKBOX",
);
$explodeArray[] = "dataName";
$explodeArray[] = "dataType";
$explodeArray[] = "dataOption";
$explodeArray[] = "dataFa";
$explodeArray[] = "dataRequired";
$explodeArray[] = "class";
$explodeArray[] = "specificationsID";
$explodeArray[] = "specifications";
$explodeArray[] = "stock";
$explodeArray[] = "addProduct";
$explodeArray[] = "addProductSpecifications";
$explodeArray[] = "maxCount";
$explodeArray[] = "addProductMaxCount";
$explodeArray[] = "addProductMoney";
$searchKey[] = "name";

function getLabel_($v){
	global $console;
	return $console->getLabel($v);
}
/**後台開出**/