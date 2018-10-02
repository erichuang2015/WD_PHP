<?


/**
 * 備份資料庫
 * MTsung by 20180630
 */
namespace MTsung{

	class backup{
		var $conn;
		
		function __construct($conn){
			$this->conn = $conn;
		}

		/**
		 * 匯入資料庫
		 * @param  [type] $fileName [description]
		 * @return [type]           [description]
		 */
		function importDatabase($fileName){
			ignore_user_abort(true);
			set_time_limit(0);
			ini_set("memory_limit",-1);
			$this->conn->Execute("set global max_allowed_packet=200*1024*1024; ");
			$fileName = APP_PATH.'output/databaseBackup/'.$fileName;
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
		 * 匯出資料庫
		 * @param  integer $sendMail 是否寄送mail
		 * @return [type]            [description]
		 */
		function exportDatabase($sendMail=1){
			ignore_user_abort(true);
			set_time_limit(0);
			ini_set("memory_limit",-1);
			$this->conn->SetFetchMode(ADODB_FETCH_ASSOC);
			$sql = "-- 產生時間： ".date('Y 年 m 月 d 日 H:i')." 網站host ".$_SERVER["HTTP_HOST"]."\r\n\r\n";
			$temp = $this->conn->GetArray("show tables");
			foreach ($temp as $key => $value) {
				$tableName = array_shift($value);
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

			if(!is_dir(APP_PATH.'output')){
				mkdir(APP_PATH.'output');
			}
			if(!is_dir(APP_PATH.'output/databaseBackup')){
				mkdir(APP_PATH.'output/databaseBackup');
			}

			$fileName = APP_PATH.'output/databaseBackup/'.date('Y_m_d_H_i_s').".sql";
			$fp = fopen($fileName,'w');
			fwrite($fp,$sql);
			fclose($fp);

			global $console;
			if(!isset($console)){
				global $setting;
				include_once(APP_PATH.'class/design.class.php');				//樣板模組
				include_once(APP_PATH.'class/PHPMailer.class.php');				//郵件模組
				include_once(APP_PATH.'class/validation.class.php');			//表單驗證
				include_once(APP_PATH.'class/webSetting.class.php');			//網站設定
				include_once(APP_PATH.'include/main.php');						//核心
				$design = new design();
				$console = new main($this->conn,$design,$setting);
				$webSetting = new webSetting($console,PREFIX."web_setting",$_SESSION[FRAME_NAME]['language'.$console->langSessionName]);//前端輸出用
				$console->setWebSetting($webSetting);
			}
			//寄信備份
			if(isset($console) && $console->setting->getValue("backupMailSwitch") && $sendMail){
				$mail = new phpMailer($console);
				$mail->setMailTitle("[".$_SERVER["HTTP_HOST"]."] ".date('Y_m_d_H_i_s').' 網站備份');
				$mail->setMailFrom($console->setting->getValue("senderEmail"),$console->setting->getValue("senderName"));
				$mail->setMailAddress($console->setting->getValue("backupMailUser"));
				$mail->setMailBody(mailTemplate::BACKUP);
				$mail->setMailFile($fileName,$console->webSetting->getValue("webTitle")." ".date('Y_m_d_H_i_s').".sql");
				$mail->sendMail('','');
			}

			$this->conn->SetFetchMode(ADODB_FETCH_DEFAULT);
		}
	}
}