<?php

$switch["buttonBox"] = 1;
$switch["saveButton"] = 1;
$switch["editList"] = 1;


$basicOne = new MTsung\dataList($console,PREFIX."memberField","");

$data["typeOption"] = array(
	"text" => "FORM_TEXT",
	// "textarea" => "FORM_TEXTAREA",
	// "email" => "FORM_EMAIL",
	// "password" => "FORM_PASSWORD",
	"address" => "FORM_ADDRESS",
	"date" => "FORM_DATE",
	// "file" => "FORM_FILE",
	"select" => "FORM_SELECT",
	"radio" => "FORM_RADIO",
	"checkbox" => "FORM_CHECKBOX",
);
$explodeArray[] = "dataName";
$explodeArray[] = "dataKey";
$explodeArray[] = "dataType";
$explodeArray[] = "dataOption";
$explodeArray[] = "dataFa";
$explodeArray[] = "dataRequired";
$explodeArray[] = "dataAuto";

//保留字
$ReservedWord= array("id","name","urlKey","originalPrice","specialPrice","memberPrice","specificationsID","specifications","stock","maxCount","addProduct","addProductSpecifications","addProductMaxCount","addProductMoney","suggestProduct","sort","status","create_date","update_date","create_user","update_user","pictureAlt","pageTitle","pageMeta","next","point","account","password","groupID","fbID","fbName","fbEmail","fbPicture","googleID","googleName","googleEmail","googlePicture","lineID","lineName","lineEmail","linePicture","page","account","password","checkPassword","account","newPassword","checkNewPassword","name","email","address");
if(isset($_POST["dataKey"]) && is_array($_POST["dataKey"])){
	foreach ($_POST["dataKey"] as $key => $value) {
		if(in_array($value,$ReservedWord)){
			$console->alert($console->getMessage("RESERVED_WORD",array($value)),-1);
		}
	}
}

if($_POST){
	if($basicOne->getData("where id=?",array("1"))[0]){
		$_POST["id"] = 1;
	}
	if($basicOne->setData($_POST)){
		$console->alert($basicOne->message,$_SERVER["REQUEST_URI"]);
	}else{
		$console->alert($basicOne->message,-1);
	}
}else{
	$data["one"] = $basicOne->getData("where id=?",array("1"),$explodeArray)[0];
}
