<?php


/**
 * cPanel
 * MTsung by 20181218
 */
namespace MTsung{

	class cPanel{
		var $console;
		var $message;
		var $account;
		var $password;
		var $auth;
		var $serverName;

		function __construct($console,$account,$password,$serverName=""){
			$this->console = $console;
			$this->account = $account;
			$this->password = $password;
			$this->auth = $this->account.":".$this->password;
			if($serverName){
				$this->serverName = $serverName;
			}else{
				$this->serverName = HTTP.$_SERVER['SERVER_NAME'].":2083";
			}
		}

		/**
		 * 取得頻寬
		 * @param [type] $grouping      欄位 |分隔
		 * @param [type] $domains       網域 |分隔
		 * @param [type] $start       	區間開始時間
		 * @param [type] $end       	區間結束時間
		 */
		function getBandwidth($grouping="domain|year_month",$domains="",$start="",$end=""){
			$url = "/execute/Bandwidth/query";
			if(is_array($grouping)){
				$grouping = implode("|",$grouping);
			}
			if(is_array($domains)){
				$domains = implode("|",$domains);
			}
			$data = array(
				"grouping" => $grouping,
				"interval" => "daily",
				"domains" => $domains,
				"start" => $start,
				"end" => $end
			);
			if(!$temp = $this->cCurl($url,"GET",$data)){
				return false;
			}
			if($err = $temp["errors"][0]){
				$this->message = $err;
				return false;
			}
			return $temp["data"];
		}

		/**
		 * 新增附加網域
		 * @param [type] $name      [description]
		 * @param [type] $subDoname [description]
		 */
		function addAddonDomain($name,$subDoname){
			$url = "/json-api/cpanel";
			$data = array(
				"cpanel_jsonapi_apiversion" => "2",
				"cpanel_jsonapi_module" => "AddonDomain",
				"cpanel_jsonapi_func" => "addaddondomain",
				"dir" => "/public_html",
				"newdomain" => $name,
				"subdomain" => $subDoname
			);
			if(!$temp = $this->cCurl($url,"GET",$data)){
				return false;
			}
			if($err = $temp["cpanelresult"]["error"]){
				$this->message = $err;
				return false;
			}
			return true;
		}

		/**
		 * 新增子網域
		 * @param [type] $name      [description]
		 * @param [type] $subDoname [description]
		 */
		function addSubDomain($name){
			$url = "/json-api/cpanel";
			$data = array(
				"cpanel_jsonapi_apiversion" => "2",
				"cpanel_jsonapi_module" => "SubDomain",
				"cpanel_jsonapi_func" => "addsubdomain",
				"dir" => "/public_html/".$name,
				"domain" => $name,
				"rootdomain" => str_replace('www.','',$_SERVER['SERVER_NAME'])
			);
			if(!$temp = $this->cCurl($url,"GET",$data)){
				return false;
			}
			if($err = $temp["cpanelresult"]["error"]){
				$this->message = $err;
				return false;
			}
			return true;
		}

		/**
		 * 新增資料庫
		 * @param [type] $name [description]
		 */
		function addDatabase($name){
			$url = "/json-api/cpanel";
			$data = array(
				// "cpanel_jsonapi_user" => "user",
				"cpanel_jsonapi_apiversion" => "2",
				"cpanel_jsonapi_module" => "MysqlFE",
				"cpanel_jsonapi_func" => "createdb",
				"db" => PREFIX.$name
			);
			if(!$temp = $this->cCurl($url,"GET",$data)){
				return false;
			}
			if($err = $temp["cpanelresult"]["error"]){
				$this->message = $err;
				return false;
			}
			return true;
		}

		/**
		 * curl
		 * @param  string $type    [description]
		 * @param  [type] $data    [description]
		 * @param  [type] $options [description]
		 * @param  [type] $header  [description]
		 * @return [type]          [description]
		 */
		function cCurl($url,$type='GET',$data=array(),$options=array(),$header=array()) {
			$ch = curl_init();
			$header[0] = "Authorization: Basic " . base64_encode($this->account.":".$this->password) . "\n\r";

			$url = $this->serverName.$url;

			if(strtoupper($type) == "GET"){
				$url = $url."?".http_build_query($data);
			}else{
				$options[CURLOPT_POST] = true;
				$options[CURLOPT_POSTFIELDS] = http_build_query($data);
			}

			$defaultOptions = array(
				CURLOPT_RETURNTRANSFER => true, // 不直接出現回傳值
				CURLOPT_URL => $url,
				CURLOPT_USERPWD => $this->auth,
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

			return json_decode($response,true);
		}
	}
}