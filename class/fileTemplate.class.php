<?php


/**
 * 樣板檔案 
 * MTsung by 20180710
 */
namespace MTsung{

	class fileTemplate{
		var $console;
		var $conn;
		var $table = PREFIX."template";//資料表
		var $dirPath = APP_PATH."view/templates";//資料夾
		var $message;

		/**
		 * @param [type] $console [description]
		 */
		function __construct($console){
			$this->setConsole($console);
			$this->conn = $console->conn;
			$this->checkTable();
				
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
					  `type` varchar(191) NOT NULL COMMENT 'type',
					  `name` varchar(191) NOT NULL COMMENT 'name',
					  `detail` text NOT NULL COMMENT 'detail',
					  `sort` int(11) NOT NULL COMMENT '排序',
					  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
					  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最後修改時間',
					  `create_user` varchar(20) NOT NULL COMMENT '創建人',
					  `update_user` varchar(20) NOT NULL COMMENT '最後修改人',
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `name` ( `type`,`name`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;
				");
				return false;
			}
			return true;
		}

		/**
		 * 取得樣板內容
		 * @param  [type] $id [description]
		 * @return [type]     [description]
		 */
		function getFile($id){
			$temp = $this->conn->getRow("select * from ".$this->table." where id='".$id."'");
			if($temp){
				$fileName = $this->dirPath."/".$temp["type"]."/".$temp["name"];
				$file = fopen($fileName, "r") or die("Unable to open file!");
				if(filesize($fileName) > 0){
					$string = fread($file,filesize($fileName));
				}else{
					$string = '';
				}
				fclose($file);
				return htmlspecialchars($string);
			}else{
				$this->message = $this->console->getMessage('NOT_AUTHORITY');
				return false;
			}
		}

		/**
		 * 寫入檔案
		 * @param [type] $id   [description]
		 * @param [type] $data [description]
		 */
		function setFile($id,$data){
			$temp = $this->conn->getRow("select * from ".$this->table." where id='".$id."'");
			if($temp){
				$fileName = $this->dirPath."/".$temp["type"]."/".$temp["name"];
				$file = fopen($fileName, "w") or die("Unable to open file!");
				fwrite($file,$data);
				fclose($file);
				$this->message = $this->console->getMessage('EDITOR_OK');
				return true;
			}else{
				$this->message = $this->console->getMessage('NOT_AUTHORITY');
				return false;
			}
		}

		/**
		 * 刪除檔案
		 * @param  [type] $id [description]
		 * @return [type]     [description]
		 */
		function rmFile($id){
			$temp = $this->conn->getRow("select name from ".$this->table." where id='".$id."'");
			if($temp){
				$fileName = $this->dirPath."/".$this->console->path[1]."/".$temp["name"];
				unlink($fileName);
				return true;
			}else{
				$this->message = $this->console->getMessage('NOT_AUTHORITY');
				return false;
			}
		}

		/**
		 * 新增檔案
		 * @param [type] $name [description]
		 * @param [type] $data [description]
		 */
		function addFile($name,$data){
			$fileName = $this->dirPath."/".$this->console->path[1]."/".$name;
			if(!is_file($fileName)){
				$file = fopen($fileName, "w") or die("Unable to open file!");
				fwrite($file,$data);
				fclose($file);
				$this->message = $this->console->getMessage('ADD_OK');
				return true;
			}else{
				$this->message = $this->console->getMessage('DATA_REPEAT');
				return false;
			}
		}

		/**
		 * 新增資料
		 * @param [type] $data [description]
		 */
		function addData($data){
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
			$data["create_user"] = $data["update_user"];
			$data["sort"] = 0;
			if($this->conn->AutoExecute($this->table,$data,"INSERT")){
				$this->conn->Execute("set @j:=0;");
				$this->conn->Execute("update ".$this->table." set sort=@j:=@j+1 where type='".$this->console->path[1]."' order by sort");
				$this->message = $this->console->getMessage('ADD_OK');
				return true;
			}else{
				$this->message = $this->console->getMessage('ADD_ERROR');
				return false;
			}
		}

		/**
		 * 取得資料庫內容
		 * @param  [type] $id [description]
		 * @return [type]     [description]
		 */
		function getData($id){
			$temp = $this->conn->getRow("select * from ".$this->table." where id='".$id."'");
			if($temp){
				$temp = array_map("htmlspecialchars",$temp);
				return $temp;
			}else{
				$this->message = $this->console->getMessage('NOT_AUTHORITY');
				return false;
			}
		}

		/**
		 * 資料庫寫入 檔案名稱
		 * @param [type] $id   [description]
		 * @param [type] $data [description]
		 */
		function setData($id,$data){
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
			if(isset($data["name"])){
				$temp = $this->conn->getRow("select * from ".$this->table." where id='".$id."'");
				$oldFileName = $this->dirPath."/".$temp["type"]."/".$temp["name"];
				$newFileName = $this->dirPath."/".$temp["type"]."/".$data["name"];
				rename($oldFileName,$newFileName);
			}

			if($this->conn->AutoExecute($this->table,$data,"UPDATE","id='".$id."'")){
				$this->message = $this->console->getMessage('EDITOR_OK');
				return true;
			}else{
				$this->message = $this->console->getMessage('EDITOR_ERROR');
				return fasle;					
			}
			
		}

		/**
		 * 資料庫刪除 檔案
		 * @param  [type] $id [description]
		 * @return [type]     [description]
		 */
		function rmData($id){
			if(is_array($id)){
				foreach ($id as $key => $value) {
					$this->rmFile($value);
				}
				$deleteID = implode(",",$id);
			}else{
				$deleteID = $id;
			}

			if($this->conn->Execute("delete from ".$this->table." where id in (".$deleteID.")")){
				$this->conn->Execute("set @j:=0;");
				$this->conn->Execute("update ".$this->table." set sort=@j:=@j+1 where type='".$this->console->path[1]."' order by sort");
				$this->message = $this->console->getMessage('DELETE_OK');
				return true;
			}else{
				$this->message = $this->console->getMessage('DELETE_ERROR');
				return false;
			}
		}


		/**
		 * 列表頁存檔用
		 * @param [type] $data [description]
		 */
		function setDataAll($data){

			$newData = array();
			foreach ($data as $key=>$value) {
				//分解取出id
				$temp = explode("_",$key);
				$dataID = $temp[count($temp)-1];
				unset($temp[count($temp)-1]);
				//重組
				$dataKey = implode("_",$temp);
				
				array_push($newData, array("id" => $dataID, $dataKey => $value));
			}
			foreach ($newData as $key => $value) {
				$this->setData($value["id"],$value);
			}
			$this->conn->Execute("set @j:=0;");
			$this->conn->Execute("update ".$this->table." set sort=@j:=@j+1 where type='".$this->console->path[1]."' order by sort");
			return true;
		}

		/**
		 * 讀取檔案列表
		 * @param  [type] $type  [description]
		 */
		function getListData($type){
			$temp = $this->conn->getArray("select * from ".$this->table." where type='".$type."' order by sort ");
			return $temp;
		}
		
	    /**
	     * 設定console
	     * @param Mtsung/main $console 
	     */
	    function setConsole($console){
	    	$this->console = $console;
	    }
	}
}
