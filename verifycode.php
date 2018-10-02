<?php

/**
 * 驗證碼 20180615 MTsung整理
 */
include_once("include/header.php");
header("Content-type:image/png");



// 產生隨機驗證碼
$str = 'ACEFGHJKMNSTUWX2345678';
$verification__session = '';
for($i=0; $i<4; $i++){
   $verification__session.= strtoupper($str[rand(0,strlen($str)-1)]);
}

// 限制組數
while(isset($_SESSION['__VERIFYCODE_LIST']) && count($_SESSION['__VERIFYCODE_LIST'])>10){
	array_shift($_SESSION['__VERIFYCODE_LIST']);
}
$_SESSION['__VERIFYCODE_LIST'][] = $verification__session;


// 圖片大小
$imageWidth = 160;
$imageHeight = 40;

$im = @imagecreatetruecolor($imageWidth, $imageHeight) or die("無法建立圖片！");

// 背景
$bgColor = imagecolorallocate($im, rand(250,255), rand(250,255), rand(250,255));
imagefill($im,0,0,$bgColor);

// 畫線
// for($i=0; $i<rand(1,5); $i++){
//    imageline($im,rand(0,$imageWidth),rand(0,$imageHeight),rand($imageHeight,$imageWidth),rand(0,$imageHeight),imagecolorallocate($im, rand(180,222), rand(180,222), rand(180,222)));
// }


//add by peter 參數

//--edit by Jones 多字形選擇
$jfont_array = array ('mywanderingheart.ttf','SentyCHALKoriginal.ttf');


// 文字大小
$font_size = 25;

// 旋轉角度
$text_angle_minimum = -8;
$text_angle_maximum = 8;

// x變量
$text_maximum_distance = 30;
$text_minimum_distance = 40;

// 文字開始的X軸位置
$x = 15;

// y範圍
$y_min = ($imageHeight / 2) + ($font_size / 2) - 5;
$y_max = ($imageHeight / 2) + ($font_size / 2);

$strlen = strlen($verification__session);
for ($i = 0; $i < $strlen; $i++) {
	// 隨機字型
	$ttf_file = "./fonts/".$jfont_array[rand(0,count($jfont_array)-1)];
	// 隨機色彩
	$font_color = imagecolorallocate($im, rand(70,120), rand(70,120), rand(70,120));
	// 隨機旋轉
	$angle = rand($text_angle_minimum, $text_angle_maximum);

	$y = rand($y_min, $y_max);

	@imagettftext($im, $font_size, $angle, $x, $y, $font_color, $ttf_file, $verification__session[$i]);

	$x += rand($text_minimum_distance, $text_maximum_distance);
}


// 邊框
$borderColor=imagecolorallocate($im, 129, 186, 213);
imagerectangle($im,0,0,$imageWidth-2,$imageHeight-1,$borderColor);


imagepng($im);
imagedestroy($im);

?>
