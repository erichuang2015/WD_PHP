<?php
/**
 * 登入
 */
$memberSessionName = 'serback';

$member = new MTsung\member($console,PREFIX.'admin',$memberSessionName);

$member->logout();
$link = isset($_SESSION[FRAME_NAME]["BACK_URI"])?$_SESSION[FRAME_NAME]["BACK_URI"] : 'index';

if($_POST){
	
	//驗證部份(reCAPTCHA沒成功就判斷傳統)
	$Verify = false;
	if(isset($_POST['g-recaptcha-response'])){
		$Verify = $console->checkreCAPTCHA($_POST['g-recaptcha-response']);
	}

	if(!$Verify && isset($_POST['verifycode'])){
		$Verify = $console->checkVerifyCode($_POST['verifycode']);
	}

	if(!$Verify){
		$member->addLog($_POST["account"],MTsung\logType::VERIFCODE_ERROR);
		$console->alert($console->getMessage('VERIFY_ERROR'),'login');
	}



	$temp = $member->login($_POST['account'],$_POST['password']);
	if($temp === true){
		//成功
		$console->linkTo($link);
	}else if($temp === false){
		//失敗 清除記憶
		echo '	
			<script>
	            localStorage.removeItem("serbackAccount");
	            localStorage.removeItem("serbackPassword");
	            // localStorage.removeItem("serbackLang");
	            localStorage.removeItem("serbackRememberMe");
	        </script>
        ';
		$console->alert($member->message,'login');
	}else{
		//未通過email驗證
		//$temp = 會員id
		$console->alert($member->message,'login');
	}
}

/**
 * 社群登入 $_GET["socialLogin"] = (fb,ling,google)
 */
if(isset($_GET["socialLogin"]) && isset($_SESSION[FRAME_NAME][strtoupper($_GET['socialLogin']).'_LOGIN'])){
	$temp = $member->socialLogin($_GET["socialLogin"],$_SESSION[FRAME_NAME][strtoupper($_GET['socialLogin']).'_LOGIN']["id"]);
	unset($_SESSION[FRAME_NAME][strtoupper($_GET['socialLogin']).'_LOGIN']);
	if($temp === true){
		//成功
		$console->linkTo($link);
	}else if($temp === false){
		//失敗
		$console->alert($member->message,'login');
	}else{
		//未通過email驗證
		//$temp = 會員id
		$console->alert($member->message,'login');
	}
}
?>