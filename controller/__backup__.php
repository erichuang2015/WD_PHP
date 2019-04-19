<?php
	if($console->setting->getValue("backupSwitch")==0){
		exit;
	}
	ignore_user_abort(true);
	set_time_limit(0);

	//備份
	$fileName = APP_PATH.DATA_PATH."backup.txt";
	$file = fopen($fileName, "r") or die("Unable to open file!");
	$day = fread($file,filesize($fileName));
	fclose($file);
	if(!is_numeric($day)){
		$file = fopen($fileName, "w") or die("Unable to open file!");
		fwrite($file,strtotime(DATE));
		fclose($file);
		exit;
	}

	//多少天備份一次
	if($console->setting->getValue("backupDay")<0){
		exit;
	}

	if((strtotime(DATE)-$day) > 60*60*24*$console->setting->getValue("backupDay")){
		$file = fopen($fileName, "w") or die("Unable to open file!");
		$backup = new MTsung\backup($console->conn);
		$backup->exportDatabase();
		fwrite($file,strtotime(DATE));
		fclose($file);
	}

	//多少天以上的刪除
	if($console->setting->getValue("backupDeleteDay")>0){
		$dir = dir(APP_PATH.OUTPUT_PATH.'databaseBackup/');
		while($file = $dir->read()) {
		   	if (!is_dir($file)){
		   		$temp = explode("_",explode(".",$file)[0]);
				if((strtotime(DATE)-strtotime($temp[0]."-".$temp[1]."-".$temp[2]." ".$temp[3].":".$temp[4].":".$temp[5])) > 60*60*24*$console->setting->getValue("backupDeleteDay")){
					unlink(APP_PATH.OUTPUT_PATH.'databaseBackup/'.$file);
				}
		   	}
		}
		$dir->close();
	}
	exit;
?>