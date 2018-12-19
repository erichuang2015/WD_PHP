<?php 
	include_once("include/header.php");

	include_once(APP_PATH.'class/center.class.php');				//資料處理
	include_once(APP_PATH.'class/tree.class.php');					//分類樹
	include_once(APP_PATH.'class/validation.class.php');			//表單驗證

	include_once(APP_PATH.'class/design.class.php');				//樣板模組
	include_once(APP_PATH.'class/PHPMailer.class.php');				//郵件模組
	include_once(APP_PATH.'class/HTMLtoPDF.class.php');				//輸出PDF模組
	include_once(APP_PATH.'class/member.class.php');				//會員模組
	include_once(APP_PATH.'class/memberGroup.class.php');			//會員群組模組
	include_once(APP_PATH.'class/pageNumber.class.php');			//頁碼模組
	include_once(APP_PATH.'class/uploadFile.class.php');			//上傳模組
	include_once(APP_PATH.'class/webSetting.class.php');			//網站設定
	include_once(APP_PATH.'class/menu.class.php');					//選單
	include_once(APP_PATH.'class/backup.class.php');				//備份
	include_once(APP_PATH.'class/fileTemplate.class.php');			//樣板資料處理
	include_once(APP_PATH.'class/systemLog.class.php');				//操作紀錄
	
	include_once(APP_PATH.'class/dataClass.class.php');				//資料分類
	include_once(APP_PATH.'class/dataList.class.php');				//資料

	include_once(APP_PATH.'class/product.class.php');				//商品
	include_once(APP_PATH.'class/shoppingCart.class.php');			//購物車
	include_once(APP_PATH.'class/form.class.php');					//表單
	include_once(APP_PATH.'class/watermark.class.php');				//浮水印
	include_once(APP_PATH.'class/imgCompress.class.php');			//圖片壓縮
	include_once(APP_PATH.'class/ECPay.class.php');					//綠界
	include_once(APP_PATH.'class/ECPayLog.class.php');				//綠界
	include_once(APP_PATH.'class/csv.class.php');					//csv
	include_once(APP_PATH.'class/analytics.class.php');				//analytics
	include_once(APP_PATH.'class/cPanel.class.php');				//cPanel
	include_once(APP_PATH.'include/main.php');						//核心
	
	$design = new MTsung\design();
	$console = new MTsung\main($conn,$design,$setting);
	$webSetting = new MTsung\webSetting($console,PREFIX."web_setting",$_SESSION[FRAME_NAME]['language'.$console->langSessionName]);//前端輸出用
	$console->setWebSetting($webSetting);
	$console->loadController();
?>