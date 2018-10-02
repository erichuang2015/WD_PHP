<html>
<head>
<?php

/*
* step0 產生apple pay 按鈕
*/

include_once('ECPay.Payment.Applepay.php') ;
$ecpay_applepay = new Ecpay_ApplePay ;

$ecpay_applepay->Applepay_Button['merchantIdentifier'] 	= 'merchant.ECpay.ECC';
$ecpay_applepay->Applepay_Button['lable'] 		= 'ecpay store';
$ecpay_applepay->Applepay_Button['step1_url'] 		= 'verify_vendor.php';
$ecpay_applepay->Applepay_Button['step2_url'] 		= 'payment_gateway.php';
$ecpay_applepay->Applepay_Button['debug_mode'] 		= 'yes';


$ecpay_applepay->Applepay_Button['order_id'] 		= date('ymdhis');		// 廠商訂單編號
$ecpay_applepay->Applepay_Button['amount'] 		= '777'; 		// 訂單總金額

$ecpay_applepay->applepay_button();

?>

</head>
<body>

<div id="divPay">
<button id="btnApplePay" class="apple-pay-button apple-pay-button-white" lang="tw" style="-webkit-appearance: -apple-pay-button; -apple-pay-button-type: buy; width: 400px; height: 64px;"></button>
</div>


</body>

</html>