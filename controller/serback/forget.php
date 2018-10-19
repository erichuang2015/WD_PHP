<?php
/**
 * 忘記密碼
 */
$memberSessionName = 'serback';

$member = new MTsung\member($console,PREFIX.'admin',$memberSessionName);

$member->logout();

/**
 * 傳送email
 */
if(isset($_POST["email"])){
	if(!$member->resetPassword('email',$_POST["email"],'serback/forget')){
		$console->alert($member->message,'forget');
	}
}

/**
 * 接收
 */
if(isset($_GET["uID"]) && isset($_GET["auth"])){
	if(!$member->recivePassword($_GET["uID"],$_GET["auth"])){
		$console->alert($member->message,'forget');
	}else{
		if($_POST){
			$temp = $member->setUser($_GET['uID'],$_POST,array('newPassword','checkNewPassword'),array('newPassword','checkNewPassword'));
			if($temp){
				$console->alert($member->message,'login');
			}else{
				$console->alert($member->message,-1);
			}
		}
	}
}

?>