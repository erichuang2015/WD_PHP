<?php
	$str = "";
	foreach ($_POST as $key => $value) {
		$str .= "[$key] => $value , \r\n";
	}
	// print_r($_POST);
	// print_r($_SERVER);

	$str .= "\r\n\r\n\r\n\r\n\r\n";
	// $str = json_encode($_SERVER);
	$file = fopen("test.txt","a+"); //開啟檔案
	fwrite($file,$str);
	fclose($file);

?>