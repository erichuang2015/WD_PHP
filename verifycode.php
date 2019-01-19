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
$bgColor = imagecolorallocate($im, rand(180,255), rand(180,255), rand(180,255));
imagefill($im,0,0,$bgColor);

// 畫線
for($i=0; $i<rand(5,8); $i++){
   imagelinethick($im,rand(0,$imageWidth),rand(0,$imageHeight),rand($imageHeight,$imageWidth),rand(0,$imageHeight),imagecolorallocate($im, rand(160,222), rand(160,222), rand(160,222)),2);
}

function imagelinethick($image, $x1, $y1, $x2, $y2, $color, $thick = 1)
{
    if ($thick == 1) {
        return imageline($image, $x1, $y1, $x2, $y2, $color);
    }
    $t = $thick / 2 - 0.5;
    if ($x1 == $x2 || $y1 == $y2) {
        return imagefilledrectangle($image, round(min($x1, $x2) - $t), round(min($y1, $y2) - $t), round(max($x1, $x2) + $t), round(max($y1, $y2) + $t), $color);
    }
    $k = ($y2 - $y1) / ($x2 - $x1); //y = kx + q
    $a = $t / sqrt(1 + pow($k, 2));
    $points = array(
        round($x1 - (1+$k)*$a), round($y1 + (1-$k)*$a),
        round($x1 - (1-$k)*$a), round($y1 - (1+$k)*$a),
        round($x2 + (1+$k)*$a), round($y2 - (1-$k)*$a),
        round($x2 + (1-$k)*$a), round($y2 + (1+$k)*$a),
    );
    imagefilledpolygon($image, $points, 4, $color);
    return imagepolygon($image, $points, 4, $color);
}

//add by peter 參數

//--edit by Jones 多字形選擇
$jfont_array = array ("SentyWEN2017.ttf",'SentyCHALKoriginal.ttf');


// 文字大小
$font_size = 30;

// 旋轉角度
$text_angle_minimum = -20;
$text_angle_maximum = 20;

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


/**干擾曲線 (左右)**/
$A = mt_rand(1, $imageHeight/2);                  // 振幅
$b = mt_rand(-$imageHeight/4, $imageHeight/4);   // Y軸方向偏移量
$f = mt_rand(-$imageHeight/4, $imageHeight/4);   // X軸方向偏移量
$T = mt_rand($imageHeight*1.5, $imageWidth*2);  // 周期
$w = (2* M_PI)/$T;

$px1 = 0;  // 曲線橫坐標起始位置
$px2 = mt_rand($imageWidth/2, $imageWidth * 0.667);  // 曲線橫坐標結束位置
for ($px=$px1; $px<=$px2; $px=$px+ 0.9) {
    if ($w!=0) {
        $py = $A * sin($w*$px + $f)+ $b + $imageHeight/2;  // y = Asin(ωx+φ) + b
        $i = (int) (($font_size - 6)/8);
        while ($i > 0) {
            imagesetpixel($im, $px + $i, $py + $i, imagecolorallocate($im, rand(70,120), rand(70,120), rand(70,120)));
            $i--;
        }
    }
}

$A = mt_rand(1, $imageHeight/2);                  // 振幅
$f = mt_rand(-$imageHeight/4, $imageHeight/4);   // X軸方向偏移量
$T = mt_rand($imageHeight*1.5, $imageWidth*2);  // 周期
$w = (2* M_PI)/$T;
$b = $py - $A * sin($w*$px + $f) - $imageHeight/2;
$px1 = $px2;
$px2 = $imageWidth;
for ($px=$px1; $px<=$px2; $px=$px+ 0.9) {
    if ($w!=0) {
        $py = $A * sin($w*$px + $f)+ $b + $imageHeight/2;  // y = Asin(ωx+φ) + b
        $i = (int) (($font_size - 8)/8);
        while ($i > 0) {
            imagesetpixel($im, $px + $i, $py + $i, imagecolorallocate($im, rand(70,120), rand(70,120), rand(70,120)));     
            $i--;
        }
    }
}

// 邊框
$borderColor=imagecolorallocate($im, 129, 186, 213);
imagerectangle($im,0,0,$imageWidth-2,$imageHeight-1,$borderColor);


imagepng($im);
imagedestroy($im);

?>
