<?php


/**
 * CRUD 、 語系複製
 * $_GET["class"] 判斷是否使用分類排序
 * MTsung by 20180625
 */
namespace MTsung{

	class center{
		var $console;
		var $conn;
		var $tableSort;//分類排序資料表名稱
		var $table;//資料表名稱
		var $message;//訊息
		var $lang;//語言
		var $keyException = array("");//例外不新增的鍵值
		var $pageNumber;//頁碼
		var $isTree = false;//是否為分類樹
		var $systemLog;//操作記錄
		var $pictureName = array("picture","icon","image","images","img","watermark");//圖片name


		/**
		 * @param [type] $console [description]
		 * @param [type] $table   [description]
		 * @param string $lang    可略
		 */
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
			$this->tableSort = $this->table."_sort";
			$this->systemLog = new systemLog($this->console,PREFIX."system_log",$this->lang);

			if(isset($_GET["class"]) && is_numeric($_GET["class"]) && $_GET["class"]){
				$this->checkSortTable();
			}
		}

		/**
		 * 取得所有欄位
		 * @return [type] [description]
		 */
		function getField(){
			$temp = $this->conn->GetArray("desc ".$this->table);
			$field = array();
			foreach ($temp as $key => $value) {
				array_push($field,$value["Field"]);
			}
			return $field;
		}

		/**
		 * 新增或修改
		 * @param [type]  $data          資料
		 * @param boolean $isSetAll      是否為列表存檔
		 * @param array   $checkArray    欄位白名單
		 * @param array   $requiredArray 必填欄位
		 */
		function setData($data,$isSetAll=false,$checkArray=array(),$requiredArray=array()){

			//過濾資料
			$data = $this->checkData($data,$checkArray);
			//檢查必填
			if($temp = $this->requiredData($data,$requiredArray)){
				$this->message = $this->console->getMessage('REQURED_NULL');
				return false;			
			}

			//自定義網址
			if(isset($data["urlKey"])){
				if(trim($data["urlKey"])==""){
					$data["urlKey"] = NULL;
				}else{
					if(isset($data["id"])){
						if($this->getData("where urlKey='".$data["urlKey"]."' and id<>'".$data["id"]."'")){
							$this->message = $this->console->getMessage("CUSTOM_URL_REPEAT");
							return false;	
						}
					}else{
						if($this->getData("where urlKey='".$data["urlKey"])){
							$this->message = $this->console->getMessage("CUSTOM_URL_REPEAT");
							return false;
						}
					}
				}
			}

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
			
			//陣列轉換
			foreach ($data as $key => $value) {
				if(is_array($value)){
					$data[$key] = implode("|__|",$value);
				}
			}
			
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

			$this->autoAddKey($data);



			//資料寫入
			if(isset($data["id"])){
				//修改

				$temp = $data;
				unset($temp["update_user"]);
				if($this->getData($this->makeWhereSql($temp))){
					$this->message = $this->console->getMessage("DATA_NO_CHANGE");
					return false;
				}

				$data["update_date"] = DATE;
				$oldData = $this->getData("where id='".$data["id"]."'")[0];
				if($this->conn->AutoExecute($this->table,$data,"UPDATE","id='".$data["id"]."'")){
					if(!$this->isTree && !$isSetAll) $this->sortTable();
					$this->message = $this->console->getMessage('EDIT_OK');
					$this->systemLog->addLog("UPDATE",$this->table,$oldData,$data);
					return true;
				}else{
					$this->message = $this->console->getMessage('EDIT_ERROR');
					error_log($this->message.$this->conn->errorMsg());
					return false;
				}
			}else{
				//新增
				if(in_array("sort", $this->getField())){
					$data["sort"] = 0;
				}
				if(in_array("step", $this->getField())){
					$data["step"] = 0;
				}
				$data["create_date"] = $data["update_date"] = DATE;
				$data["create_user"] = $data["update_user"];
				if($this->conn->AutoExecute($this->table,$data,"INSERT")){

					$data["id"] = $this->conn->GetRow("SELECT LAST_INSERT_ID()")[0];
					if(isset($_GET["class"]) && is_numeric($_GET["class"]) && $_GET["class"]){
						$dataSort = array(
							"classID" => $_GET["class"],
							"dataID" => $data["id"],
							"sort" => '0'
						);
						$this->conn->AutoExecute($this->tableSort,$dataSort,"INSERT");
					}

					if(!$this->isTree) $this->sortTable();
					$this->message = $this->console->getMessage('ADD_OK');
					$this->systemLog->addLog("INSERT",$this->table,array(),$data);
					return $data["id"];
				}else{
					$this->message = $this->console->getMessage('ADD_ERROR');
					error_log($this->message.$this->conn->errorMsg());
					return false;					
				}
			}
		}

