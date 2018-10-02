<?php


/**
 * 操作紀錄
 * MTsung by 20180715
 */
namespace MTsung{

	class systemLog{
		use userDeviceInfomation;
		var $console;
		var $conn;
		var $table;
		var $lang;
		var $pageNumber;


		/**
		 * @param [type] $console [description]
		 * @param [type] $table   [description]
		 * @param string $lang    可略
		 */
		function __construct($console,$table,$lang=LANG){
			$this->setConsole($console);
			$this->conn = $this->console->conn;
			$this->lang = $lang;
			$this->table = $table;
			$this->checkTable();
			// $this->redoData();
		}

		/**
		 * 檢查資料表是否存在 不存在就建立
		 * @return [type] 存在 true,不存在 false
		 */
		public function checkTable(){
			$temp = $this->conn->GetArray("desc ".$this->table);
			if(!$temp){
				$this->conn->Execute("
					CREATE TABLE `".$this->table."` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `language` varchar(191) NOT NULL COMMENT '語系',
					  `type` varchar(191) NOT NULL COMMENT '異動模式',
					  `dataTable` varchar(191) NOT NULL COMMENT '異動資料表',
					  `oldData` text NOT NULL COMMENT '原始內容(json)',
					  `newData` text NOT NULL COMMENT '異動內容(json)',
					  `url` text NOT NULL COMMENT '異動網址',
					  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '異動時間',
					  `create_user` varchar(191) NOT NULL COMMENT '異動人',
					  `IP` varchar(20) NOT NULL COMMENT 'IP',
					  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;
				");
				return false;
			}
			return true;
		}

		/**
		 * 新增
		 * @param [type] $data [description]
		 */
		function addLog($type,$table,$oldData='',$newData=''){
			if(is_array($oldData)){
					$oldData = array_map("htmlspecialchars_decode",$oldData);
			}
			$data = array();
			if (isset($_SESSION[FRAME_NAME]["member"])) {
				if($this->console->langSessionName=="Serback" && $_SESSION[FRAME_NAME]["member"]["serback"]["account"]){
					$data["create_user"] = $_SESSION[FRAME_NAME]["member"]["serback"]["account"];
				}else if($this->console->langSessionName=="" && $_SESSION[FRAME_NAME]["member"]["member"]["account"]){
					$data["create_user"] = $_SESSION[FRAME_NAME]["member"]["member"]["account"];
				}else{
					foreach ($_SESSION[FRAME_NAME]["member"] as $key => $value) {
						$data["create_user"] = $value["account"];
						break;
					}
				}
			}

			$data["IP"] = $this->getIP();
			$data["type"] = $type;
			$data["dataTable"] = $table;
			$data["oldData"] = json_encode($oldData);
			$data["newData"] = json_encode($newData);
			$data["url"] = $_SERVER["REDIRECT_URL"]?$_SERVER["REDIRECT_URL"]:$_SERVER["REQUEST_URI"];
			$data["language"] = $this->lang;
			$this->conn->AutoExecute($this->table,$data,"INSERT");
		}

		/**
		 * 還原資料
		 * @param [type] $id [description]
		 */
		function redoData($id){
			$data = $this->getLog("where status='1' and id='".$id."'")[0];
			if($data){
				$data["oldData"] = json_decode($data["oldData"], true);
				$data["newData"] = json_decode($data["newData"], true);
				$result = false;
				switch($data["type"]){
					case 'INSERT':
						$result = $this->conn->Execute("delete from ".$data["dataTable"]." where id='".$data["newData"]["id"]."'");
						$this->conn->Execute("ALTER TABLE ".$data["dataTable"]." AUTO_INCREMENT=".$data["newData"]["id"].";");
						break;
					case 'DELETE':
						$result = $this->conn->AutoExecute($data["dataTable"],$data["oldData"],"INSERT");
						break;
					case 'UPDATE':
						$result = $this->conn->AutoExecute($data["dataTable"],$data["oldData"],"UPDATE","id='".$data["oldData"]["id"]."'");
						break;
					default:
						$this->message = $this->console->getMessage('UNKNOWN_ERROR');
				}
				if($result){
					$this->conn->Execute("update ".$this->table." set status='0' where id='".$id."'");
					$this->message = $this->console->getMessage('REDO_OK');
				}else{
					$this->message = $this->console->getMessage('REDO_ERROR');
				}
				return $result;
			}else{
				$this->message = $this->console->getMessage('REDO_REPEAT');
				return false;
			}
		}

		/**
		 * 取得資料 列表用
		 * @param  string  $whereSql    while sql
	 	 * @param  array   $searckKey   keyword搜尋的欄位名稱
		 * @param  integer $per         每頁幾筆
		 * @param  integer $pageViewMax 頁碼顯示N個
		 * @return [type]               [description]
		 */
		function getListLog($whereSql='',$searckKey=array("name"),$per=10,$pageViewMax=5){

			if(isset($_GET["per"]) && is_numeric($_GET["per"]) && ($_GET["per"] > 0)){
				$per = $_GET["per"];
			}

			$sql = "";
			if(isset($_GET["searchKeyWord"])){
				$sql = "where ";
				if($_GET["searchKeyWord"]!=''){
					$temp =  explode(" ",$_GET["searchKeyWord"]);
					$sql .= " (";
					foreach ($temp as $key => $value) {
						if($value!=''){
							foreach ($searckKey as $key1 => $value1) {
								$sql .= " ".$value1." LIKE '%".$value."%' or";
							}
						}
					}
					$sql = substr($sql,0,-2);
					$sql .= ")";
				}else{
					$sql .= " 1=1 ";
				}
				
				if($_GET["startDate"]){
					$sql .= " and create_date>'".$_GET["startDate"]."' ";
				}

				if($_GET["endDate"]){
					$sql .= " and create_date<'".$_GET["endDate"]."' ";
				}

				if($_GET["status"] != ''){
					$sql .= " and status='".$_GET["status"]."' ";
				}
			}else{
				$sql = "where 1=1 ";
			}
			$whereSql = $sql.$whereSql;
			$this->pageNumber = new pageNumber($this->console,'select * from '.$this->table." ".$whereSql,$per,$pageViewMax);
			return $this->getLog($whereSql." limit ".$this->pageNumber->getDataStart().",".$per);
		}

		/**
		 * 搜尋
		 * @param  string $whereSql while sql
		 * @return [type]           [description]
		 */
		function getLog($whereSql=''){
			$temp = $this->conn->GetArray("select * from ".$this->table." ".$whereSql);
			if($temp){
				return $temp;
			}else{
				$this->message = $this->console->getMessage('DATA_NULL');
				return false;
			}
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
