<?php


/**
 * 金流
 * MTsung by 20190221
 */
namespace MTsung{

	class pay{
		var $message;

		function __construct(){
		}

		function formSubmit($url,$data,$medthod="POST"){
			$temp = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><form id="postForm" action="'.$url.'" method="'.$medthod.'">';
			if($data && is_array($data)){
				foreach ($data as $key => $value){
					$temp .= '<input type="hidden" name="'.$key.'" value="'.$value.'">';
				}
			}
			$temp .= '</form><script>document.forms.postForm.submit();</script>';	
			echo $temp;
			exit;
		}

		/**
		 * curl
		 * @param  string $type    [description]
		 * @param  [type] $data    [description]
		 * @param  [type] $options [description]
		 * @param  [type] $header  [description]
		 * @return [type]          [description]
		 */
		function curl($url,$type='GET',$data=array(),$options=array(),$header=array()) {
			$ch = curl_init();

			if(strtoupper($type) == "GET"){
				$url = $url."?".http_build_query($data);
			}else{
				$options[CURLOPT_POST] = true;
				$options[CURLOPT_POSTFIELDS] = http_build_query($data);
			}

			$defaultOptions = array(
				CURLOPT_RETURNTRANSFER => true, // 不直接出現回傳值
				CURLOPT_URL => $url,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_HTTPHEADER => $header
			);
			$options = $options + $defaultOptions;
			curl_setopt_array($ch, $options);

			$response = curl_exec($ch);

			if (!$response) {
				$this->message = curl_error($ch);
				return false;
			}

			return $response;
		}
	}
}