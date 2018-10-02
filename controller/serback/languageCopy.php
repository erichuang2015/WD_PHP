<?php
/**
 * 語系複製
 */
$switch["buttonBox"] = 1;
$switch["saveButton"] = 1;


$tables = $this->conn->GetArray("SHOW TABLES");
foreach ($tables as $key => $value) {
	if(isset(explode("__",$value[0])[1]) && explode("__",$value[0])[1]==str_replace("-","_",$settingLang)){
		$tables[$key]["key"] = explode("__",$value[0])[0];
		$tables[$key]["name"] = $value[0];
	}else{
		unset($tables[$key]);
	}
}
$data["tables"] = array_values($tables);
if($_POST && isset($_POST["checkElement"])){
	$msg = "";
	foreach ($_POST["checkElement"] as $key => $value) {
		$temp = new MTsung\center($console,$value,$settingLang);
		$temp->copyLang($_POST["lang"]);
		$msg .= $value."：".$temp->message."\\n";
	}
	$console->alert($msg,$_SERVER["REQUEST_URI"]);
}

?>