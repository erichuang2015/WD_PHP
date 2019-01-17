<?php
ini_set("memory_limit",-1);

if($_POST) $console->alert("error",-1);

$module["aceEditor"]["name"] = '_aceEditor';//POST欄位名稱，不可使用"aceEditor"
// file_put_contents(ini_get("error_log"),"");
$data[$module["aceEditor"]["name"]] = "";
if(is_file(ini_get("error_log"))){
	$data[$module["aceEditor"]["name"]] = htmlspecialchars(file_get_contents(ini_get("error_log")));
}