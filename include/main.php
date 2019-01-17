<?php

/** 
 * MTsung by 20180510
 */
namespace MTsung{

	class main{
		use userDeviceInfomation;
		var $conn;
		var $design;
		var $setting;
		var $webSetting;

		var $message;
		var $serbackLabel;

		var $path;
		var $controller;
		var $switch;
		var $config;
		var $languageArray = array();
		var $langSessionName = '';

		/**
		 * 建構式
		 * @param ADO 		$conn 
		 * @param design 	$design  
		 */
		function __construct($conn,$design,$setting){

			//網址處理
			$url = "";
			if($_SERVER['REQUEST_URI']!=$_SERVER['SCRIPT_NAME']){
				$url = $_SERVER['REQUEST_URI'];
			}
			$url = str_replace('?'.$_SERVER['QUERY_STRING'], '', $url);
			$url = substr($url, strlen(WEB_PATH)+1,strlen($url)); ///AAA/BBB

			if (empty($url)){
				$this->path[0] = $setting->getValue("indexPATH")?$setting->getValue("indexPATH"):INDEX_PATH;
			}else{
				$this->path = explode('/', urldecode($url));
			}
			foreach ($this->path as $key => $value) {
				if($value==""){
					unset($this->path[$key]);
				}
			}
			$this->controller = $this->path[0];

			//語言Session前後台不同
			if(($this->controller=='serback') || (isset($this->path[1])&&($this->path[1]=='serback')) ){
				$this->langSessionName='Serback';
			}

			$this->conn = $conn;
			$this->setting = $setting;
			$this->design = $design;
			$this->design->setConsole($this);

			
			$this->setLanguageArray();//語言檔案



			/**
			 * 語言
			 * path優先並存cookie
			 * 沒path先看cookie有沒有，沒有去找瀏覽器語系，從權重高開始找，如果都沒有就預設
			 */
			if(array_key_exists($this->controller, $this->languageArray)){
				$temp = $this->controller;
				unset($this->path[0]);
				$this->path = array_values($this->path);
				if(isset($this->path[0]) && $this->path[0]){
					$this->controller = $this->path[0];
				}else{
					$this->controller = $setting->getValue("indexPATH")?$setting->getValue("indexPATH"):INDEX_PATH;
				}
			 	setcookie("lang".$this->langSessionName, $temp, time()+157680000, '/');
			}else if(isset($_COOKIE["lang".$this->langSessionName])){
			 	$temp = $_COOKIE["lang".$this->langSessionName];
			}else{
				if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
					$temp = explode(",", strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']));//瀏覽器語系
					foreach ($temp as $key => $value) {
						$temp[$key] = explode(";", $value)[0];
						if(array_key_exists($temp[$key], $this->languageArray)){
							$temp = $value;
							break;
						}
					}
				}else{
					$temp = LANG;
				}
			}

			if(!is_array($temp) && array_key_exists($temp, $this->languageArray)){
				$this->setLanguage($temp);
			}else{
				setcookie("lang".$this->langSessionName , '' , time()-157680000, '/');
				$this->setLanguage();
			}
			/*switch*/

			//在哪邊不開
			$XSSArray = array('serback');
			$CSRFArray = array();

			$csrfWhitelist = explode("\n",$this->setting->getValue("csrfWhitelist"));//網域 IP 白名單
			$csrfWhitelist = array_map(function($v){
				return str_replace("\r","",$v);
			}, $csrfWhitelist);

			$this->switch["XSSVerifty"] = false && !in_array($this->controller, $XSSArray);//輸出會過濾就不需要了
			$this->switch["CSRFVerifty"] = (
				!in_array($this->controller, $CSRFArray) && 
				!in_array($this->getIP(),$csrfWhitelist) && 
				!($this->path[0] == "ECPayResponse" && $this->path[1] == "map") &&
				!($this->path[0] == "shopping" && $this->path[1] == "aio") &&
				!($this->path[0] == "shopping" && $this->path[1] == "shipment")
			);

			/*switch*/
			
			/*config*/

			$this->config["CSRFKey"] = 'MTsung';		//token金鑰
			$this->config["CSRFType"] = 'md5';			//token加密方式
			$this->config["CSRFTime"] = 604800;			//token存活時間(秒)
			$this->config["CSRFTokenMax"] = 50;			//token最大組數(個)
			$this->config["POSTTime"] = 0.5;			//連續POST最小時間

			/*config*/


			$this->POSTVerifty();						//POSTtime
			$this->XSSVerifty();						//XSS處理
			$this->rmBadToken();						//刪除不合法的token
			$this->CSRFVerifty();						//CSRF處理

			$this->loadLanguageini();					//載入語言ini

			/**
			 * debug
			 */
			// print_r("<script>console.log(".json_encode($_SESSION[FRAME_NAME]).");");
			// print_r("console.log(".json_encode($_COOKIE).");</script>");
			
			// echo '<!--';
			// print_r($_SESSION);
			// print_r($_COOKIE);
			// print_r($_SERVER);
			// echo '-->';

		}

