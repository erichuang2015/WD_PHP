<?php


/**
 * 網站設定類
 * MTsung by 20180622
 */
namespace MTsung{

	class webSetting{
		var $console;
		var $conn;
		var $table;
		var $message;
		var $lang;
		var $data = array();
		var $pictureName = array("picture","icon","image","images","img","watermark");//圖片name

		function __construct($console,$table,$lang=''){
			$this->setConsole($console);
			$this->conn = $this->console->conn;
			$this->lang = $lang;
			//是否需要語系
			if($this->lang){
				$this->table = $table.'__'.str_replace("-","_",$this->lang);//不能用-
			}else{
				$this->table = $table;
			}

			if($this->checkTable($this->table)){
				$this->loadData();
			}
			if (class_exists('MTsung\systemLog')) {
				$this->systemLog = new systemLog($this->console,PREFIX."system_log",$this->lang);
			}
		}

		/**
		 * 檢查資料表是否存在 不存在就建立
		 * @param  [type] $table 	table
		 * @return [type] 			存在 true,不存在 false
		 */
		public function checkTable($table){
			$temp = $this->conn->GetArray("desc ".$table);
			if(!$temp){
				$this->conn->Execute("
					CREATE TABLE `".$table."` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `name` varchar(191) NOT NULL COMMENT 'name',
					  `detail` text NOT NULL COMMENT 'detail' COMMENT '內容',
					  PRIMARY KEY (`id`),
					  UNIQUE(`name`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;
				");
				return false;
			}
			return true;
		}

		/**
		 * 讀取設定
		 * @return [type] [description]
		 */
		function loadData(){
			$this->data = $this->conn->GetArray("select * from ".$this->table."");
			if(is_array($this->data)){
				$temp = array();
				foreach ($this->data as $key => $value) {
					$temp[$value["name"]] = htmlspecialchars($value["detail"]);
				}
				$this->data = $temp;
			}
		}

		/**
		 * 寫入設定
		 * @param [type] $data [description]
		 */
		function setData($data){
			//把有POST的圖片拿掉
			if($this->pictureName){
				foreach ($this->pictureName as $key => $value) {
					if(isset($data[$value]) && $data[$value] && $_SESSION[FRAME_NAME]["PICTURE_TEMP"]){
						if(is_array($data[$value])){
							foreach ($data[$value] as $key => $value) {
								if(is_numeric($key1 = array_search($value,$_SESSION[FRAME_NAME]["PICTURE_TEMP"]))){
									unset($_SESSION[FRAME_NAME]["PICTURE_TEMP"][$key1]);
								}
							}
						}else{
							if(is_numeric($key = array_search($value,$_SESSION[FRAME_NAME]["PICTURE_TEMP"]))){
								unset($_SESSION[FRAME_NAME]["PICTURE_TEMP"][$key]);
							}
						}
					}
				}
			}
			
			$data["update_date"] = DATE;
			$data["update_user"] = '__AUTO__';
			if (isset($_SESSION[FRAME_NAME]["member"])) {
				if($this->console->langSessionName=="Serback" && isset($_SESSION[FRAME_NAME]["member"]["serback"]["account"])){
					$data["update_user"] = $_SESSION[FRAME_NAME]["member"]["serback"]["account"];
				}else if($this->console->langSessionName=="" && isset($_SESSION[FRAME_NAME]["member"]["member"]["account"])){
					$data["update_user"] = $_SESSION[FRAME_NAME]["member"]["member"]["account"];
				}else{
					foreach ($_SESSION[FRAME_NAME]["member"] as $key => $value) {
						$data["update_user"] = isset($value["account"])?$value["account"]:"_AUTO_";
						break;
					}
				}
			}


			if(is_array($data)){
				foreach ($data as $key => $value) {
					if(is_array($value)){
						$value = implode("|__|",$value);
					}
					if($temp = $this->conn->GetRow("select * from ".$this->table." where name='".$key."'")){
						if($temp["detail"]!=$value){
							$this->conn->AutoExecute($this->table,array('detail' => $value),"UPDATE","name='".$key."'");
							if("update_date" != $key && "update_user" != $key){
								$this->systemLog->addLog("UPDATE",$this->table,$temp,array('detail' => $value));
							}
						}
					}else{
						$this->conn->AutoExecute($this->table,array("name" => $key , "detail" => $value),"INSERT");
						$id = $this->conn->GetRow("SELECT LAST_INSERT_ID()")[0];
						$this->systemLog->addLog("INSERT",$this->table,array(),array("id" => $id ,"name" => $key , "detail" => $value));
					}
				}
				$this->loadData();
				$this->message = $this->console->getMessage('EDIT_OK');
				return true;
			}else{
				$this->message = $this->console->getMessage('EDIT_ERROR');
				return false;
			}
		}

		/**
		 * 複製語系
		 * @param  [type] $lang [description]
		 * @return [type]       [description]
		 */
		function copyLang($lang){
			if($this->lang && ($lang != $this->lang) && isset($this->console->languageArray[$lang])){
				$temp = $this->conn->GetArray("desc ".$this->table);

				//要複製的欄位
				$allKey = '';
				foreach ($temp as $key => $value) {
					if($value['Field'] != 'id' && $value['Field'] != 'create_date' && $value['Field'] != 'update_date'){
						$allKey .= $value['Field'].',';
					}
				}
				$allKey = substr($allKey,0,-1);

				//新table
				$newTable = explode('__', $this->table)[0].'__'.str_replace("-","_",$lang);
				$this->checkTable($newTable);


				//複製到新table
				$this->conn->Execute("insert ignore into ".$newTable." (".$allKey.") select ".$allKey." from ".$this->table."");


				$this->message = $this->console->getMessage('COPY_OK');
				return true;
			}else{
				$this->message = $this->console->getMessage('COPY_ERROR');
				return false;
			}
		}

		/**
		 * 複製到所有語系
		 */
		function copyAllLang(){
			if($this->lang){
				$AllKey = array_keys($this->console->languageArray);
				foreach ($AllKey as $key => $value) {
					if($value != $this->lang){
						$this->copyLang($value);
					}
				}
				$this->message = $this->console->getMessage('COPY_OK');
				return true;
			}else{
				$this->message = $this->console->getMessage('COPY_ERROR');
				return false;
			}
		}

		/**
		 * 取得資料
		 * @param  [type] $value [description]
		 * @return [type]        [description]
		 */
		function getValue($value=''){
			if($value && isset($this->data[$value])){
				return $this->data[$value];
			}else if($value == ''){
				return $this->data;
			}else{
				return false;
			}
		}

		/**
		 * 增加圖片name
		 */
		function addPictureName($data){
			$this->pictureName[] = $data;
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