		/**
		 * 列表頁存檔用
		 * @param [type] $data [description]
		 */
		function setDataAll($data,$checkArray=array()){

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
			$isSet = false;//是否有修改
			foreach ($newData as $key => $value) {

				//分類排序
				if(isset($_GET["class"]) && is_numeric($_GET["class"]) && $_GET["class"] && isset($value["sort"])){
					$dataSort = array(
						"classID" => $_GET["class"],
						"dataID" => $value["id"],
						"sort" => $value["sort"]
					);

					if(!$this->conn->getRow("select * from ".$this->tableSort." ".$this->makeWhereSql($dataSort))){
						if($this->conn->getRow("select * from ".$this->tableSort." where classID='".$_GET["class"]."' and dataID='".$value["id"]."'")){
							$this->conn->AutoExecute($this->tableSort,$dataSort,"UPDATE","classID='".$_GET["class"]."' and dataID='".$value["id"]."'");
						}else{
							$this->conn->AutoExecute($this->tableSort,$dataSort,"INSERT");
						}
						$this->message = $this->console->getMessage('EDIT_OK');
						$isSet = true;
					}
					unset($value);
				}

				if(isset($value) && !$this->getData($this->makeWhereSql($value))){
					$this->setData($value,true,$checkArray);
					$isSet = true;
				}
			}
			if($isSet){
				if(!$this->isTree) $this->sortTable();
			}else{
				$this->message = $this->console->getMessage("DATA_NO_CHANGE");
				return false;
			}
			return true;
		}

		/**
		 * 取得資料 列表用
		 * @param  string  $whereSql    sql and xxxx
	 	 * @param  array   $searchKey   keyword搜尋的欄位名稱
		 * @param  integer $per         每頁幾筆
		 * @param  integer $pageViewMax 頁碼顯示N個
		 * @return [type]               [description]
		 */
		function getListData($whereSql='',$searchKey=array("name"),$per=15,$pageViewMax=5,$queryName='page'){

			$searchKey[] = "name";

			if(isset($_GET["per"]) && is_numeric($_GET["per"]) && ($_GET["per"] > 0)){
				$per = $_GET["per"];
			}

			
			$whereSql = $this->getSqlWhere($searchKey,strpos($whereSql,"class like")===false).$whereSql;
			$this->pageNumber = new pageNumber($this->console,'select * from '.$this->table." ".$whereSql,$per,$pageViewMax,$queryName);
			return $this->getData($whereSql." limit ".$this->pageNumber->getDataStart().",".$this->pageNumber->getPer());
		}