		/**
		 * read ini檔
		 * @param  [type] $value 語言代碼
		 * @return [type]        [description]
		 */
		function readLanguageini($value){
			$fileName = LANGUAGE_PATH.$value.'.ini';
			if(is_file($fileName) && isset($this->languageArray[$value])){
				$file = fopen($fileName, "r") or die("Unable to open file!");
				if(filesize($fileName) > 0){
					$langStr = fread($file,filesize($fileName));
				}else{
					$langStr = '';
				}
				fclose($file);
				return $langStr;
			}else{
				return false;
			}
		}

		/**
		 * write ini檔
		 * @param  [type] $value 語言代碼
		 * @param  [type] $data  內容
		 */
		function writeLanguageini($value,$data){
			$fileName = LANGUAGE_PATH.$value.'.ini';
			
			$file = fopen($fileName, "w") or die("Unable to open file!");
			fwrite($file,$data);
			fclose($file);
		}

		/**
		 * 重新命名
		 * @param  [type] $oldFileName 舊語言代碼
		 * @param  [type] $newFileName 新語言代碼
		 * @return [type]              [description]
		 */
		function reNameLanguageini($oldValue,$newValue){
			if($oldValue && $newValue){
				if($oldValue==LANG){
					$this->alert($this->getMessage('DEFAULT_LANGUAGE_NO_RENAME'),-1);
					exit;
				}
				$oldFileName = LANGUAGE_PATH.$oldValue.'.ini';
				if(!is_file($oldFileName)){
					$this->alert($this->getMessage('LANGUAGE_NULL'),-1);
					exit;
				}else{
					$newFileName = LANGUAGE_PATH.$newValue.'.ini';
					if(is_file($newFileName)){
						$this->alert($this->getMessage('LANGUAGE_REPEAT'),-1);
						exit;
					}else{
						//資料表改名
						$allTable = $this->conn->Execute("SHOW TABLE STATUS;");
						if($allTable){
							foreach ($allTable as $key => $value) {
								$langKey = explode("__",$value["Name"]);
								if(isset($langKey[1]) && ($langKey[1]==$oldValue)){
									$this->conn->Execute("ALTER TABLE ".$value["Name"]." RENAME TO ".$langKey[0]."__".$newValue.";");
								}
							}
						}
						//語言檔改名
						rename($oldFileName,$newFileName);
						return true;
					}
				}
			}else{
				$this->alert($this->getMessage('INPUT_NULL'),-1);
			}
		}

		/**
		 * 刪除語言檔
		 * @param  string $value 語言代碼
		 * @return [type]        [description]
		 */
		function deleteLanguageini($value=''){
			//預設語言不能刪除
			if($value==LANG){
				$this->alert($this->getMessage('DEFAULT_LANGUAGE_NO_DELETE'),-1);
				exit;
			}

			$fileName = LANGUAGE_PATH.$value.'.ini';
			$value = str_replace("-","_",$value);
			if(!is_file($fileName)){
				return false;
			}else{
				//資料表刪除
				$allTable = $this->conn->Execute("SHOW TABLE STATUS;");
				if($allTable){
					foreach ($allTable as $key => $v) {
						$langKey = explode("__",$v["Name"]);
						if(isset($langKey[1]) && ($langKey[1]==$value)){
							$this->conn->Execute("DROP TABLE ".$v["Name"].";");
						}
					}
				}
				//語言檔刪除
				unlink($fileName);
				return true;
			}

		}

