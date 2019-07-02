<?php

$switch["buttonBox"] = 1;
$data["listUrl"] = $web_set['serback_url'].'/'.$console->path[0];

$theme = new MTsung\theme($console->conn);

if(isset($console->path[1])){
	switch ($console->path[1]) {
		case 'install':
			if($_POST["id"]){
				//驗證部份(reCAPTCHA沒成功就判斷傳統)
				$Verify = false;
				if(isset($_POST['g-recaptcha-response'])){
					$Verify = $console->checkreCAPTCHA($_POST['g-recaptcha-response']);
				}

				if(!$Verify && isset($_POST['verifycode'])){
					$Verify = $console->checkVerifyCode($_POST['verifycode']);
				}

				if(!$Verify){
					$console->alert($console->getMessage('VERIFY_ERROR'),-1);
				}
				
				if($theme->install($_POST["id"])){
					$console->alert($console->getMessage("_OK"),$data["listUrl"]);
				}
			}
			$console->alert($console->getMessage("NOT_AUTHORITY"),-1);
			break;
		case 'edit':
			if($_POST["id"]){
				$theme->editTheme($_POST["id"],$_POST["name"],$_POST["picture"],$_POST["memo"]);
				$console->alert($console->getMessage("_OK"),$_SERVER["REQUEST_URI"]);
			}
			$console->alert($console->getMessage("NOT_AUTHORITY"),-1);
			break;
		case 'add':
			//新增
			if($_POST["name"]){
				$theme->export($_POST["name"],$_POST["picture"],$_POST["memo"]);
				$console->alert($console->getMessage("_OK"),$data["listUrl"]);
			}else{
				$console->alert($console->getMessage("INPUT_NULL"),-1);
			}
			$console->alert($console->getMessage("NOT_AUTHORITY"),-1);
			break;
		default:
			//網址參數錯誤
			$console->alert($console->getMessage("NOT_AUTHORITY"),$data["listUrl"]);
			break;
	}
}else{

	$data["list"] = $theme->getListTheme();
	$data["pageNumber"] = $pageNumber = new MTsung\pageNumber($console,$data["list"],20);
    $temp = array();
    $end = $pageNumber->getDataStart()+$pageNumber->getPer();
    $end = ($end>$pageNumber->getDataCount())?$pageNumber->getDataCount():$end;
    for ($i = $pageNumber->getDataStart(); $i < $end ; $i++) { 
    	$temp[] = $data["list"][$i];
    }
    $data["list"] = $temp;
}