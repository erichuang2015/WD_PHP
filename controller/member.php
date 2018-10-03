<?php
	include_once('header.php');
	$web_set["titlePrefix"] = "會員專區";

	define("MEMBER_PATH",WEB_PATH."/".$console->path[0]."/");

	$type = "login";
	if(isset($console->path[1]) && $console->path[1]){
		$type = $console->path[1];
	}

	$noCheck = array('login','forget','join','terms','check');
	if (!$member->isLogin() && !in_array($type,$noCheck)){
		$_SESSION[FRAME_NAME]["MEMBER_BACK_URI"] = $_SERVER["REQUEST_URI"];
		$console->linkTo(MEMBER_PATH."login");
		exit;
	}else if($member->isLogin() && in_array($type,$noCheck)){
		$console->linkTo(MEMBER_PATH."detail");
		exit;
	}
	

	switch ($type) {
		case 'terms':

			$data["one"] = (new MTsung\dataList($console,PREFIX."terms",$lang))->getOne()["detail"];

			break;

		case 'join':

			if(isset($_GET["socialLogin"]) && isset($_SESSION[FRAME_NAME][strtoupper($_GET['socialLogin']).'_LOGIN'])){
				if(!$_POST){
					$console->alert($console->getMessage("JOIN_IN_SOCIAL"),"NO");
				}
				$data["socialData"] = $_SESSION[FRAME_NAME][strtoupper($_GET['socialLogin']).'_LOGIN'];

				if($_POST){
					$socialName = strtolower($_GET['socialLogin']);
					$_POST[$socialName."ID"] = $_SESSION[FRAME_NAME][strtoupper($_GET['socialLogin']).'_LOGIN']["id"];
					$_POST[$socialName."Name"] = $_SESSION[FRAME_NAME][strtoupper($_GET['socialLogin']).'_LOGIN']["name"];
					$_POST[$socialName."Email"] = $_SESSION[FRAME_NAME][strtoupper($_GET['socialLogin']).'_LOGIN']["email"];
					$_POST[$socialName."Picture"] = $_SESSION[FRAME_NAME][strtoupper($_GET['socialLogin']).'_LOGIN']["picture"];
					if (!$_POST["email"]) {
						$_POST["email"] = $_POST["account"];
					}
					unset($_SESSION[FRAME_NAME][strtoupper($_GET['socialLogin']).'_LOGIN']);
					$_POST["emailCheck"] = MTsung\emailCheckType::CHECK_OK;

					if($member->addUser($_POST,false)){
						$member->login($_POST['account'],$_POST['password']);
						$console->alert($member->message,MEMBER_PATH.'detail');
					}else{
						$console->alert($member->message,-1);
					}
				}
			}

			if($_POST){
				if (!$_POST["email"]) {
					$_POST["email"] = $_POST["account"];
				}
				if($member->addUser($_POST,$console->webSetting->getValue("emailCheck"))){
					$temp = $member->login($_POST['account'],$_POST['password']);
					if($temp === true){
						$console->linkTo($link);
					}else{
						$member->checkEmail($temp,MEMBER_PATH."login");
					}
				}else{
					$console->alert($member->message,-1);
				}
			}

			break;

		case 'check':

			if(isset($_GET['uID'])){
				$member->reciveEmail($_GET['uID'],$_GET['auth']);
				$console->alert($member->message,MEMBER_PATH."login");
			}

			break;

		case 'login':

			$link = isset($_SESSION[FRAME_NAME]["MEMBER_BACK_URI"])?$_SESSION[FRAME_NAME]["MEMBER_BACK_URI"] : MEMBER_PATH.'detail';

			if($_POST){

				$temp = $member->login($_POST['account'],$_POST['password']);
				if($temp === true){
					//成功
					unset($_SESSION[FRAME_NAME]["MEMBER_BACK_URI"]);
					$console->linkTo($link);
				}else if($temp === false){
					//失敗
					$console->alert($member->message,MEMBER_PATH.$type);
				}else{
					//未通過email驗證
					//$temp = 會員id
					$console->alert($member->message,MEMBER_PATH.$type);
				}
			}

			/**
			 * 社群登入 $_GET["socialLogin"] = (fb,ling,google)
			 */
			if(isset($_GET["socialLogin"]) && isset($_SESSION[FRAME_NAME][strtoupper($_GET['socialLogin']).'_LOGIN'])){
				$temp = $member->socialLogin($_GET["socialLogin"],$_SESSION[FRAME_NAME][strtoupper($_GET['socialLogin']).'_LOGIN']["id"]);
				if(($temp === false) && ($member->message == $console->getMessage('SOCIAL_LINK_NULL'))){
					$console->linkTo(MEMBER_PATH."join?".explode("?",$_SERVER["REQUEST_URI"])[1]);
				}

				unset($_SESSION[FRAME_NAME][strtoupper($_GET['socialLogin']).'_LOGIN']);
				if($temp === true){
					//成功
					unset($_SESSION[FRAME_NAME]["MEMBER_BACK_URI"]);
					$console->linkTo($link);
				}else if($temp === false){
					//失敗
					$console->alert($member->message,MEMBER_PATH.$type);
				}else{
					//未通過email驗證
					//$temp = 會員id
					$console->alert($member->message,MEMBER_PATH.$type);
				}
			}

			break;

		case 'detail':

			if($_POST){
				$temp = $member->setUser($member->getInfo("id"),$_POST,array('name','phone','address'),array('name','phone','address'));
				if($temp){
					$console->alert($member->message,MEMBER_PATH.$type);
				}else{
					$console->alert($member->message,-1);
				}
			}

			/**
			 * 社群link $_GET["socialLogin"] = (fb,ling,google)
			 */
			if(isset($_GET["socialLogin"]) && isset($_SESSION[FRAME_NAME][strtoupper($_GET['socialLogin']).'_LOGIN'])){
				if($member->link($member->getInfo("id"),$_GET["socialLogin"],$_SESSION[FRAME_NAME][strtoupper($_GET['socialLogin']).'_LOGIN'])){
					unset($_SESSION[FRAME_NAME][strtoupper($_GET['socialLogin']).'_LOGIN']);
				}
				$console->alert($member->message,MEMBER_PATH.$type);
			}

			/**
			 * 社群unlink $_GET["unLink"] = (fb,ling,google)
			 */
			if(isset($_GET["unLink"])){
				$member->unLink($member->getInfo("id"),$_GET["unLink"]);
				$console->alert($member->message,MEMBER_PATH.$type);
			}

			break;

		case 'password':

			if($_POST){
				if(true === $member->login($member->getInfo("account"),$_POST['password'])){
					$temp = $member->setUser($member->getInfo("id"),$_POST,array('checkNewPassword','newPassword'),array('checkNewPassword','newPassword'));
					if($temp){
						$console->alert($member->message,MEMBER_PATH.$type);
					}
				}
				$console->alert($member->message,-1);
			}

			break;

		case 'logout':

			$member->logout();
			$console->linkTo(MEMBER_PATH.'login');

			break;

		case 'forget':

			/**
			 * 傳送email
			 */
			if(isset($_POST["email"])){
				if(!$member->resetPassword('email',$_POST["email"],$console->path[0].'/forget',MEMBER_PATH."login")){
					$console->alert($member->message,-1);
				}
			}

			/**
			 * 接收
			 */
			if(isset($_GET["uID"]) && isset($_GET["auth"])){
				if(!$member->recivePassword($_GET["uID"],$_GET["auth"])){
					$console->alert($member->message,$console->path[0]);
				}else{
					if($_POST){
						$temp = $member->setUser($_GET['uID'],$_POST,array('newPassword','checkNewPassword'),array('newPassword','checkNewPassword'));
						if($temp){
							$console->alert($member->message,MEMBER_PATH.'login');
						}else{
							$console->alert($member->message,-1);
						}
					}
				}
			}

			break;

		case 'order':

			$data["shipmentTitle"] = $order->getShipmentTitle();
			$data["shipmentStatus"] = array(
				MTsung\shipmentStatusType::NO => "<font color='red'>".$console->serbackLabel["SHIPMENT_STATUS_TYPE_NO"]."</font>",
				MTsung\shipmentStatusType::OK => "<font color='blue'>".$console->serbackLabel["SHIPMENT_STATUS_TYPE_OK"]."</font>",
			);
			$data["paymentTitle"] = $order->getPaymentTitle();
			$data["paymentStatus"] = array(
				MTsung\paymentStatusType::NO => "<font color='red'>".$console->serbackLabel["PAYMENT_STATUS_TYPE_NO"]."</font>",
				MTsung\paymentStatusType::OK => "<font color='blue'>".$console->serbackLabel["PAYMENT_STATUS_TYPE_OK"]."</font>",
			);
			if(isset($console->path[2])){
				$data["order"] = $order->getOne(" and memberId='".$member->getInfo("id")."' and step>1 and id=? and status='1'",array($console->path[2]));
				if(!$data["order"]){
					$console->to404();
				}
				$data["order"]["formData"] = json_decode($data["order"]["formData"],true);
				$data["orderList"] = $order->orderListReload($order->getOrderList($data["order"]["orderNumber"]));
	        	$ECPayLog = new MTsung\ECPayLog($console);
	        	$data["orderLog"] = $ECPayLog->getData("where RtnCode='300' and type='shipment' and MerchantTradeNo like '".$data["order"]["orderNumber"]."%' order by create_date desc limit 1")[0];
			}else{
				$data["list"] = $order->getListData(" and memberId='".$member->getInfo("id")."' and step>1 and status='1' order by orderNumber desc",$searchKey);
			}

			break;
			
		case 'payment'://付款
			$data["order"] = $order->getOne(" and memberId='".$member->getInfo("id")."' and step>1 and id=?",array($console->path[2]));
			if($data["order"]["paymentMethod"] == MTsung\paymentMethodType::CONVENIENCE_STORE_PICK_UP_PAYMENT_ECPAY
				|| $data["order"]["paymentMethod"] == MTsung\paymentMethodType::CASH_ON_DELIVERY
				){
				$console->alert($console->getMessage("PAYMENT_IS_NOT_PAY"),-1);
			}
			if(!$order->payment($data["order"]["orderNumber"])){
				$console->alert($order->message,-1);
			}else if($data["order"]["paymentMethod"] == MTsung\paymentMethodType::PHYSICAL_ATM_TRANSFER){
				$console->alert($console->getMessage("PHYSICAL_ATM_TRANSFER_MAIL_TEXT"),-1);
			}
			break;

		case 'shipment'://
			break;

		default:
			$console->to404();
			break;
	}

	$designName = $console->path[0]."_".$type;

?>
