<?php


/**
 * 系統設定
 * MTsung by 20180615
 */
namespace MTsung{

	class setting{
		var $conn;
		var $table;
		var $message;
		var $pictureName = array("picture","icon","image","images","img","watermark");//圖片name
		var $data = array();



		function __construct($conn){
			$this->conn = $conn;
			$this->table = PREFIX.'setting';

			if(!$this->checkTable()){
				$this->setData($this->data);
			}else{
				$this->loadData();
			}
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
					  `name` varchar(191) NOT NULL COMMENT 'name',
					  `detail` text NOT NULL COMMENT 'detail',
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `name` (`name`)
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
			$this->data = $this->conn->GetArray("select * from ".$this->table);
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
								if(is_numeric($key1 = array_search(str_replace(UPLOAD_PATH,"",$value),$_SESSION[FRAME_NAME]["PICTURE_TEMP"]))){
									unset($_SESSION[FRAME_NAME]["PICTURE_TEMP"][$key1]);
								}
							}
						}else{
							if(is_numeric($key = array_search(str_replace(UPLOAD_PATH,"",$data[$value]),$_SESSION[FRAME_NAME]["PICTURE_TEMP"]))){
								unset($_SESSION[FRAME_NAME]["PICTURE_TEMP"][$key]);
							}
						}
					}
				}
			}

			$data["update_date"] = date("Y-m-d H:i:s");
			$data["update_user"] = '__AUTO__';
			if (isset($_SESSION[FRAME_NAME]["member"])) {
				$data["update_user"] = $_SESSION[FRAME_NAME]["member"]["serback"]["account"];
			}
			if(is_array($data)){
				foreach ($data as $key => $value) {
					if($this->conn->GetArray("select * from ".$this->table." where name='".$key."'")){
						if($this->data[$key]!=$value){
							$this->conn->AutoExecute($this->table,array('detail' => $value),"UPDATE","name='".$key."'");
						}
					}else{
						$this->conn->AutoExecute($this->table,array("name" => $key , "detail" => $value),"INSERT");
					}
				}
				$this->loadData();
				return true;
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
		 * 取得設定
		 * @param  [type] $value [description]
		 * @return [type]        [description]
		 */
		function getValue($value=''){
			if($value){
				if(isset($this->data[$value])){
					return $this->data[$value];
				}
				return false;
			}else{
				return $this->data;
			}
		}


	}
}
