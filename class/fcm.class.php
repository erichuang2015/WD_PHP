<?php

/**
 * fcm
 */
namespace MTsung{
	class fcm{
		var $console;
		var $couldMessageKey;//Cloud Messaging伺服器金鑰
		
		function __construct($console){
			$this->console = $console;
			$this->couldMessageKey = $console->setting->getValue("couldMessageKey");
		}
		
		/**
		 * 發送訊息
		 * @param  [type] $group 發送群組
		 * @param  string $title 標題
		 * @param  string $body  內容
		 * @param  [type] $icon  icon
		 * @param  string $url   網址
		 * @return [type]        [description]
		 */
		function send($group,$title='測試標題',$body='測試內容',$icon='',$url=''){
			if(!$icon) $icon = $this->console->setting->getValue('icon');
			if(!$url) $url = $this->console->MTweb;
			$data = array(
				"to" => '/topics/'.$group,
				"notification" => array(
					"title" => $title,
					"body" => $body,
					"icon" => $icon,
					"click_action" => $url
				)
			);
			$data = json_encode($data);
			return $this->fcmCurl("https://fcm.googleapis.com/fcm/send",$data);
		}
		
		
		/**
		 * 加入/離開群組
		 * @param  [type] $group     [description]
		 * @param  [type] $userToken [description]
		 * @param  [type] $isJoin	 加入true/離開false
		 * @return [type]            [description]
		 */
		function setTopics($group,$userToken,$isJoin = true){
			$data = array(
				"to" => '/topics/'.$group,
				"registration_tokens" => array($userToken),
			);
			$data = json_encode($data);
			if($isJoin){
				return $this->fcmCurl("https://iid.googleapis.com/iid/v1:batchAdd",$data);
			}else{
				return $this->fcmCurl("https://iid.googleapis.com/iid/v1:batchRemove",$data);
			}
		}
		
		/**
		 * 取得群組資訊
		 * @param  [type] $userToken [description]
		 * @return [type]            [description]
		 */
		function getInfo($userToken){
			return $this->fcmCurl("https://iid.googleapis.com/iid/info/".$userToken."?details=true");
		}
		
		/**
		 * fcmCurl
		 * @param  [type] $url  [description]
		 * @param  string $data [description]
		 * @return [type]       [description]
		 */
		function fcmCurl($url,$data=''){
			$header = array(
	              'Content-Type: application/json',
	              'Authorization: key='.$this->couldMessageKey
		    );

			$ch = curl_init(); 

			curl_setopt_array($ch, array(
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_RETURNTRANSFER => true, // 不直接出現回傳值
				CURLOPT_URL => $url,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_HTTPHEADER => $header,
				CURLOPT_POSTFIELDS => $data
			));

	    	$output = curl_exec($ch);

	    	if($output===false){
	            echo curl_error($ch);
	        }else{
				return json_decode($output,true);
			}
	    	curl_close($ch);
		}
	}
}
?>