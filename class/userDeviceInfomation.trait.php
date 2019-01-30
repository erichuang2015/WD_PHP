<?php


/**
 * 使用者裝置資訊function 
 * MTsung by 20180720
 */
namespace MTsung{

	trait userDeviceInfomation{


		/**
		 * 取得作業系統
		 * @return [type] [description]
		 */
		function getSystem(){
		    $sys = $_SERVER['HTTP_USER_AGENT'];

		    if(stripos($sys, "NT 10.0")){
		        $os = "Windows 10";
		    }else if(stripos($sys, "NT 6.3")){
		        $os = "Windows 8.1";
		    }else if(stripos($sys, "NT 6.2")){
		        $os = "Windows 8";
		    }else if(stripos($sys, "NT 6.1")){
		        $os = "Windows 7";
		    }elseif(stripos($sys, "NT 6.0")){
		        $os = "Windows Vista";
		    }elseif(stripos($sys, "NT 5.1")){
		        $os = "Windows XP";
		    }elseif(stripos($sys, "NT 5.2")){
		        $os = "Windows Server 2003";
		    }elseif(stripos($sys, "NT 5")){
		        $os = "Windows 2000";
		    }elseif(stripos($sys, "NT 4.9")){
		        $os = "Windows ME";
		    }elseif(stripos($sys, "NT 4")){
		        $os = "Windows NT 4.0";
		    }elseif(stripos($sys, "Mac") && $this->getDevice()=="Desktop"){
		        $os = "Mac";
		    }elseif(stripos($sys, "Unix")){
		        $os = "Unix";
		    }elseif(stripos($sys, "FreeBSD")){
		        $os = "FreeBSD";
		    }elseif(stripos($sys, "SunOS")){
		        $os = "SunOS";
		    }elseif(stripos($sys, "BeOS")){
		        $os = "BeOS";
		    }elseif(stripos($sys, "OS/2")){
		        $os = "OS/2";
		    }elseif(stripos($sys, "PC")){
		        $os = "Macintosh";
		    }elseif(stripos($sys, "AIX")){
		        $os = "AIX";
		    }elseif(stripos($sys, "Android")){
		        $os = "Android";
		    }elseif(stripos($sys, "iPhone")){
		        $os = "iOS";
		    }elseif(stripos($sys, "98")){
		        $os = "Windows 98";
		    }elseif(stripos($sys, "95")){
		        $os = "Windows 95";
		    }else{
		        $os = "Other";
		    }
		    return $os;
		}

		/**
		 * 取得裝置
		 * @return [type]     Tablet=平板，Mobile=手機，Desktop=電腦
		 */
		function getDevice(){
		    $ua = $_SERVER['HTTP_USER_AGENT'];
		    $iphone = strstr(strtolower($ua), 'mobile'); //Search for 'mobile' in user-agent (iPhone have that)
		    $android = strstr(strtolower($ua), 'android'); //Search for 'android' in user-agent
		    $windowsPhone = strstr(strtolower($ua), 'phone'); //Search for 'phone' in user-agent (Windows Phone uses that)
		 
		    $androidTablet = false;
		    if(strstr(strtolower($ua), 'android') ){//Search for android in user-agent
	            if(!strstr(strtolower($ua), 'mobile')){ //If there is no ''mobile' in user-agent (Android have that on their phones, but not tablets)
	                $androidTablet = ture;
	            }
	        }
		    $ipad = strstr(strtolower($ua), 'ipad'); //Search for iPad in user-agent
		 
		    if($androidTablet || $ipad){ //If it's a tablet (iPad / Android)
		        return 'Tablet';
		    }
		    elseif($iphone && !$ipad || $android && !$androidTablet || $windowsPhone){ //If it's a phone and NOT a tablet
		        return 'Mobile';
		    }
		    else{ //If it's not a mobile device
		        return 'Desktop';
		    }
		}


		/**
		 * 取得使用者IP
		 * @return [type] [description]
		 */
		public function getIP(){
			if (!empty($_SERVER['HTTP_CLIENT_IP'])){
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			}else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)){
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}else{
				$ip = $_SERVER['REMOTE_ADDR'];
			}

			return $ip;
		}

		/**
		 * 取得來源網域
		 * @return [type] [description]
		 */
		function getReferer(){
		    $temp = explode("/",str_replace(array("https://","http://"),"",$_SERVER["HTTP_REFERER"]))[0];

		    if($temp == $_SERVER["HTTP_HOST"]){
		        $referer = "";
		    }else if(stripos($temp, "google")!==false){
		        $referer = "google";
		    }else if(stripos($temp, "yahoo")!==false){
		        $referer = "yahoo";
		    }else if(stripos($temp, "bing")!==false){
		        $referer = "bing";
		    }else if(stripos($temp, "facebook")!==false){
		        $referer = "facebook";
		    }else if(stripos($temp, "instagram")!==false){
		        $referer = "instagram";
		    }else if(stripos($temp, "instagram")!==false){
		        $referer = "instagram";
		    }else if(stripos($temp, "pchome")!==false){
		        $referer = "pchome";
		    }else if(stripos($temp, "wikipedia")!==false){
		        $referer = "wikipedia";
		    }else if(stripos($temp, "twitter")!==false){
		        $referer = "twitter";
		    }else if($temp == ""){
		        $referer = "";
		    }else{
		    	error_log("來源網域:".$temp."\n");
		        $referer = "other";
		    }
		    return $referer;
		}
	}
}
