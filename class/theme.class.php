<?php


/**
 * 主題包
 * MTsung by 20190701
 */
namespace MTsung{

	class theme{
		var $conn;
		var $dirPath = APP_PATH."theme/";//資料夾
		
		function __construct($conn){
			$this->conn = $conn;
			if(!is_dir($this->dirPath)){
				mkdir($this->dirPath);
			}
		}

		/**
		 * 使用主題包
		 * @param  [type] $id 		使用的id
		 * @return [type]           [description]
		 */
		function install($id){
			ignore_user_abort(true);
			set_time_limit(0);
			ini_set("memory_limit",-1);
			$this->conn->Execute("set global max_allowed_packet=200*1024*1024; ");
			$fileName = $this->dirPath.$id."/theme.sql";
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

				//切版複製
				$this->delTree(DATA_PATH."view"); // 刪除原本的切版
				$this->recurse_copy($this->dirPath.$id,DATA_PATH);

				return true;
			}else{
				return false;
			}
		}

		/**
		 * 建立主題包
		 * @param  [type] $themeName 主題包名稱
		 * @param  [type] $picture   主題包縮圖
		 * @return [type]            [description]
		 */
		function export($themeName,$picture='',$memo=''){
			$id = $this->getMaxId()+1;
			$exportPath = $this->dirPath.$id."/";
			mkdir($exportPath);

			//menu匯出
			ignore_user_abort(true);
			set_time_limit(0);
			ini_set("memory_limit",-1);
			$this->conn->SetFetchMode(ADODB_FETCH_ASSOC);
			$sql = "-- 產生時間： ".date('Y 年 m 月 d 日 H:i')." 網站host ".$_SERVER["HTTP_HOST"]."\r\n\r\n";
			$temp = $this->conn->GetArray("show tables");
			foreach ($temp as $key => $value) {
				$tableName = array_shift($value);
				if(!in_array($tableName, array(PREFIX."menu",PREFIX."template"))){
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

			$fp = fopen($exportPath."theme.sql",'w');
			fwrite($fp,$sql);
			fclose($fp);

			$this->conn->SetFetchMode(ADODB_FETCH_DEFAULT);

			file_put_contents($exportPath."setting.ini","name=\"".$themeName."\"\npicture=\"".$picture."\"\nmemo=\"".$memo."\"");

			//切版複製
			$filePath = opendir(DATA_PATH);
			$noCopyPath = array(".","..","output","upload");
			$file = [];
			while (($fileName = readdir($filePath)) !== false) {
			    if(!in_array($fileName, $noCopyPath) && is_dir(DATA_PATH.$fileName)){
			    	$file[] = $fileName;
			    }
			}
			foreach ($file as $value) {
				$this->recurse_copy(DATA_PATH.$value,$exportPath.$value);
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
			            $this->recurse_copy($src . '/' . $file,$dst . '/' . $file);
			        }
			        else {
			            copy($src . '/' . $file,$dst . '/' . $file);
			        }
			    }
			}
			closedir($dir);
		}

		/**
		 * 資料夾刪除
		 * @param  [type] $dir [description]
		 * @return [type]      [description]
		 */
		function delTree($dir) {
		    $files = array_diff(scandir($dir), array('.','..'));
			foreach ($files as $file) {
			    (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
			}
			return rmdir($dir);
		}

		/**
		 * 取得列表
		 * @return [type] [description]
		 */
		function getListTheme(){
			$temp = array();
			if ($listFile = scandir($this->dirPath)){
				foreach ($listFile as $key => $value){
					if (is_dir($this->dirPath.$value) && is_numeric($value)){
						$setting = @parse_ini_file($this->dirPath.$value."/setting.ini");
						$temp[] = array(
							'id' => $value,
							'name' => $setting["name"],
							'picture' => $setting["picture"],
							'memo' => $setting["memo"]
						);
					}
				}
			}
			return $temp;
		}

		/**
		 * 取得最大id
		 * @return [type] [description]
		 */
		function getMaxId(){
			$max = 10000;
			if ($listFile = scandir($this->dirPath)){
				foreach ($listFile as $key => $value){
					if (is_dir($this->dirPath.$value) && is_numeric($value)){
						if($value > $max){
							$max = $value;
						}
					}
				}
			}
			return $max;
		}

		/**
		 * 修改主題設定檔
		 * @param  [type] $id        [description]
		 * @param  [type] $themeName [description]
		 * @param  string $picture   [description]
		 * @return [type]            [description]
		 */
		function editTheme($id,$themeName,$picture='',$memo=''){
			file_put_contents($dirPath.$id."/setting.ini","name=\"".$themeName."\"\npicture=\"".$picture."\"\nmemo=\"".$memo."\"");
		}

	}
}