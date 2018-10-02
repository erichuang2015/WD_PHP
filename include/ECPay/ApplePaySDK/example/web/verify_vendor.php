<?php

/*
* step1 廠商驗證
*/

$validation_url = isset( $_POST['validationURL'] ) ? $_POST['validationURL'] : "https://apple-pay-gateway-cert.apple.com/paymentservices/startSession";


include_once('ECPay.Payment.Applepay.php') ;
$ecpay_applepay = new Ecpay_ApplePay ;

$ecpay_applepay->ServiceURL 				= $validation_url ;

$ecpay_applepay->Verify_Vendor['displayName'] 		= 'merchant.ECpay.ECC';	// apple Merchant ID.
$ecpay_applepay->Verify_Vendor['crt_path'] 		= 'ApplePay.crt.pem'; 	// 憑證檔案絕對路徑
$ecpay_applepay->Verify_Vendor['key_path'] 		= 'ApplePay.key.pem'; 	// 憑證檔案絕對路徑
$ecpay_applepay->Verify_Vendor['key_password'] 		= 'ecpay';

$aMsg_Return = $ecpay_applepay->Verify_Vendor();
echo json_encode($aMsg_Return);
?>