		/**
		 * 讀取語言訊息ini
		 */
		function loadLanguageini(){
			$file = LANGUAGE_PATH.$_SESSION[FRAME_NAME]['language'.$this->langSessionName].'.ini';
			if(!is_file($file)){
				$this->alert($this->getMessage('LANGUAGE_NULL'),-1);
				exit;
			}else{
				$tmpe = parse_ini_file($file,true);

				$this->message = @$tmpe['message'];
				$this->serbackLabel = @$tmpe['label'];

				/**
				 * 找不到就用預設的
				 */
				$tmpe = parse_ini_file(LANGUAGE_PATH.LANG.'.ini',true);
				foreach ($tmpe['message'] as $key => $value) {
					if(!isset($this->message[$key])){
						$this->message[$key] = $value;
					}
				}
				foreach ($tmpe['label'] as $key => $value) {
					if(!isset($this->serbackLabel[$key])){
						$this->serbackLabel[$key] = $value;
					}
				}

			}
		}

		/**
		 * 讀取language有哪些語言 Array ( [zh-tw] => 繁體中文 )
		 */
		function setLanguageArray(){
			$dir = dir(LANGUAGE_PATH);
			while($file = $dir->read()) {
			   	if (!is_dir($file) && strpos($file,'.ini')){
			   		$temp = parse_ini_file(LANGUAGE_PATH.$file,true);
			   		if(isset($temp['value']['LANGUAGE_NAME'])){
			   			$temp = htmlspecialchars($temp['value']['LANGUAGE_NAME']);
			   		}else{
			   			$temp = str_replace('.ini','',$file);
			   		}
			   		$this->languageArray[str_replace('.ini','',$file)] = $temp;
			   	}
			}
			$dir->close();
		}

