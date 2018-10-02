<?php
    // 產生B2C測標資料
    require('ECPay.Logistics.Integration.php');
    try {
        $AL = new ECPayLogistics();
        $AL->HashKey = '5294y06JbISpM5x9';
        $AL->HashIV = 'v77hoKGq4kWxNNIS';
        $AL->Send = array(
            'MerchantID' => '2000132',
            'ClientReplyURL' => '',
            'LogisticsSubType' => LogisticsSubType::FAMILY,
            'PlatformID' => ''
        );

        $html = $AL->CreateTestData('產生測標資料');
        echo $html;
    } catch(Exception $e) {
        echo $e->getMessage();
    }
?>