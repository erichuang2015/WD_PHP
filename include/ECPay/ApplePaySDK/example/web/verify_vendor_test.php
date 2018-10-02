<?php
/*
* 測試憑證是否正確與apple連線
*/
date_default_timezone_set("Asia/Taipei");
include_once('ECPay.Payment.Applepay.php') ;
$ecpay_applepay = new Ecpay_ApplePay ;

$ecpay_applepay->ServiceURL 				= 'https://apple-pay-gateway-cert.apple.com/paymentservices/startSession' ; 	// 串傳送位置

$ecpay_applepay->Verify_Vendor['displayName'] 		= 'ECPayECC';
$ecpay_applepay->Verify_Vendor['crt_path'] 		= 'ApplePay.crt.pem';	// 憑證路徑
$ecpay_applepay->Verify_Vendor['key_path'] 		= 'ApplePay.key.pem';	// 憑證路徑
$ecpay_applepay->Verify_Vendor['key_password'] 		= 'ecpay';		// 憑證密碼

$aMsg_Return = $ecpay_applepay->Verify_Vendor_Test();

echo $aMsg_Return;

?>