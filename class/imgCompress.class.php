<?php

/**
 * 圖片壓縮
 * MTsung 修改背景透明 by 20180905
 * MTsung 產生縮圖 by 20190130
 * 
 * $source =  'Penguins.jpg';  
 * $dst_img = '123';  
 * $percent = 1;  #原圖壓縮，不縮放，但體積大大降低  
 * $image = (new imgCompress($source,$percent))->compressImg($dst_img);
 */

/** 
 * 圖片壓縮類：通過縮放來壓縮。 
* 如果要保持源圖比例，把參數$percent保持為1即可。 
* 即使原比例壓縮，也可大幅度縮小。數碼相機4M圖片。也可以縮為700KB左右。如果縮小比例，則體積會更小。 
 *   
 * 結果：可保存、可直接顯示。 
 */  
namespace MTsung{

	class imgCompress{  
	  
		private $src;  
		private $image;  
		private $imageinfo;  
		private $percent = 0.5;  
	  
		/** 
		 * 圖片壓縮 
		 * @param $src 源圖 
		 * @param float $percent  壓縮比例 
		 */  
		public function __construct($src, $percent=1)  
		{  
			$this->src = $src;  
			$this->percent = $percent;  
		}  
	  
	  	/**
	  	 * 產生縮圖
	  	 * by MTsung
	  	 * @return [type] [description]
	  	 */
	  	function thumbnail($imageMinSize=500,$suffix='min'){
			$temp = explode(".",$this->src);
			$type = strtolower(end($temp));
			if($type=='jpg') $type = 'jpeg';
	  		$minFileName = str_replace(".".pathinfo($this->src, PATHINFO_EXTENSION),"_".$suffix.".".pathinfo($this->src, PATHINFO_EXTENSION),$this->src);
            eval("\$img = imagecreatefrom".$type."(\$this->src);");
            $imgX = imagesx($img);
            $imgY = imagesy($img);
            
            if(($imgX>$imageMinSize) || ($imgY>$imageMinSize)){
                if($imgX > $imgY){
                  $newX = $imageMinSize;
                  $newY = intval($imgY / $imgX * $imageMinSize);
                }else{
                  $newY = $imageMinSize;
                  $newX = intval($imgX / $imgY * $imageMinSize);
                }
                $output = imagecreatetruecolor($newX, $newY);
                imagesavealpha($output, true);
                imageinterlace($output, 1);
                imagefill($output, 0, 0, imagecolorallocatealpha($output, 0, 0, 0, 127));
                imagecopyresampled($output, $img, 0, 0, 0, 0, $newX, $newY, $imgX, $imgY);
                eval("image".$type."(\$output,\$minFileName);");
            }else{
                copy($this->src,$minFileName);
            }
	  	}
	  
		/** 高清壓縮圖片 
		 * @param string $saveName  提供圖片名（可不帶擴展名，用源圖擴展名）用於保存。或不提供文檔名直接顯示 
		 */  
		public function compressImg($saveName='')  
		{  
			$this->_openImage();  
			if(!empty($saveName)) $this->_saveImage($saveName);  //保存  
			else $this->_showImage();  
		}  
	  
