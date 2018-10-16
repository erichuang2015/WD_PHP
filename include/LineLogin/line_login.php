<?php
    include("../header.php");

	$data["response_type"] = 'code';
	$data["client_id"] = $setting->getValue("lineAuthClientID");
	$data["redirect_uri"] = HTTP_PATH"include/LineLogin/line_callback.php";
	$data["scope"] = 'profile openid email';
	$data["state"] = $_SESSION[FRAME_NAME]['LINE_TOKEN'] = urlencode(hash_hmac('md5',rand(),'token'));
	header("Location: https://access.line.me/oauth2/v2.1/authorize?".http_build_query($data));
	exit;
?>