		/**
		 * 輸出語言列表
		 * @param  string $value 輸出格式 html,array,json,form,serback
		 * @return [type]        照格式輸出
		 */
		function getLanguageArray($value='html',$path='',$class=''){
			switch ($value) {
				case 'html':
					$temp = '';
					foreach ($this->path as $key => $value) {
						$temp .= '/'.$value;
					}
					$temp .= ($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : '';
					$temp = '<select id="langSelect" onchange="location.href=\''.HTTP_PATH.'\'+this.value+\''.$path.$temp.'\'" class="'.$class.'">';
					foreach ($this->languageArray as $key => $value) {
						if($_SESSION[FRAME_NAME]['language'.$this->langSessionName] == $key){
							$temp .= '<option value="'.$key.'" selected>'.$value.'</option>';
						}else{
							$temp .= '<option value="'.$key.'">'.$value.'</option>';
						}
					}
					$temp .= '</select>';
					return $temp;
					break;

				case 'form'://表單用
					$temp = '<select name="lang"  class="'.$class.'">';
					foreach ($this->languageArray as $key => $value) {
						if($_SESSION[FRAME_NAME]['language'.$this->langSessionName] == $key){
							$temp .= '<option value="'.$key.'" selected>'.$value.'</option>';
						}else{
							$temp .= '<option value="'.$key.'">'.$value.'</option>';
						}
					}
					$temp .= '</select>';
					return $temp;
					break;

				case 'serback'://管理語系用
					$temp = '<select onchange="setLang(this.value);" class="'.$class.'">';
					foreach ($this->languageArray as $key => $value) {
						if($_SESSION[FRAME_NAME]["SETTING_LANG"] == $key){
							$temp .= '<option value="'.$key.'" selected>'.$value.'</option>';
						}else{
							$temp .= '<option value="'.$key.'">'.$value.'</option>';
						}
					}
					$temp .= '</select>';
					return $temp;
					break;

				case 'array':
					return $this->languageArray;
					break;
				
				case 'json':
					return json_encode($this->languageArray);
					break;
			}
		}

		/**
		 * 設定語言
		 * @param string $value 語言
		 */
		function setLanguage($value=LANG){
			$file = LANGUAGE_PATH.$value.'.ini';
			if(!is_file($file)){
				echo $this->getMessage('LANGUAGE_NULL',$value);
				exit;
			}else{
				$_SESSION[FRAME_NAME]['language'.$this->langSessionName] = $value;
				$this->loadLanguageini();
			}
		}

		/**
		 * 取得語言
		 * @return [type] [description]
		 */
		function getLanguage(){
			return $_SESSION[FRAME_NAME]['language'.$this->langSessionName];
		}

		/**
		 * 多國語標籤 hreflang 給Search Console看的   https://support.google.com/webmasters/answer/189077?hl=zh-Hant
		 * @return [type] [description]
		 */
		function getHreflang(){
			$temp = '';
			foreach ($this->path as $key => $value) {
				$temp .= '/'.$value;
			}
			$temp .= ($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : '';
			// $temp = ''.HTTP_PATH.'\'+this.value+\''.$temp.'\'">';

			$data = '';
			$array = $this->getLanguageArray('array');
			if(count($array)>1){
				foreach ($array as $key => $value) {
					$data .= '<link rel="alternate" href="'.HTTP_PATH.$key.$temp.'" hreflang="'.$key.'" />
';
				}
				$data .= '<link rel="alternate" href="'.substr(HTTP_PATH,0,-1).$temp.'" hreflang="x-default" />';//
			}
			return $data;
		}

		/**
		 * 顯示訊息
		 * @param  string $value 訊息代碼
		 * @param  array  $data  訊息參數
		 * @return string        訊息
		 */
		function getMessage($value='',$data=array()){
			$str = '';
			if(isset($this->message[$value])){
				$temp = $this->message[$value];
				if (is_array($data) && count($data)>0){
					ksort($data);
					foreach ($data as $k => $v) {
						$temp = str_replace('{'.($k+1).'}',$v,$temp);
					}							
				}
				$str = $temp;
			}else{
				$str = $_SESSION[FRAME_NAME]['language'.$this->langSessionName].".ini 語言檔不存在或是未設定".$value."訊息!";
			}

			return $str;
		}

		/**
		 * 取得label
		 * @param  string $value 代碼
		 * @return string        label
		 */
		function getLabel($value){
			if(isset($this->serbackLabel[$value])){
				return $this->serbackLabel[$value];
			}else{
				return $value;
			}
		}

		/**
		 * 顯示alert
		 * @param  string $message alert訊息
		 * @param  string $url     轉跳網址 -1:上一頁  NULL,"":reload  NO:不轉跳 CLOSE:關閉
		 */
		function alert($message,$url=NULL){
		    $message = str_replace(array("\r", "\n", "\r\n", "\n\r"), '', $message);
			print "<meta http-equiv=Content-Type content=text/html; charset=utf-8>";
			if(trim($url) == "-1"){
				echo "<script language=\"JavaScript\" type=\"text/JavaScript\">window.addEventListener('load',function(){alert(\"$message\");javascript:history.back(-1);});</script>";
				exit;
			}else if(trim($url)==NULL || trim($url)==""){
				echo "<script language=\"JavaScript\" type=\"text/JavaScript\">window.addEventListener('load',function(){alert(\"$message\");location.reload();});</script>";
			}else if(trim($url)=='NO'){
				echo "<script language=\"JavaScript\" type=\"text/JavaScript\">window.addEventListener('load',function(){alert(\"$message\");});</script>";
			}else if(trim($url)=='CLOSE'){
				echo "<script language=\"JavaScript\" type=\"text/JavaScript\">window.addEventListener('load',function(){alert(\"$message\");window.close();});</script>";
			}else{
				echo "<script language=\"JavaScript\" type=\"text/JavaScript\">window.addEventListener('load',function(){alert(\"$message\");location.href='$url';});</script>";
				exit;
			}
		}

		/**
		 * 加載controller
		 */
		function loadController(){
			global $console;
			if (is_file(CONTROLLER_PATH.$this->controller.'.php')){
				include_once(CONTROLLER_PATH.$this->controller.'.php'); 
				include_once(INCLUDE_PATH.'foor.php');
			}else{
				include_once(CONTROLLER_PATH.'index.php'); 
				include_once(INCLUDE_PATH.'foor.php');
				// $this->to404();
				// echo $this->getMessage('CONTROLLER_NULL',array($this->controller));
				exit;
			}
		}

		/**
		 * 防止一直傳送表單防止、重複傳送
		 */
		function POSTVerifty(){
			$nowTime = microtime(true);
			if($_POST){
				if(isset($_SESSION[FRAME_NAME]["POST_TIME"]) && ($nowTime-$_SESSION[FRAME_NAME]["POST_TIME"]<$this->config["POSTTime"])){
					$temp = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '//'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
					$this->alert($this->getMessage('POST_TOO_FAST',array(round($nowTime-$_SESSION[FRAME_NAME]["POST_TIME"],2))),$temp);
					$_SESSION[FRAME_NAME]["POST_TIME"] = $nowTime;
					exit;
				}else{
					$_SESSION[FRAME_NAME]["POST_TIME"] = $nowTime;
				}
			}
		}

		/**
		 * 防止XSS攻擊
		 */
		function XSSVerifty(){
			if($this->switch["XSSVerifty"]){
				if($_GET) $_GET = $this->XXSDataVerifty($_GET);
				if($_POST) $_POST = $this->XXSDataVerifty($_POST);
			}
		}

		/**
		 * XSS資料處理
		 */
		function XXSDataVerifty($data){
			foreach ($data as $key=>$value){
				$data[$key] = (!is_array($value)) ? /*addslashes*/(htmlspecialchars(/*strip_tags*/($value))) : $this->XXSDataVerifty($value);
			}
			return $data;
	    }

		/**
		 * 防止CSRF跨站攻擊
		 */
		function CSRFVerifty(){
			
			if($this->switch["CSRFVerifty"] && $_POST){

				/**
				 * 來源網址驗證
				 * HTTP_REFERER
				 * https > http 時會沒有值
				 */
				if(isset($_SERVER['HTTP_REFERER'])){
					$check_csrf = explode('//',$_SERVER['HTTP_REFERER']);
					$check_csrf = explode('/',$check_csrf[1]);
					$csrfWhitelist = explode("\n",$this->setting->getValue("csrfWhitelist"));//白名單
					$csrfWhitelist[] = $_SERVER['HTTP_HOST'];
					if (!in_array($check_csrf[0],$csrfWhitelist)) {
						$this->alert($this->getMessage('CSRF_REFFRER_NOT_TRUE'),-1);
						exit;
					}
				}else{
					$this->alert($this->getMessage('CSRF_REFFRER_NOT_TRUE'),-1);
					exit;
				}

				/**
				 * token驗證
				 */
				$csrfWhitelist = explode("\n",$this->setting->getValue("csrfWhitelist"));//白名單
				if (!in_array($check_csrf[0],$csrfWhitelist)) {
					if(isset($_POST[TOKEN_NAME]) && isset($_SESSION[FRAME_NAME]['CSRF_TOKEN']) && $token_key = array_search($_POST[TOKEN_NAME],$_SESSION[FRAME_NAME]['CSRF_TOKEN'])){
						unset($_POST[TOKEN_NAME]);
						unset($_SESSION[FRAME_NAME]["CSRF_TOKEN"][$token_key]);
					}else{
						if(isset($_POST[TOKEN_NAME])) unset($_POST[TOKEN_NAME]);
						$temp = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '//'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
						$this->alert($this->getMessage('CSRF_TOKEN_NOT_TRUE'),$temp);
						exit;
					}
				}
				if(!$_POST){
					$this->linkTo("//".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);	
				}
			}
			
		}

		/**
		 * 取得token
		 * @param  string $return 設定text的話只回傳token碼，不設定回傳input
		 * @return string         同上
		 */
		function getToken($return='html'){
			$nowTime = strtotime(DATE);
			while(isset($_SESSION[FRAME_NAME]['CSRF_TOKEN'][$nowTime])){
				$nowTime++;
			}
			$_SESSION[FRAME_NAME]['CSRF_TOKEN'][$nowTime] = hash_hmac($this->config["CSRFType"] ,rand(),$this->config["CSRFKey"]);

			if($return=='text'){
				return $_SESSION[FRAME_NAME]['CSRF_TOKEN'][$nowTime];
			}else{
				return '<input type="hidden" name="'.TOKEN_NAME.'" value="'.$_SESSION[FRAME_NAME]['CSRF_TOKEN'][$nowTime].'">';
			}
		}

		/**
		 * 取得token name
		 * @return string TOKEN_NAME
		 */
		function getTokenName(){
			return TOKEN_NAME;
		}

		/**
		 * 將不合法的token移除
		 */
		function rmBadToken(){
			$nowTime = strtotime(DATE);
			if(isset($_SESSION[FRAME_NAME]['CSRF_TOKEN'])){
				foreach ($_SESSION[FRAME_NAME]['CSRF_TOKEN'] as $key => $value) {
					if(count($_SESSION[FRAME_NAME]['CSRF_TOKEN'])>$this->config["CSRFTokenMax"] || ($nowTime-$key)>$this->config["CSRFTime"]){
						unset($_SESSION[FRAME_NAME]['CSRF_TOKEN'][$key]);
					}
				}
			}
		}

		/**
		 * 使用google的API產生QRCode
		 * @param  [type] $data        	資料
		 * @param  string $widthHeight 	寬高
		 * @param  string $EC_level    	L - Allows recovery of up to 7% data loss 預設
		 * 								M - Allows recovery of up to 15% data loss
		 *								Q - Allows recovery of up to 25% data loss
		 *								H - Allows recovery of up to 30% data loss
		 * @param  string $margin      	
		 * @param  string $choe 		UTF-8 預設
		 *								Shift_JIS
		 *								ISO-8859-1
		 * @return [type]              	回傳img tag
		 */
		function getQRCodeInGoogle($data,$widthHeight ='150',$EC_level='L',$margin='2',$choe='UTF-8') {
			$url = 'http://chart.apis.google.com/chart?cht=qr&chs='.$widthHeight.'x'.$widthHeight.'&chld='.$EC_level.'|'.$margin.'&chl='.$data.'&choe='.$choe;
		    return '<a href="'.$url.'"  target="_blank"><img src="'.$url.'" alt="QR code" width="'.$widthHeight.'" height="'.$widthHeight.'" /></a>';
		}

		/**
		 * reCAPTCHA驗證
		 * @param  [type] $value $_POST['g-recaptcha-response']
		 * @return [type]        [description]
		 */
		function checkreCAPTCHA($value){
			$curl = curl_init();
		    curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://www.google.com/recaptcha/api/siteverify?'.http_build_query(
		    		array(
			            'secret'   => $this->setting->getValue("reCAPTCHASecretKey"),//Secret key
			            'response' => $value
			        )
		    	),
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "GET",
			));

