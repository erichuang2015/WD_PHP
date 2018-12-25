<?php
/*
	Create by Jones
	瀏覽人數計計數器顯示
	MTsung 2018/12/19 cookie防止重複計算 (3600秒)
*/

header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Content-type:image/png");
header("Content-Disposition:filename=math_code.png");

define(COUNT_FILE_NAME, "count.txt");

$mydata = 0;
if (is_file(COUNT_FILE_NAME)){
	$mydata = file_get_contents(COUNT_FILE_NAME);
}

if(!isset($_COOKIE["vCount"])){
	file_put_contents(COUNT_FILE_NAME,++$mydata);
	setcookie("vCount", "1", time()+3600);//3600秒算一次
}


$all_len = '8';									//--字段總長度 (auto則自動)
if ($all_len!='auto'){
	$verification__session = str_pad(($mydata*1), $all_len*1, "0", STR_PAD_LEFT);
}else{
	$verification__session = ($mydata*1).'';
}
$jfont_array = array ('Pixel-lcd-machine.ttf'); //--多自型選擇
$font_size = 30;
$text_angle_minimum = 0;
$text_angle_maximum = 0;
$text_maximum_distance = 28;
$text_minimum_distance = 30;
$ttf_file = "./fonts/".$jfont_array[rand(0,count($jfont_array)-1)]; ///--字體路徑
$text_x_start = ceil($font_size/3);
$image_height_add = 3;


//--寬高度自動計算
$strlen = strlen($verification__session);
$imageHeight = ($font_size)+$image_height_add*2;
$imageWidth = ($strlen*$text_minimum_distance); 	//--圖像寬度
$x = $text_x_start;
$y = $imageHeight-($imageHeight/2)+($font_size/2)-$image_height_add;




$imageHeight += $image_height_add;
//--圖像建立
$im = @imagecreatetruecolor($imageWidth, $imageHeight)
or die("無法建立圖片！");

$bgColor = imagecolorallocate($im, 0,0,0);				//背景RGB色彩
$font_color = imagecolorallocate($im, 255,255,255);		//字體RGB色彩

imagefill($im,0,0,$bgColor);

$a_pix_padding = ceil($text_x_start);
for($i = 0; $i < $strlen; ++$i) {
	@imagettftext($im, $font_size, $text_angle_minimum, $x, $y, $font_color, $ttf_file, $verification__session[$i]);
	
	if ($i>0) {
		$a_end = ($x+$text_minimum_distance)-$text_x_start;
	}else{
		$a_start = $x-$a_pix_padding;
		$a_end = ($x+$text_minimum_distance)-$text_x_start;
	}
	if ($i==$strlen-1) $a_end = $imageWidth-1;
	//邊框
	$borderColor=imagecolorallocate($im,'255','255','255');
	imagerectangle($im,$a_start,0,$a_end,$imageHeight-1,$borderColor);
	
	$a_start = $a_end;
	$x += $text_minimum_distance; //--單字位移
} //for loop


imagepng($im);
imagedestroy($im);

?>
