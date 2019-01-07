<?php
/**
 * 檔案
 */
$allowUser = array("vipadmin");
$disallowArray = array("css","js","fonts","upload","output","images","module",".htaccess","robots.txt","404.html");
if(!in_array($console->path[1],$disallowArray) && !in_array($member->getInfo("account"),$allowUser)){
	$console->alert($console->getMessage("NOT_AUTHORITY"),-1);
	exit;
}

$switch["buttonBox"] = 1;

$module["aceEditor"]["name"] = '_aceEditor';//POST欄位名稱，不可使用"aceEditor"
if($console->path[1]=="css"){
	$module["aceEditor"]["type"] = "css";
}else if($console->path[1]=="js"){
	$module["aceEditor"]["type"] = "javascript";
}

$dir = $console->path[1];
foreach ($console->path as $key => $value) {
	if($key>1){
		if($key < count($console->path)){
			$dir .= '/';
		}
		$dir .= $value;
	}
}
$data["listUrl"] = $web_set['serback_url']."/".$console->path[0]."/".$dir;

while(strpos($dir,'../')!==false || strpos($dir,'..\\')!==false){
	$dir = str_replace('../',"",$dir);
	$dir = str_replace('..\\',"",$dir);
}

$dirPath = iconv("BIG5", "UTF-8",APP_PATH.$dir);


if($_FILES){
	$disallowArray = array("php","htaccess");
	foreach ($_FILES as $key => $value) {
		foreach ($value["name"] as $key1 => $value1) {
			$MIME = explode('.', $value1);
			$MIME = end($MIME);
			if(in_array($MIME, $disallowArray)){
				continue;
			}

			if(!preg_match("/[^a-zA-Z0-9_\-~@. ]/",$value1)){
				move_uploaded_file($value["tmp_name"][$key1],$dirPath."/".$value1);
			}else{
                $destination = $dirPath."/".str_replace('.',"",microtime(true)).".".$MIME;
                $i=0;//避免無窮迴圈
                while(is_file($destination) && ($i++)<10000){
                	$destination = $dirPath."/".str_replace('.',"",microtime(true)).".".$MIME;
                }
				move_uploaded_file($value["tmp_name"][$key1],$destination);
			}
		}
	}
	exit;
}

//刪除
if($_POST && $console->path[count($console->path)-1]=="delete" && isset($_POST["checkElement"])){
	$data["listUrl"] = str_replace('/delete',"/",$data["listUrl"]);
	$dirPath = str_replace('/delete',"/",$dirPath);
	foreach ($_POST["checkElement"] as $key => $value) {
		if(is_dir($dirPath.$value)){
			delTree($dirPath.$value);
		}else{
			if($value!=".htaccess"){
				unlink($dirPath.$value);
			}
		}
	}
	$console->linkTo($data["listUrl"]);
}

function delTree($dir) {
    $files = array_diff(scandir($dir), array('.','..'));
	foreach ($files as $file) {
	    (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
	}
	return rmdir($dir);
}

if(!is_dir($dirPath)){//編輯
	if(!is_file($dirPath)){
		$console->alert($console->getMessage("NOT_AUTHORITY"),-1);
	}

	$extension = explode('.', $dirPath);
	$extension = end($extension);

	if(function_exists("mime_content_type")){
    	$MIME = explode("/",mime_content_type($dirPath))[0];
    }else{
		$MIME = $extension;
    }

	if($extension == "php"){
		$module["aceEditor"]["type"] = "php";
	}

    switch ($MIME) {
    	case 'text':
    	case 'txt':
    	case 'html':
    	case 'js':
    	case 'css':
    	case 'htaccess':
    		if(in_array(".htaccess",explode("/",$dirPath)) && ($console->path[1]!=".htaccess") && !in_array($member->getInfo("account"),$allowUser)){
				$console->alert($console->getMessage("MIME_ERROR"),-1);
    		}
    		if($_POST){
            	file_put_contents($dirPath, $_POST[$module["aceEditor"]["name"]]);
				$console->alert($console->getMessage("EDIT_OK"),$_SERVER["REQUEST_URI"]);
    		}
			$data[$module["aceEditor"]["name"]] = htmlspecialchars(file_get_contents($dirPath));
    		break;
    	default:
			$console->alert($console->getMessage("MIME_ERROR"),-1);
    		break;
    }

	$data["listUrl"] .= "../../";
	$switch["editList"] = 1;
	$switch["saveButton"] = 1;
	$switch["backButton"] = isset($console->path[2]);
}else{//資料夾
    $data["list"] = array();

    $dh = opendir($dirPath);
    while ($filename = readdir($dh)){
    	if($filename!="."){
    		if(!isset($console->path[2]) && $filename==".."){
    			continue;
    		}
    		$allPath = iconv("BIG5", "UTF-8", $dirPath."/".$filename);//完整路徑名稱
    		$temp = array();
    		$temp["name"] = iconv("BIG5", "UTF-8", $filename);
    		if($temp["isDir"] = (int)is_dir($allPath)){
    			$temp["name"] .= "/";
    			$temp["size"] = $console->formatSize($console->getDirSize($allPath."/"));
    		}else{
    			$temp["size"] = $console->formatSize(filesize($allPath));
    		}
    		$data["list"][] = $temp;
    	}
    }
	usort($data["list"], function($a,$b){
	    if($a['isDir'] == $b['isDir']) return ($a['name'] > $b['name']) ? 1 : -1;
	    return ($a['isDir'] < $b['isDir']) ? 1 : -1;
	});
    closedir ($dh);

    $data["dirPath"] = $dir;
	$switch["deleteButton"] = 1;
	$switch["listList"] = 1;
}



?>