			$response = curl_exec($curl);
			curl_close($curl);
		    $response = json_decode($response, true);
		    return (isset($response["success"]) && intval($response["success"]) === 1) ? true : false;
		}

		/**
		 * 普通驗證碼驗證
		 * @param [type] $value $_POST['verifycode']
		 */
		function checkVerifyCode($value){
			$token_key = array_search(strtoupper($value),$_SESSION['__VERIFYCODE_LIST']);
			if($token_key !== false){
				unset($_SESSION['__VERIFYCODE_LIST'][$token_key]);
		    	return true;
			}
		    return false;
		}

		/**
		 * 取得指定資料庫大小
		 * @param  string $database [description]
		 * @return [type]           [description]
		 */
		function getDatabaseSize($database){
			return $this->conn->getRow("
                    SELECT table_schema ,SUM(data_length + index_length) AS size
                    FROM information_schema.TABLES
                    WHERE table_schema = '".$database."'
                ")["size"];
		}

		/**
		 * 計算資料庫/資料表大小
		 * @param  string $table [description]
		 * @return [type]        [description]
		 */
		function getSqlSize($table=''){
			if(!$table){
				$temp = $this->conn->GetArray("SHOW TABLE STATUS");
			}else{
				if(is_array($table)){
					$sql = "";
					foreach ($table as $key => $value) {
						$sql .= " NAME='".$value."' OR ";
					}
					$sql .= "1=0";
					$temp = $this->conn->GetArray("SHOW TABLE STATUS WHERE ".$sql);
				}else{
					$temp = $this->conn->GetArray("SHOW TABLE STATUS WHERE NAME='".$table."'");
				}
			}
			$total = 0;
			if($temp){
				foreach ($temp as $key => $value) {
					$total += $value["Data_length"] + $value["Index_length"];
				}
			}
			return $total;
		}

		/**
		 * 計算資料夾/檔案大小
		 * @param [type] $path [description]
		 */
		function getDirSize($path) {
		 
		    // I reccomend using a normalize_path function here
		    // to make sure $path contains an ending slash
		    // (-> http://www.jonasjohn.de/snippets/php/normalize-path.htm)
		 
		    // To display a good looking size you can use a readable_filesize
		    // function.
		    // (-> http://www.jonasjohn.de/snippets/php/readable-filesize.htm)
		 
		    $Size = 0;
		 
		    $Dir = opendir($path);
		 
		    if (!$Dir)
		        return -1;
		 
		    while (($File = readdir($Dir)) !== false) {
		 
		        // Skip file pointers
		        if ($File[0] == '.') continue;
		 
		        // Go recursive down, or add the file size
		        if (is_dir($path.$File))
		            $Size += $this->getDirSize($path.$File.DIRECTORY_SEPARATOR);
		        else
		            $Size += filesize($path.$File);
		    }
		 
		    closedir($Dir);
		 
		    return $Size;
		}

		/**
		 * 單位轉換
		 * @param  [type]  $size  大小
		 * @param  integer $depth 預設0，Bytes
		 * @return [type]         [description]
		 */
		function formatSize($size,$depth=0){
			$disk_array = array('0'=>'Bytes','1'=>'KB','2'=>'MB','3'=>'GB','4'=>'TB');
			return $size = ($size/1024>1) ? $this->formatSize($size/1024,++$depth) : number_format($size,2).$disk_array[$depth];
		}

		/**
		 * circle使用(資料夾/檔案)
		 * @param  [type]  $path 路徑
		 * @param  integer $max  最大
		 * @return [type]        size 大小,percentage 百分比,color 顏色,max 最大直
		 */
		function getDataSizeArray($path=APP_PATH,$max=524288000){
			$data['size'] = $this->getDirSize($path);
			$data['percentage'] = floatval(str_replace(',','',number_format($data['size']/$max*100,2)));

			if ($data['percentage']<30) {
				$data['color'] = '#A8C64A';
			}else if ($data['percentage']<50) {
				$data['color'] = '#3498DB';
			}else{
				$data['color'] = '#FF5555';
			}
			
			$data['size'] = $this->formatSize($data['size']);
			$data['max'] = $this->formatSize($max);
			return $data;
		}


		/**
		 * circle使用(sql)
		 * @param  [type]  $table 	資料表
		 * @param  integer $max  	最大
		 * @return [type]        	size 大小,percentage 百分比,color 顏色,max 最大直
		 */
		function getSqlSizeArray($table='',$max=524288000){
			$data['size'] = $this->getSqlSize($table);
			$data['percentage'] = number_format($data['size']/$max*100,2);

			if ($data['percentage']<30) {
				$data['color'] = '#A8C64A';
			}else if ($data['percentage']<50) {
				$data['color'] = '#3498DB';
			}else{
				$data['color'] = '#FF5555';
			}
			
			$data['size'] = $this->formatSize($data['size']);
			$data['max'] = $this->formatSize($max);
			return $data;
		}

		/**
		 * 跳到指定頁面
		 * @param  [type] $url [description]
		 * @return [type]      [description]
		 */
		function linkTo($url){	
			if($url=='-1')
				echo "<script>javascript:history.back(-1);</script>";
			else
				echo "<script>location.href='".$url."'</script>";
			exit;
		}

		/**
		 * HTTP狀態碼+跳到指定頁面
		 * @param  [type] $num HTTP狀態碼
		 * @param  [type] $url 跳到指定頁面
		 */
		function HTTPStatusCode($num,$url){
			static $http = array (
				100 => "HTTP/1.1 100 Continue",
				101 => "HTTP/1.1 101 Switching Protocols",
				200 => "HTTP/1.1 200 OK",
				201 => "HTTP/1.1 201 Created",
				202 => "HTTP/1.1 202 Accepted",
				203 => "HTTP/1.1 203 Non-Authoritative Information",
				204 => "HTTP/1.1 204 No Content",
				205 => "HTTP/1.1 205 Reset Content",
				206 => "HTTP/1.1 206 Partial Content",
				300 => "HTTP/1.1 300 Multiple Choices",
				301 => "HTTP/1.1 301 Moved Permanently",
				302 => "HTTP/1.1 302 Found",
				303 => "HTTP/1.1 303 See Other",
				304 => "HTTP/1.1 304 Not Modified",
				305 => "HTTP/1.1 305 Use Proxy",
				307 => "HTTP/1.1 307 Temporary Redirect",
				400 => "HTTP/1.1 400 Bad Request",
				401 => "HTTP/1.1 401 Unauthorized",
				402 => "HTTP/1.1 402 Payment Required",
				403 => "HTTP/1.1 403 Forbidden",
				404 => "HTTP/1.1 404 Not Found",
				405 => "HTTP/1.1 405 Method Not Allowed",
				406 => "HTTP/1.1 406 Not Acceptable",
				407 => "HTTP/1.1 407 Proxy Authentication Required",
				408 => "HTTP/1.1 408 Request Time-out",
				409 => "HTTP/1.1 409 Conflict",
				410 => "HTTP/1.1 410 Gone",
				411 => "HTTP/1.1 411 Length Required",
				412 => "HTTP/1.1 412 Precondition Failed",
				413 => "HTTP/1.1 413 Request Entity Too Large",
				414 => "HTTP/1.1 414 Request-URI Too Large",
				415 => "HTTP/1.1 415 Unsupported Media Type",
				416 => "HTTP/1.1 416 Requested range not satisfiable",
				417 => "HTTP/1.1 417 Expectation Failed",
				500 => "HTTP/1.1 500 Internal Server Error",
				501 => "HTTP/1.1 501 Not Implemented",
				502 => "HTTP/1.1 502 Bad Gateway",
				503 => "HTTP/1.1 503 Service Unavailable",
				504 => "HTTP/1.1 504 Gateway Time-out"
			);
			header($http[$num]);
			header("Location: $url");
			exit;
		}

		/**
		 * 新增query 
		 * smarty用法e.g. ({$console->addQuery(['unLink'=>'fb'])})
		 * @param array $data  array(key,value)
		 */
		 function addQuery($data=''){
			if(is_array($data)){
				foreach ($data as $key => $value) {
					$_GET[$key] = $value;
				}
			}
			return explode("?",$_SERVER['REQUEST_URI'])[0].'?'.http_build_query($_GET);
		}

		/**
		 * 設定網站設定 
		 */
		 function setWebSetting($data=''){
		 	if($data && is_a($data, 'MTsung\webSetting')){
		 		$this->webSetting = $data;
		 		return true;
		 	}else{
		 		return false;
		 	}
		}

		/**
		 * 404頁面
		 * @return [type] [description]
		 */
		function to404($data=""){
			http_response_code(404);
			global $console;
			include_once(INCLUDE_PATH.'foor.php');
			$this->design->loadDisplay('404.html');
			exit;
		}

		/**
		 * 轉換urlKey
		 * @param  [type] $data [description]
		 * @return [type]       [description]
		 */
		function urlKey($data){
			if(!$data){
				return false;
			}
			if(!isset($data["id"])){
				foreach ($data as $key => $value) {
					$data[$key]["urlKey"] = (isset($data[$key]["urlKey"])&&$data[$key]["urlKey"]) ? $data[$key]["urlKey"] : $data[$key]["id"];
				}
			}else{
				$data["urlKey"] = (isset($data["urlKey"])&&$data["urlKey"]) ? $data["urlKey"] : $data["id"];
			}
			return $data;
		}

		/**
		 * 輸出json
		 * @param  [type] $response [description]
		 * @param  [type] $message  [description]
		 * @param  [type] $data  	[description]
		 * @return [type]           [description]
		 */
		function outputJson($response,$message="",$data=""){
			echo json_encode(array(
				"response" => $response,
				"message" => $message,
				"data" => $data
			));
			exit;
		}

		/**
		 * 是否有資料表
		 * @param  string  $value [description]
		 * @return boolean        [description]
		 */
		public function isTables($value=''){
			$value = preg_replace("/[^A-Za-z0-9_ ]/", "", $value);
			return (boolean)$this->conn->GetArray("desc ".PREFIX.$value."__".str_replace("-","_",$this->getLanguage()));
		}
	}
}


?>