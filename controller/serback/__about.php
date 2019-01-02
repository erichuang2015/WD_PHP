<?php

/**後台開出**/
if($temp = $menu->getData("where url='".$console->path[0]."/".$console->path[1]."' and (features!='' or features IS NOT NULL)")){
	$temp = $temp[0];
	$designName = "__about";
	$temp["dataName"] = explode("|__|", $temp["dataName"]);
	$temp["dataType"] = explode("|__|", $temp["dataType"]);
	$temp["dataKey"] = explode("|__|", $temp["dataKey"]);
	$temp["dataCount"] = explode("|__|", $temp["dataCount"]);
	$temp["dataExtension"] = explode("|__|", $temp["dataExtension"]);
	$temp["dataSearch"] = explode("|__|", $temp["dataSearch"]);

	$imgI = $fileI = $aceI = 0;
	foreach ($temp["dataType"] as $key => $value) {
		switch ($value) {
			case 'aceEditor':
				$module["tinemceEditor"][$aceI]["name"] = $temp["dataKey"][$key];
				$aceI++;
				break;
			case 'imageModule':
				$module["uploadImg"][$imgI]["name"] = $temp["dataKey"][$key];//欄位名稱
				$module["uploadImg"][$imgI]["max"] = $temp["dataCount"][$key];//限制數量
				// $module["uploadImg"][$imgI]["textOther"] = array("Title","Alt","Href");//欄位名稱
				// $module["uploadImg"][$imgI]["textOtherText"] = array($console->getLabel("TITLE"),$console->getLabel("ALT"),$console->getLabel("URL"));//提示字
				// $module["uploadImg"][$imgI]["textareaOther"] = array("Detail");//欄位名稱
				// $module["uploadImg"][$imgI]["textareaOtherText"] = array($console->getLabel("DETAIL"));//提示字
				// $module["uploadImg"][$imgI]["suggestText"] = "1920x576";//建議尺寸
				$imgI++;
				break;
			case 'fileModule':
				$module["uploadFile"][$fileI]["name"] = $temp["dataKey"][$key];//欄位名稱
				$module["uploadFile"][$fileI]["max"] = $temp["dataCount"][$key];//限制數量
				// $module["uploadFile"][$fileI]["suggestText"] = "限制";//建議尺寸
				$module["uploadFile"][$fileI]["extension"] = explode(",",$temp["dataExtension"][$key]);//限制附檔名
				$fileI++;
				break;
			case 'search':
			    if($_POST && isset($_POST[$temp["dataKey"][$key]]) && is_array($_POST[$temp["dataKey"][$key]])){
		        	$_POST[$temp["dataKey"][$key]] = array_unique($_POST[$temp["dataKey"][$key]]);
		        }
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
$explodeArray[] = "dataRequired";
$explodeArray[] = "class";


/**後台開出**/