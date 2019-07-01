<?php


/**
 * 主題包
 * MTsung by 20190701
 */
namespace MTsung{

	class theme{
		var $conn;
		
		function __construct($conn){
			$this->conn = $conn;
			if(!is_dir(APP_PATH.'theme/')){
				mkdir(APP_PATH.'theme/');
			}
		}

		/**
		 * 使用主題包
		 * @param  [type] $fileName [description]
		 * @return [type]           [description]
		 */
		function install($fileName){
			ignore_user_abort(true);
			set_time_limit(0);
			ini_set("memory_limit",-1);
			$this->conn->Execute("set global max_allowed_packet=200*1024*1024; ");
			$fileName = APP_PATH.'theme/'.$fileName;
			if(is_file($fileName)){
				$file = file($fileName);
				$sql = "";
				foreach ($file as $value) {
					$value = str_replace("\n","", str_replace("\r","",$value));
					if(substr($value, 0, 2) == '--' || $value == ''){
						continue;
					}
					$sql .= $value;
					if($value && $value[strlen($value)-1] == ";"){
						$this->conn->Execute($sql);
						$sql = "";
					}
				}
				return true;
			}else{
				return false;
			}
		}

		/**
		 * 建立主題包
		 * @return [type]            [description]
		 */
		function export(){

			//menu匯出
			ignore_user_abort(true);
			set_time_limit(0);
			ini_set("memory_limit",-1);
			$this->conn->SetFetchMode(ADODB_FETCH_ASSOC);
			$sql = "-- 產生時間： ".date('Y 年 m 月 d 日 H:i')." 網站host ".$_SERVER["HTTP_HOST"]."\r\n\r\n";
			$temp = $this->conn->GetArray("show tables");
			foreach ($temp as $key => $value) {
				$tableName = array_shift($value);
				if($tableName != PREFIX."menu"){
					continue;
				}
				$sql .= "DROP TABLE IF EXISTS `".$tableName."`;\r\n\r\n";
				$sql .= $this->conn->GetRow("show create table ".$tableName)["Create Table"].";\r\n\r\n\r\n";
				$data = $this->conn->GetArray("select * from ".$tableName);
				if($data){
					$tempI = 0;
					foreach ($data as $key1 => $value1) {
						$keys = implode("`,`", array_map('addslashes', array_keys($value1)));
						if($tempI==0){
							$sql .= "INSERT INTO `".$tableName."` (`".$keys."`) VALUES\r\n";
						}
						
						if(in_array(NULL, $value1)){
							$nullKey = array_keys($value1,NULL,true);
							if(is_array($nullKey)){
								foreach ($nullKey as $k => $v) {
									$value1[$v] = "|MTsung|NULL|MTsung|";
								}
							}
						}

						$value1 = array_map(function($v){
								return str_replace("\r","\\r",str_replace("\n","\\n",addslashes($v)));
							}, array_values($value1));
						$values = implode("','", $value1);
						if($tempI == count($data)-1){
							$sql .= "('".$values."');\r\n\r\n";
						}else if($tempI % 10000 == 9999){//每1萬筆重新一個insert指令
							$sql .= "('".$values."');\r\n\r\nINSERT INTO `".$tableName."` (`".$keys."`) VALUES\r\n";
						}else{
							$sql .= "('".$values."'),\r\n";
						}
						$tempI++;
					}
				}

			}
			$sql = str_replace("'|MTsung|NULL|MTsung|'","NULL",$sql);


			$fileName = APP_PATH.'theme/10002/menu.sql';
			$fp = fopen($fileName,'w');
			fwrite($fp,$sql);
			fclose($fp);

			$this->conn->SetFetchMode(ADODB_FETCH_DEFAULT);

			//切版複製
			$filePath = opendir(DATA_PATH);
			$noCopyPath = array(".","..","output","upload");
			$file = [];
			while (($fileName = readdir($filePath)) !== false) {
			    if(!in_array($fileName, $noCopyPath) && is_dir(DATA_PATH.$fileName)){
			    	$file[] = $fileName;
			    }
			}
			mkdir(APP_PATH."theme/10002/");
			foreach ($file as $value) {
				$this->recurse_copy(DATA_PATH.$value,APP_PATH."theme/10002/".$value);
			}

		}

		/**
		 * 資料夾複製
		 * @param  [type] $src [description]
		 * @param  [type] $dst [description]
		 * @return [type]      [description]
		 */
		function recurse_copy($src,$dst) {
			$dir = opendir($src);
			@mkdir($dst);
			while(false !== ( $file = readdir($dir)) ) {
			    if (( $file != '.' ) && ( $file != '..' )) {
			        if ( is_dir($src . '/' . $file) ) {
			            recurse_copy($src . '/' . $file,$dst . '/' . $file);
			        }
			        else {
			            copy($src . '/' . $file,$dst . '/' . $file);
			        }
			    }
			}
			closedir($dir);
		}

	}
}