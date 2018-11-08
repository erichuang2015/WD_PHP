<?php

namespace MTsung{

	class analytics{
		use userDeviceInfomation;
		var $resetTime = 1800;//秒數內不重複計算
		var $table = PREFIX."analytics";
		var $table_page = PREFIX."analytics_page";
		var $console;
		var $member;

		function __construct($console){
			$this->setConsole($console);
			$this->conn = $this->console->conn;
			$this->checkTable();
			//刪除超過半年的資料
			$rmDate = date('Y-m-d H:i:s',strtotime(DATE)-(86400*365/2));
			$this->conn->Execute("delete from ".$this->table." where time<'".$rmDate."'");
		}

		/**
		 * 檢查資料表是否存在 不存在就建立
		 * @return [type] [description]
		 */
		public function checkTable(){
			$temp = $this->conn->GetArray("desc ".$this->table);
			if(!$temp){
				$this->conn->Execute("
					CREATE TABLE `".$this->table."` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `ip` varchar(191) DEFAULT NULL COMMENT 'ip',
					  `agent` TEXT DEFAULT NULL COMMENT '使用環境',
					  `device` varchar(191) DEFAULT NULL COMMENT '裝置',
					  `system` varchar(191) DEFAULT NULL COMMENT '系統',
					  `lang` varchar(191) DEFAULT NULL COMMENT '語系',
					  `referer` varchar(191) DEFAULT NULL COMMENT '來源網域',
					  `count` int(11) DEFAULT '1' COMMENT '次數',
					  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '時間',
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;
				");
			}
			$temp = $this->conn->GetArray("desc ".$this->table_page);
			if(!$temp){
				$this->conn->Execute("
					CREATE TABLE `".$this->table_page."` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `analyticsId` int(11) DEFAULT '1' COMMENT '關聯ID',
					  `type` varchar(191) DEFAULT NULL COMMENT 'path',
					  `url` varchar(191) DEFAULT NULL COMMENT '頁面',
					  `count` int(11) DEFAULT '1' COMMENT '次數',
					  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '時間',
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;
				");
			}
		}

		/**
		 * 新增log
		 */
		public function addLog(){

			//ajax不算
			if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
				return false;	
			}

			//去除機器人
			$bot_list = array (
			    'Googlebot',
			    'Yahoo! Slurp',
			    'Mediapartners-Google',
			    'msnbot',
			    'bingbot',
			    'MJ12bot',
			    'Ezooms',
			    'pirst; MSIE 8.0;',
			    'Google Web Preview',
			    'ia_archiver',
			    'Sogou web spider',
			    'Googlebot-Mobile',
			    'AhrefsBot',
			    'YandexBot',
			    'Purebot',
			    'Baiduspider',
			    'UnwindFetchor',
			    'TweetmemeBot',
			    'MetaURI',
			    'PaperLiBot',
			    'Showyoubot',
			    'JS-Kit',
			    'PostRank',
			    'Crowsnest',
			    'PycURL',
			    'bitlybot',
			    'Hatena',
			    'facebookexternalhit'
			);

			foreach ($bot_list as $bot) {
			    if (stripos($_SERVER['HTTP_USER_AGENT'], $bot) !== false) {
			        return false;
			    }
			}

			$data["referer"] = $this->getReferer();

			$endDate = date('Y-m-d H:i:s',strtotime(DATE)-$this->resetTime);

			$temp = array();
		    $temp[] = $data["agent"] = $_SERVER['HTTP_USER_AGENT'];
			$temp[] = $data["ip"] = $this->getIP();
			$temp[] = $data["system"] = $this->getSystem();
			$temp[] = $data["device"] = $this->getDevice();
			$temp[] = $data["lang"] = $this->console->getLanguage();

			if($analytics = $this->getLog("where agent=? and ip=? and system=? and device=? and lang=? and time>'".$endDate."'",$temp)){
				$id = $analytics[0]["id"];
				if($data["referer"]){
					$this->conn->execute($this->conn->Prepare("update ".$this->table." set count=count+1,referer=? where id=?"),array($data["referer"],$id));
				}else{
					$this->conn->execute($this->conn->Prepare("update ".$this->table." set count=count+1 where id=?"),array($id));
				}
			}else{
				$this->conn->AutoExecute($this->table,$data,"INSERT");
				$id = $this->conn->GetRow("SELECT LAST_INSERT_ID()")[0];
			}
			$this->addLogPage($id);
		}


		/**
		 * 頁面分析
		 * @param [type] $id 關聯ID
		 */
		function addLogPage($id){
			$temp[] = $data["analyticsId"] = $id;
			$temp[] = $data["url"] = explode("?",$_SERVER["REQUEST_URI"])[0];
			$data["type"] = isset($this->console->path[0])?$this->console->path[0]:"";
			if($page = $this->conn->GetRow($this->conn->Prepare("select * from ".$this->table_page." where analyticsId=? and url=?"),$temp)){
				$id = $page["id"];
				$this->conn->execute($this->conn->Prepare("update ".$this->table_page." set count=count+1 where id=?"),array($id));
			}else{
				$this->conn->AutoExecute($this->table_page,$data,"INSERT");
			}
		}

		/**
		 * 取得欄位重複資料出現次數
		 * @param  [type] $Field [description]
		 * @return [type]        [description]
		 */
		public function getFieldCountPage($isRepeat=false,$Field,$startTime='',$endTime='',$type=''){
			$startTime = strtotime($startTime)? $startTime : "1990-01-01";
			$endTime = strtotime($endTime)? $endTime : "2999-01-01";
			$typeSql = "";
			if($type){
				$typeSql = " type='".$type."' and ";
			}
			if($isRepeat){
				return $this->conn->GetArray($this->conn->Prepare("select ".$Field." as name,sum(count) as count from ".$this->table_page." where ".$typeSql." time>=? and time<=? group by ".$Field.""),array($startTime,$endTime));
			}else{
				return $this->conn->GetArray($this->conn->Prepare("select ".$Field." as name,count(".$Field.") as count from ".$this->table_page." where ".$typeSql." time>=? and time<=? group by ".$Field.""),array($startTime,$endTime));
			}
		}

		/**
		 * 取得log
		 * @param  string $whereSql [description]
		 * @param  array  $sqlArray [description]
		 * @return [type]           [description]
		 */
		public function getLog($whereSql='',$sqlArray=array()){
			return $this->conn->GetArray($this->conn->Prepare("select * from ".$this->table." ".$whereSql),$sqlArray);
		}

		/**
		 * 取得總瀏覽人數
		 * @param  boolean $isRepeat 是否計算重複來源
		 * @return [type]            [description]
		 */
		public function getTotalCount($isRepeat=false,$startTime='',$endTime=''){
			$startTime = strtotime($startTime)? $startTime : "1990-01-01";
			$endTime = strtotime($endTime)? $endTime : "2999-01-01";
			if($isRepeat){
				return $this->conn->GetRow($this->conn->Prepare("select sum(count) from ".$this->table." where time>=? and time<=?"),array($startTime,$endTime))[0];
			}else{
				return $this->conn->GetRow($this->conn->Prepare("select count(count) from ".$this->table." where time>=? and time<=?"),array($startTime,$endTime))[0];
			}
		}

		/**
		 * 取得欄位重複資料出現次數
		 * @param  [type] $Field [description]
		 * @return [type]        [description]
		 */
		public function getFieldCount($Field,$startTime='',$endTime=''){
			$startTime = strtotime($startTime)? $startTime : "1990-01-01";
			$endTime = strtotime($endTime)? $endTime : "2999-01-01";
			return $this->conn->GetArray($this->conn->Prepare("select ".$Field." as name,count(".$Field.") as count from ".$this->table." where time>=? and time<=? group by ".$Field.""),array($startTime,$endTime));
		}


	    /**
	     * 設定console
	     * @param Mtsung/main $console 
	     */
	    public function setConsole($console){
	    	$this->console = $console;
	    }
	}
}