		/** 
		 * 內部：打開圖片 
		 */  
		private function _openImage()  
		{  
			list($width, $height, $type, $attr) = getimagesize($this->src);  
			$this->imageinfo = array(  
				'width'=>$width,  
				'height'=>$height,  
				'type'=>image_type_to_extension($type,false),  
				'attr'=>$attr  
			);  
			$fun = "imagecreatefrom".$this->imageinfo['type'];  
			$this->image = $fun($this->src);  
			imagesavealpha($this->image, true);
			imageinterlace($this->image, 1);
			$this->_thumpImage();  
		}  
		/** 
		 * 內部：操作圖片 
		 */  
		private function _thumpImage()  
		{  
			$new_width = $this->imageinfo['width'] * $this->percent;  
			$new_height = $this->imageinfo['height'] * $this->percent;  
			$image_thump = imagecreatetruecolor($new_width,$new_height);

			imagesavealpha($image_thump, true);
			imageinterlace($image_thump, 1);
			$color = imagecolorallocatealpha($image_thump, 0, 0, 0, 127);
			imagefill($image_thump, 0, 0, $color);

			//將原圖複製帶圖片載體上面，並且按照一定比例壓縮,極大的保持了清晰度  
			imagecopyresampled($image_thump,$this->image,0,0,0,0,$new_width,$new_height,$this->imageinfo['width'],$this->imageinfo['height']);  
			imagedestroy($this->image);  
			$this->image = $image_thump;  
		}  
		/** 
		 * 輸出圖片:保存圖片則用saveImage() 
		 */  
		private function _showImage()  
		{  
			header('Content-Type: image/'.$this->imageinfo['type']);  
			$funcs = "image".$this->imageinfo['type'];  
			$funcs($this->image);  
		}  
		/** 
		 * 保存圖片到硬盤： 
		 * @param  string $dstImgName  1、可指定字符串不帶後綴的名稱，使用源圖擴展名 。2、直接指定目標圖片名帶擴展名。 
		 */  
		private function _saveImage($dstImgName)  
		{  
			if(empty($dstImgName)) return false;  
			$allowImgs = ['.jpg', '.jpeg', '.png', '.bmp', '.wbmp','.gif'];   //如果目標圖片名有後綴就用目標圖片擴展名 後綴，如果沒有，則用源圖的擴展名  
			$dstExt =  strrchr($dstImgName ,".");  
			$sourseExt = strrchr($this->src ,".");  
			if(!empty($dstExt)) $dstExt =strtolower($dstExt);  
			if(!empty($sourseExt)) $sourseExt =strtolower($sourseExt);  
	  
			//有指定目標名擴展名  
			if(!empty($dstExt) && in_array($dstExt,$allowImgs)){  
				$dstName = $dstImgName;  
			}elseif(!empty($sourseExt) && in_array($sourseExt,$allowImgs)){  
				$dstName = $dstImgName.$sourseExt;  
			}else{  
				$dstName = $dstImgName.$this->imageinfo['type'];
			}  
			$funcs = "image".$this->imageinfo['type'];
			imagesavealpha($this->image, true);
			imageinterlace($this->image, 1);
			$funcs($this->image,$dstName);
			imagedestroy($this->image);
		}  
	
	  	/**
	  	 * tinyPNG壓縮
	  	 * @param [type] $input [description]
	  	 */
	  	function TinyPNG($input,$key = "GCJkLWVet5vrqS9pUWsKTsbiQZAyG5T2"){
			$request = curl_init();
			curl_setopt_array($request, array(
				CURLOPT_URL => "https://api.tinypng.com/shrink",
				CURLOPT_USERPWD => "api:" . $key,
				CURLOPT_POSTFIELDS => file_get_contents($input),
				CURLOPT_BINARYTRANSFER => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HEADER => true,
				CURLOPT_SSL_VERIFYPEER => false
			));
			$response = curl_exec($request);
			if (curl_getinfo($request, CURLINFO_HTTP_CODE) === 201) {
				$headers = substr($response, 0, curl_getinfo($request, CURLINFO_HEADER_SIZE));
				foreach (explode("\r\n", $headers) as $header) {
					if (substr($header, 0, 10) === "Location: ") {
						$request = curl_init();
						curl_setopt_array($request, array(
							CURLOPT_URL => substr($header, 10),
							CURLOPT_RETURNTRANSFER => true,
							CURLOPT_SSL_VERIFYPEER => false
						));
						file_put_contents($input, curl_exec($request));
					}
				}
			}else{
				//失敗用php的壓
				$this->compressImg($input);
				// print(curl_error($request));
				// print("Compression failed");
			}
		}
	} 
}
