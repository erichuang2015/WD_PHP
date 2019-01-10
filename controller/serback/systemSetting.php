<?php
/**
 * 系統設定
 */

/**模組**/
$module["uploadImg"][0]["name"] = "icon";
$module["uploadImg"][0]["max"] = 1;
$module["uploadImg"][1]["name"] = "watermark";
$module["uploadImg"][1]["max"] = 1;
/**模組**/


$data = $console->setting->getValue();

if(!is_dir(APP_PATH.OUTPUT_PATH.'databaseBackup/')){
	mkdir(APP_PATH.OUTPUT_PATH.'databaseBackup/');
}
$dir = dir(APP_PATH.OUTPUT_PATH.'databaseBackup/');
$data["backupFile"] = array();
while($file = $dir->read()) {
   	if (!is_dir($file) && strpos($file,'.sql')){
   		$data["backupFile"][] = array('fileName' => $file, 'filePath' =>  OUTPUT_PATH.'databaseBackup/'.$file);
   	}
}
sort($data["backupFile"]);
$dir->close();

$switch["buttonBox"] = 1;
$switch["saveButton"] = 1;
if($_POST){
	if($memberInfo["groupID"]!=1){
		//允許客戶修改的值
		$a = array("icon","watermark","recipientEmail","senderEmail","senderName","csrfWhitelist","indexPATH","smtpSMTPSecure","smtpHost","smtpPort","smtpUsername","smtpPassword","otherCode");
		foreach ($_POST as $key => $value) {
			if(!in_array($key, $a)){
				unset($_POST[$key]);
			}
		}
	}
	if($console->setting->setData($_POST)){
		$console->alert($console->getMessage('EDIT_OK'),$console->path[0]);
	}else{
		$console->alert($console->getMessage('EDIT_ERROR'),$console->path[0]);
	}
}

?>