		/**
		 * 取得GET組合的sql
		 * @param  array   $searchKey [description]
		 * @param  boolean $classFlag class搜尋
		 * @return [type]             [description]
		 */
		function getSqlWhere($searchKey=array("name"),$classFlag=true){
			$sql = "";
			if(isset($_GET["searchKeyWord"])){
				$sql = "where ";
				if($_GET["searchKeyWord"]!=''){
					$temp =  explode(" ",$_GET["searchKeyWord"]);
					$sql .= " (";
					foreach ($temp as $key => $value) {
						if($value!='' && $searchKey){
							foreach ($searchKey as $key1 => $value1) {
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
					$sql .= " and update_date>'".$_GET["startDate"]."' ";
				}

				if($_GET["endDate"]){
					$sql .= " and update_date<'".$_GET["endDate"]."' ";
				}

				if($_GET["status"] != ''){
					$sql .= " and status='".$_GET["status"]."' ";
				}
			}else{
				$sql = "where 1=1 ";
			}
			if(isset($_GET["class"]) && is_numeric($_GET["class"]) && $_GET["class"] && $classFlag){
				$sql .= $this->findArrayString("class",$_GET["class"]);
			}
			if(isset($_GET["stockBelow"]) && is_numeric($_GET["stockBelow"]) && $_GET["stockBelow"] && $_GET["stockBelow"]<1000){

				$temp = "(";
				for ($i=0; $i < $_GET["stockBelow"] ; $i++) { 
					if($i < $_GET["stockBelow"]-1){
						$temp .= $i."|";
					}else{
						$temp .= $i;
					}
				}
				$temp .= ")";
				$sql .= " and (stock REGEXP '^".$temp."[|].*' or stock REGEXP '.*[|]".$temp."$' or stock REGEXP '.*[|]".$temp."[|].*' or stock REGEXP '^".$temp."$')";
			}
			return $sql;
		}

		/**
		 * 搜尋
		 * @param  string $whereSql     [description]
		 * @param  array  $sqlArray     [description]
		 * @param  array  $explodeArray 需要轉陣列的欄位
		 * @param  array  $module       模組
		 * @return [type]               [description]
		 */
		function getData($whereSql='',$sqlArray=array(),$explodeArray=array(),$module=array()){
			//分類排序
			if(strpos($whereSql,"limit") && isset(explode(",",explode("limit ",$whereSql)[1])[1])){
				$tempSql = "limit".explode("limit",$whereSql)[1];
				$count = array(
					explode(",",explode("limit ",$whereSql)[1])[0],
					explode(",",explode("limit ",$whereSql)[1])[1]
				);
				$whereSql = explode("limit",$whereSql)[0];
			}
			//分類排序

			if($sqlArray){
				$temp = $this->conn->GetArray($this->conn->Prepare("select * from ".$this->table." ".$whereSql),$sqlArray);
			}else{
				$temp = $this->conn->GetArray("select * from ".$this->table." ".$whereSql);
			}

			//分類排序
			$whereSql .= $tempSql;
			//分類排序
			
			// if($this->conn->errorMsg()) echo "error :["."select * from ".$this->table." ".$whereSql."]<br>".$this->conn->errorMsg();

			//分類排序
			if(isset($_GET["class"]) && is_numeric($_GET["class"]) && $_GET["class"] && $temp){
				foreach ($temp as $key => $value) {
					if($sort = $this->conn->getRow("select sort from ".$this->tableSort." where classID='".$_GET["class"]."' and dataID='".$value["id"]."'")){
						$temp[$key]["sort"] = $sort[0];
					}
				}
				usort($temp, array($this, 'classSort'));
			}
			if(strpos($whereSql,"limit") && isset(explode(",",explode("limit ",$whereSql)[1])[1])){
				$temp = array_slice($temp,$count[0],$count[1]);
			}
			// print_r($whereSql);
			//分類排序

			if($temp){
				foreach ($temp as $key => $value) {
					$temp[$key] = array_map("htmlspecialchars",$temp[$key]);

					if($explodeArray){//需要轉陣列的欄位
						foreach ($explodeArray as $valueE) {
							if(($valueE != "") && !is_array($temp[$key][$valueE]) && $temp[$key][$valueE]){
								$temp[$key][$valueE] = explode("|__|", $temp[$key][$valueE]);
							}
						}
					}

					if(isset($module["uploadImg"])){//後台用
						foreach ($module["uploadImg"] as $valueM) {
							if(isset($temp[$key][$valueM["name"]])){
								$temp[$key][$valueM["name"]] = explode("|__|", $temp[$key][$valueM["name"]]);
							}

							if(isset($valueM["textOther"])){
								foreach ($valueM["textOther"] as $valueM1) {
									if(isset($temp[$key][$valueM["name"].$valueM1])){
										$temp[$key][$valueM["name"].$valueM1] = json_encode(explode("|__|", $temp[$key][$valueM["name"].$valueM1]));
									}
								}
							}
							if(isset($valueM["textareaOther"])){
								foreach ($valueM["textareaOther"] as $valueM1) {
									if(isset($temp[$key][$valueM["name"].$valueM1])){
										$temp[$key][$valueM["name"].$valueM1] = json_encode(explode("|__|", $temp[$key][$valueM["name"].$valueM1]));
									}
								}
							}
						}
					}

					if(isset($module["uploadFile"])){//後台用
						foreach ($module["uploadFile"] as $valueF) {
							if(isset($temp[$key][$valueF["name"]])){
								$temp[$key][$valueF["name"]] = explode("|__|", $temp[$key][$valueF["name"]]);
							}

							if(isset($valueF["textOther"])){
								foreach ($valueF["textOther"] as $valueF1) {
									if(isset($temp[$key][$valueF["name"].$valueF1])){
										$temp[$key][$valueF["name"].$valueF1] = json_encode(explode("|__|", $temp[$key][$valueF["name"].$valueF1]));
									}
								}
							}
							if(isset($valueF["textareaOther"])){
								foreach ($valueF["textareaOther"] as $valueF1) {
									if(isset($temp[$key][$valueF["name"].$valueF1])){
										$temp[$key][$valueF["name"].$valueF1] = json_encode(explode("|__|", $temp[$key][$valueF["name"].$valueF1]));
									}
								}
							}
						}
					}
				}
				return $temp;
			}else{
				$this->message = $this->console->getMessage('DATA_NULL');
				return false;
			}
		}

		/**
		 * 前台取得資料
		 * @return [type] [description]
		 */
		function getOne($whereSql="",$sqlArray=array(),$explodeArray=array(),$module=array()){
			$temp = $this->getData("where status='1' ".$whereSql,$sqlArray,$explodeArray,$module);
			if($temp){
				foreach ($temp as $key => $value) {
					$temp[$key] = array_map(function($v){
						if(!is_array($v)){
							return htmlspecialchars_decode($v);
						}
						return $v;
					},$temp[$key]);
				}
				return $temp[0];
			}
			return false;
		}


		/**
		 * 刪除
		 * @param  [type] $id [description]
		 * @return [type]     [description]
		 */
		function rmData($id){
			$oldData = array();
			if(is_array($id)){
				foreach ($id as $key => $value) {
					$oldData[] = $this->getData("where id='".$value."'")[0];
				}
				$deleteID = implode("','",$id);
			}else{
				$deleteID = $id;
				$oldData[] = $this->getData("where id='".$id."'")[0];
			}
			
			if($this->conn->Execute("delete from ".$this->table." where id in ('".$deleteID."')")){
				$this->conn->Execute("delete from ".$this->tableSort." where dataID in (".$deleteID.")");
				if(!$this->isTree) $this->sortTable();
				$this->message = $this->console->getMessage('DELETE_OK');
				foreach ($oldData as $key => $value) {
					$this->systemLog->addLog("DELETE",$this->table,$value,array());
				}
				return true;
			}else{
				$this->message = $this->console->getMessage('DELETE_ERROR');
				error_log($this->message.$this->conn->errorMsg());
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
					if($value['Field'] != 'create_date' && $value['Field'] != 'update_date'){
						$allKey .= $value['Field'].',';
					}
				}
				$allKey = substr($allKey,0,-1);

				//新table name
				$newTable = explode('__', $this->table)[0].'__'.str_replace("-","_",$lang);

				//複製結構到新table
				if($this->conn->GetArray("desc ".$newTable)){
					$temp = $this->conn->GetArray("desc ".$this->table);
					foreach ($temp as $key => $value) {
						$this->autoAddKey(array($value["Field"] => ''),$newTable);
					}
				}else{
					$this->conn->Execute("create table ".$newTable." LIKE ".$this->table.";");
				}
				
				//複製資料到新table
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
		 * 自動生成欄位
		 * @param  [type] $data  array(key=>value,key1=>value1)
		 * @param  [type] $table 資料表
		 * @return [type]        [description]
		 */
		function autoAddKey($data,$table=''){
			if($table==''){
				$table = $this->table;
			}
			foreach ($data as $k=>$v){
				if(in_array($k,$this->keyException) || is_numeric($k)) {
					continue;
				}
				$this->conn->Execute("ALTER TABLE ".$table." ADD ".$k." TEXT NULL COMMENT 'ATOU'");
			}
		}

		/**
		 * 排序資料表
		 */
		function sortTable(){
			if(!in_array("sort", $this->getField()) || $this->isTree){
				return false;
			}
			if(isset($_GET["class"]) && is_numeric($_GET["class"]) && $_GET["class"]){
				$this->conn->Execute("set @j:=0;");
				$this->conn->Execute("update ".$this->tableSort." set sort=@j:=@j+1 where classID='".$_GET["class"]."' order by sort");
			}
			$this->conn->Execute("set @k:=0;");
			$this->conn->Execute("update ".$this->table." set sort=@k:=@k+1 order by sort");
		}

		/**
		 * 增加圖片name
		 */
		function addPictureName($data){
			$this->pictureName[] = $data;
		}

		/**
		 * 將不合法的欄位清除
		 * @param  array  $data  處理的資料
		 * @param  array  $array 合法的key
		 * @return [type]        處理後的資料
		 */
		function checkData($data,$array){
			if($array && is_array($array)){
				foreach ($data as $key => $value) {
					if(!in_array($key,$array)){
						unset($data[$key]);
					}
				}
			}
			return $data;
		}

		/**
		 * 檢查必填欄位是否有值
		 * @param  array  $data  資料
		 * @param  array  $array 要必填的key
		 * @return [type]        失敗的欄位
		 */
		function requiredData($data,$array){
			$temp = array();
			if(is_array($array)){
				foreach ($array as $key => $value) {
					if(!isset($data[$value]) || (""==$data[$value])){
						array_push($temp,$value);
					}
				}
			}
			return $temp;
		}

		/**
		 * 製造where
		 * @return [type] [description]
		 */
		function makeWhereSql($data,$isPrepare=false){
			$sql = " where ";
			foreach ($data as $key => $value) {
				if($value===NULL){
					$sql .= $key." IS NULL and ";
				}else{
					if($isPrepare){
						$sql .= $key."=? and ";
					}else{
						$sql .= $key."='".$value."' and ";
					}
				}
			}
			$sql .=" 1=1 ";
			return $sql;
		}


		/**
		 * 分類排序資料表
		 * @return [type] [description]
		 */
		public function checkSortTable(){
			if(!in_array("sort", $this->getField()) || !in_array("class", $this->getField()) || $this->isTree){
				return false;
			}
			$temp = $this->conn->GetArray("desc ".$this->tableSort);
			if(!$temp){
				$this->conn->Execute("
					CREATE TABLE `".$this->tableSort."` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `classID` int(11) NOT NULL COMMENT '分類ID',
					  `dataID` int(11) NOT NULL COMMENT '資料ID',
					  `sort` int(11) NOT NULL COMMENT '排序',
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;
				");
			}
		}

		/**
		 * 重新排列資料 usort($temp, array($this, 'classSort'));
		 * @param  [type] $a [description]
		 * @param  [type] $b [description]
		 * @return [type]    [description]
		 */
		function classSort($a,$b){
            if($a['sort'] == $b['sort']) return 0;
            return ($a['sort'] > $b['sort']) ? 1 : -1;
		}

		/**
		 * 合成陣列字串搜尋sql
		 * @param  [type] $row [description]
		 * @param  [type] $val [description]
		 * @return [type]      [description]
		 */
		function findArrayString($row,$val){
			if(is_array($val)){
				$temp = " and (";
				foreach ($val as $key => $value) {
					$temp .= " ".$row." like '%|".$value."|%' or ".$row." like '%|".$value."' or ".$row." like '".$value."|%' or ".$row."='".$value."' or";
				}
				$temp .= " 0)";
				return $temp;
			}
			return " and (".$row." like '%|".$val."|%' or ".$row." like '%|".$val."' or ".$row." like '".$val."|%' or ".$row."='".$val."')";
		}

		/**
		 * 取得更新者帳號
		 * @return [type] [description]
		 */
		function getUpdateUser(){
			$user = '__AUTO__';
			if (isset($_SESSION[FRAME_NAME]["member"])) {
				if($this->console->langSessionName=="Serback" && isset($_SESSION[FRAME_NAME]["member"]["serback"]["account"])){
					$user = $_SESSION[FRAME_NAME]["member"]["serback"]["account"];
				}else if($this->console->langSessionName=="" && isset($_SESSION[FRAME_NAME]["member"]["member"]["account"])){
					$user = $_SESSION[FRAME_NAME]["member"]["member"]["account"];
				}else{
					foreach ($_SESSION[FRAME_NAME]["member"] as $key => $value) {
						$user = $value["account"];
						break;
					}
				}
			}
			return $user;
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
