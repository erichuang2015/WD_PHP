<?php


/**
 * 會員模組
 * MTsung by 20180527
 */
namespace MTsung{


	class member extends center{
		use userDeviceInfomation;

		var $sessionName;
		var $config;

		function __construct($console,$table,$sessionName='member'){
			parent::__construct($console,$table);
			$this->sessionName = $sessionName;

			$this->checkTable();

			$this->reloadInfo();

			//重設密碼過期時間
			$this->config["time"] = 3600;
			// $this->memberGroup = new memberGroup($console,$table."_group");

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
					  `account` varchar(50) NOT NULL COMMENT '帳號',
					  `password` varchar(191) NOT NULL COMMENT '密碼',
					  `groupID` int(11) DEFAULT NULL COMMENT '群組',
					  `name` varchar(191) DEFAULT NULL COMMENT '姓名',
					  `email` varchar(50) DEFAULT NULL COMMENT '電子郵件',
					  `doingTime` int(11) DEFAULT 3600 COMMENT '閒置登出時間',
					  `sex` varchar(20) DEFAULT NULL COMMENT '1=男 2=女 3=不提供',
					  `point` int(11) DEFAULT 0 COMMENT '紅利點數',
					  `picture` varchar(50) DEFAULT NULL COMMENT '照片',
					  `country` varchar(191) DEFAULT NULL COMMENT '國家',
					  `county` varchar(191) DEFAULT NULL COMMENT '城市',
					  `city` varchar(191) DEFAULT NULL COMMENT '區域',
					  `zipcode` int(10) DEFAULT NULL COMMENT '郵遞區號',
					  `address` text DEFAULT NULL COMMENT '住址',
					  `company` varchar(100) DEFAULT NULL COMMENT '公司名稱',
					  `landline` varchar(100) DEFAULT NULL COMMENT '家用電話',
					  `landline2` varchar(100) DEFAULT NULL COMMENT '家用電話2',
					  `phone` varchar(100) DEFAULT NULL COMMENT '手機號碼',
					  `phone2` varchar(100) DEFAULT NULL COMMENT '手機號碼2',
					  `fax` varchar(100) DEFAULT NULL COMMENT '傳真號碼',
					  `fax2` varchar(100) DEFAULT NULL COMMENT '傳真號碼2',

					  `emailCheck` int(11) DEFAULT '-1' COMMENT '-1=不用驗證 0=未驗證 1=已驗證',
					  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
					  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
					  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最後修改時間',
					  `create_user` varchar(191) NOT NULL COMMENT '創建人',
					  `update_user` varchar(191) NOT NULL COMMENT '最後修改人',
					  `fbID` varchar(100) DEFAULT NULL COMMENT 'fb帳號連結',
					  `fbName` varchar(100) DEFAULT NULL COMMENT 'fb名字',
					  `fbEmail` varchar(100) DEFAULT NULL COMMENT 'fb-email',
					  `fbPicture` text DEFAULT NULL COMMENT 'fb大頭貼',
					  `googleID` varchar(100) DEFAULT NULL COMMENT 'google帳號連結',
					  `googleName` varchar(100) DEFAULT NULL COMMENT 'google名字',
					  `googleEmail` varchar(100) DEFAULT NULL COMMENT 'google-email',
					  `googlePicture` text DEFAULT NULL COMMENT 'google大頭貼',
					  `lineID` varchar(100) DEFAULT NULL COMMENT 'line帳號連結',
					  `lineName` varchar(100) DEFAULT NULL COMMENT 'line名字',
					  `lineEmail` varchar(100) DEFAULT NULL COMMENT 'line-email',
					  `linePicture` text DEFAULT NULL COMMENT 'line大頭貼',
				  	  `memo` text NOT NULL COMMENT '備註',
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `account` (`account`),
					  UNIQUE KEY `fbID` (`fbID`),
					  UNIQUE KEY `googleID` (`googleID`),
					  UNIQUE KEY `lineID` (`lineID`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;
				");
			}
			$temp = $this->conn->GetArray("desc ".$this->table."_logs");
			if(!$temp){
				$this->conn->Execute("
					CREATE TABLE `".$this->table."_logs` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `account` varchar(50) NOT NULL COMMENT '帳號',
					  `type` int(11) NOT NULL COMMENT '0註冊,1登入,2社群登入,3登出,4信件認證,5忘記密碼,6刪除,7帳號錯誤,8密碼錯誤,9驗證碼失敗',
					  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '時間',
					  `IP` varchar(20) NOT NULL COMMENT 'IP',
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;
				");
			}
		}

		/**
		 * 新增使用者
		 * @param [type]  $data          資料
		 * @param boolean $emailCheck    是否需要驗證enail
		 * @param string  $checkArray    合法key，選填
		 * @param string  $requiredArray 必填key，選填
		 * @return [type]          		 失敗 false，成功true，若有必填欄位未填回傳未填的key陣列
		 */
		public function addUser($data,$emailCheck=false,$checkArray='',$requiredArray=array()){
			//預設必填
			array_push($requiredArray,'account','password','checkPassword');
			$requiredArray = array_unique($requiredArray);


			$data = $this->checkData($data,$checkArray);
			if($temp = $this->requiredData($data,$requiredArray)){
				$this->message = $this->console->getMessage('USER_REQURED_NULL');
				return $temp;			
			}

			//email認證
			if(!isset($data["emailCheck"])){
				$data["emailCheck"] = $emailCheck ? emailCheckType::CHECK_NO : emailCheckType::IS_NO_CHECK;
			}

			//密碼
			if($data["password"] == $data["checkPassword"]){
				$data["password"] = password_hash($data["password"], PASSWORD_DEFAULT);
				unset($data["checkPassword"]);
			}else{
				$this->message = $this->console->getMessage('USER_PASSWORD_CHECK_ERROR');
				return false;
			}

			//建立人and修改人
			$data["create_user"] = $data["update_user"] = $data["account"];
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

			//沒姓名時用帳號當性名
			if(!isset($data["name"])){
				$data["name"] = $data["account"];

			}

			if($this->setData($data)){
				$this->addLog($data["account"],logType::JOIN);
				$this->message = $this->console->getMessage('USER_JOIN_OK');
				return true;
			}else{
				$this->message = $this->console->getMessage('USER_REPEAT');
				return false;
			}
		}

		/**
		 * 修改會員資料
		 * @param [type]  $id    		 會員id
		 * @param [type]  $data  		 修改資料
		 * @param string  $checkArray 	 合法的key
		 * @param string  $requiredArray 必填key
		 * @return [type]       		 失敗 false，成功true，若有必填欄位未填回傳未填的key陣列
		 */
		public function setUser($id,$data,$checkArray='',$requiredArray=''){
			$temp = $this->conn->GetRow($this->conn->Prepare("select * from ".$this->table." where id=? "),array($id));
			if($temp){
				unset($data["account"]);//帳號不可修改
				$data = $this->checkData($data,$checkArray);
				if($temp1 = $this->requiredData($data,$requiredArray)){
					$this->message = $this->console->getMessage('USER_REQURED_NULL');
					return $temp1;
				}

				//修改人
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

				//自己不能改變自己狀態/群組
				if($this->getInfo("id") == $id){
					unset($data["status"]);
					unset($data["groupID"]);
				}

				//修改密碼
				if(isset($data["newPassword"]) && isset($data["checkNewPassword"]) && $data["newPassword"]){
					if($data["newPassword"]==$data["checkNewPassword"]){
						$data["password"] = password_hash($data["newPassword"], PASSWORD_DEFAULT);
					}else{
						$this->message = $this->console->getMessage('USER_PASSWORD_CHECK_ERROR');
						return false;
					}
				}
				unset($data["newPassword"],$data["checkNewPassword"]);

				$data["update_date"] = DATE;
				$data["id"] = $id;
				if($this->setData($data)){
					$this->message = $this->console->getMessage('USER_UPDATE_OK');
					return true;
				}else{
					$this->message = $this->console->getMessage('USER_UPDATE_REPEAT');
					return false;
				}
			}else{
				$this->message = $this->console->getMessage('USER_NULL');
				return false;
			}
		}

		/**
		 * 取得會員資料
		 * @param  [type] $id   會員id
		 * @param  string $name 想取得的欄位，不填則全部
		 * @return [type]       回傳資料 OR 失敗===false
		 */
		public function getUser($id,$name=''){
			$temp = $this->conn->GetRow($this->conn->Prepare("select * from ".$this->table." where id=? "),array($id));
			if($temp){
				$temp = array_map("htmlspecialchars",$temp);
				if(strpos($temp["address"],"|__|")!==false){
					$temp["address"] = explode("|__|",$temp["address"]);
				}
				if($name){
					if(is_array($name)){
						$data = array();
						foreach ($temp as $key => $value) {
							if(in_array($key,$name)){
								array_push($data, $value);
							}
						}
						return $data;
					}else{
						return $temp[$name];
					}
				}else{
					return $temp;
				}
			}else{
				$this->message = $this->console->getMessage('USER_NULL');
				return false;
			}
		}

		/**
		 * 刪除會員
		 * @param  [type] $id 會員id
		 * @return [type]     成功OR失敗
		 */
		public function rmUser($id){
			$temp = $this->conn->GetRow($this->conn->Prepare("select * from ".$this->table." where id=? "),array($id));
			if($temp){
				if($this->rmData($id)){
					$this->addLog($temp["account"],logType::DELETE);
					$this->message = $this->console->getMessage('USER_DELETE_OK');
					return true;
				}else{
					$this->message = $this->console->getMessage('USER_DELETE_ERROR');
					return false;
				}
			}else{
				$this->message = $this->console->getMessage('USER_NULL');
				return false;
			}
		}

		/**
		 * 重新讀取資料
		 * @return [type] [description]
		 */
		public function reloadInfo(){
			if (isset($_SESSION[FRAME_NAME]["member"][$this->sessionName]["id"])){
				$temp = $this->conn->GetRow($this->conn->Prepare("select * from ".$this->table." where id=?"),array($_SESSION[FRAME_NAME]["member"][$this->sessionName]["id"]));
				if($temp){
					if($temp["status"]){
						$temp = array_map("htmlspecialchars",$temp);
						$_SESSION[FRAME_NAME]["member"][$this->sessionName] = $temp;
					}else{
						$this->message = $this->console->getMessage('USER_DISABLED');
						$_SESSION[FRAME_NAME]["member"][$this->sessionName] = '';
					}
				}else{
					$_SESSION[FRAME_NAME]["member"][$this->sessionName] = '';
				}
			}
		}

		/**
		 * 社群登入
		 * @param  [type] $type 社群名稱(fb,google,line)
		 * @param  [type] $sub  社群API取得的ID
		 * @return [type]       [description]
		 */
		public function socialLogin($type,$sub){
			$type = strtolower($type);
			$temp = $this->conn->GetRow($this->conn->Prepare("select * from ".$this->table." where ".$type."ID=?"),array($sub));
			//帳號
			if($temp){
				//狀態
				if($temp["status"]){
					//認證未通過
					if($temp["emailCheck"]!=emailCheckType::CHECK_NO){
						$temp = array_map("htmlspecialchars",$temp);
						$_SESSION[FRAME_NAME]["member"][$this->sessionName] = $temp;
						$this->addLog($temp["account"],logType::SOCIAL_LOGIN);
						return true;
					}else{
						$this->message = $this->console->getMessage('USER_EMAIL_CHECK_ERROR');
						return $temp["id"];
					}
				}else{
					$this->message = $this->console->getMessage('USER_DISABLED');
				}
			}else{
				$this->message = $this->console->getMessage('SOCIAL_LINK_NULL');
			}
			$this->logout();
			return false;
		}

		/**
		 * 連結社群帳號
		 * @param  [type] $id   	會員
		 * @param  [type] $type 	社群名稱(fb,google,line)
		 * @param  [type] $data  	社群取得的data(id,name,email,picture)
		 * @return [type]       	成功or失敗
		 */
		public function link($id,$type,$data){
			$type = strtolower($type);
			$temp = $this->conn->GetRow($this->conn->Prepare("select * from ".$this->table." where id=? "),array($id));
			$temp1 = $this->conn->GetRow($this->conn->Prepare("select * from ".$this->table." where ".$type."ID=? "),array($data["id"]));
			//是否有會員
			if($temp){
				//社群帳號是否綁定過
				if(!$temp1){
					//帳號是否綁定過
					if(!$temp[$type."ID"]){
						/**
						 * 原本API給的解析度太小
						 */
						if($type == 'google'){
							$data["picture"] = str_replace("/s96","/s300",$data["picture"]);
						}else if($type == 'fb'){
							$data["picture"] = 'https://graph.facebook.com/'.$data["id"].'/picture?type=large';
						}


						$data = array(	$type."ID" => $data["id"],
										$type."Name" => $data["name"],
										$type."Email" => $data["email"],
										$type."Picture" => $data["picture"],
										"id" => $id
									);
						if($this->setData($data)){
							$this->message = $this->console->getMessage('USER_LINK_OK');
							return true;
						}else{
							$this->message = $this->console->getMessage('USER_LINK_ERROR');
						}
					}else{
						$this->message = $this->console->getMessage('USER_LINK_REPEAT');
					}
				}else{
					$this->message = $this->console->getMessage('SOCIAL_REPEAT');
				}
			}else{
				$this->message = $this->console->getMessage('USER_NULL');
			}
			return false;
		

		}

		/**
		 * 取消社群帳號連結
		 * @param  [type] $id   會員id
		 * @param  [type] $type 要取消的社群名稱
		 * @return [type]       成功or失敗
		 */
		public function unLink($id,$type){
			$type = strtolower($type);
			$temp = $this->conn->GetRow($this->conn->Prepare("select * from ".$this->table." where id=? "),array($id));
			if($temp){
				$data = array(	$type."ID" => NULL,
								$type."Name" => '',
								$type."Email" => '',
								$type."Picture" => '',
								"id" => $id
							);
				if($this->setData($data)){
					$this->message = $this->console->getMessage('USER_UNLINK_OK');
					return true;
				}else{
					$this->message = $this->console->getMessage('USER_UNLINK_ERROR').$this->conn->errorMsg();
				}
			}else{
				$this->message = $this->console->getMessage('USER_NULL');
			}
			return false;
		}

		/**
		 * 寄送email認證信
		 * @param  [type] $id   會員id
		 * @param  string $back 成功時導向哪頁
		 * @return [type]       失敗為false
		 */
		public function checkEmail($id,$back='NO'){
			$temp = $this->conn->GetRow($this->conn->Prepare("select * from ".$this->table." where id=? "),array($id));
			if($temp){
				if($temp["emailCheck"]==emailCheckType::CHECK_NO){
					$auth = urlencode(base64_encode(base64_encode($temp["password"])));
					$url = $this->console->MT_web['http_path'].'member/check?uID='.$temp["id"].'&auth='.$auth;

					$data["mailTitle"] = $this->console->webSetting->getValue("webTitle").'-'.$this->console->getMessage('TITLE_USER_LETTER');
					$data["webUrl"] = $this->console->MT_web['http_path'];
					$data["webName"] = $this->console->webSetting->getValue("webTitle");
					$data["checkUrl"] = $url;
					$data["webEmail"] = $this->console->setting->getValue("senderEmail");

					$mail = new phpMailer($this->console);
					$mail->setMailTitle($this->console->getMessage('TITLE_USER_LETTER'));
					$mail->setMailAddress($temp["email"]);
					$mail->setMailBody(mailTemplate::MAIL_MEMBER_NOTICE,array('data' => $data ,'member' => $temp));
					return $mail->sendMail($back,'JOIN_MAIL_SEND_OK');
				}
			}else{
				$this->message = $this->console->getMessage('USER_NULL');
				return false;
			}
		}

		/**
		 * 會員認證
		 * @param  [type] $id   $_GET['uID']
		 * @param  [type] $auth $_GET['auth']
		 * @return [type]       成功(順便登入)OR失敗
		 */
		public function reciveEmail($id,$auth){
			$auth = base64_decode(base64_decode($auth));

			$temp = $this->conn->GetRow($this->conn->Prepare("select * from ".$this->table." where id=? and password=?"),array($id,$auth));
			if ($temp){
				if($temp["emailCheck"]==emailCheckType::CHECK_NO){
					$this->conn->AutoExecute($this->table,array('emailCheck' => emailCheckType::CHECK_OK),"UPDATE",'id='.$id);
					$this->message = $this->console->getMessage('USER_LETTER_OK');
					$temp = array_map("htmlspecialchars",$temp);
					$_SESSION[FRAME_NAME]["member"][$this->sessionName] = $temp;
					$this->addLog($temp["account"],logType::CHECK_EMAIL);
					return true;
				}else{
					$this->message = $this->console->getMessage('USER_LETTER_REPEAT');
				}
			}else{
				$this->message = $this->console->getMessage('USER_LETTER_ERROR');
			
			}
			return false;
		}

		/**
		 * 登入狀態
		 * @return boolean [description]
		 */
		public function isLogin(){
			if(isset($_SESSION[FRAME_NAME]["member"][$this->sessionName])&&$_SESSION[FRAME_NAME]["member"][$this->sessionName]){
				$doingTime = (int)$_SESSION[FRAME_NAME]["member"][$this->sessionName]["doingTime"];
				if(isset($_SESSION[FRAME_NAME]["DOING_TIME"][$this->sessionName]) && ($doingTime > 0) && (strtotime(DATE) - strtotime($_SESSION[FRAME_NAME]["DOING_TIME"][$this->sessionName]) > $doingTime)){
					$this->message = $this->console->getMessage('IDLE_LOGOUT');
					return false;
				}
				return true;
			}
			return false;
		}

		/**
		 * 取得會員資料
		 * @param  string $value 欄位
		 * @return [type]        [description]
		 */
		public function getInfo($value=''){
			if($this->isLogin()){
				if($value){
					if(isset($_SESSION[FRAME_NAME]["member"][$this->sessionName][$value])){
						return $_SESSION[FRAME_NAME]["member"][$this->sessionName][$value];
					}
					$this->message = $this->console->getMessage("MEMBER_INFO_NULL");
					return false;
				}else{
					return $_SESSION[FRAME_NAME]["member"][$this->sessionName];
				}
			}else{
				return false;
			}
		}

		/**
		 * 登入
		 * @param  [type] $account  帳號
		 * @param  [type] $password 密碼
		 * @return [type]           失敗return false，成功true，未通過驗證 回傳id
		 */
		public function login($account,$password){
			$temp = $this->conn->GetRow($this->conn->Prepare("select * from ".$this->table." where account=?"),array($account));
			//帳號
			if($temp){
				//密碼
				if(password_verify($password, $temp["password"])){
					//狀態
					if($temp["status"]){
						//認證未通過
						if($temp["emailCheck"]!=emailCheckType::CHECK_NO){
							$temp = array_map("htmlspecialchars",$temp);
							$_SESSION[FRAME_NAME]["member"][$this->sessionName] = $temp;
							$this->addLog($temp["account"],logType::LOGIN);
							return true;
						}else{
							$this->message = $this->console->getMessage('USER_EMAIL_CHECK_ERROR');
							return $temp["id"];
						}
					}else{
						$this->message = $this->console->getMessage('USER_DISABLED');
					}
				}else{
					$this->addLog($account,logType::PASSWORD_ERROR);
					$this->message = $this->console->getMessage('INFO_INSERT_ERROR');
				}
			}else{
				$this->addLog($account,logType::ACCOUNT_ERROR);
				$this->message = $this->console->getMessage('INFO_INSERT_ERROR');
			}
			return false;
		}

		/**
		 * 登出
		 * @return [type] [description]
		 */
		public function logout(){
			if(isset($_SESSION[FRAME_NAME]["member"][$this->sessionName]["account"])){
				$this->addLog($_SESSION[FRAME_NAME]["member"][$this->sessionName]["account"],logType::LOGOUT);
				unset($_SESSION[FRAME_NAME]["member"][$this->sessionName]);
				unset($_SESSION[FRAME_NAME]["DOING_TIME"][$this->sessionName]);
			}
		}

		/**
		 * 忘記密碼認證信發送
		 * @param  [type] $key 		[description]
		 * @param  [type] $value 	[description]
		 * @return [type]       	[description]
		 */
		public function resetPassword($key,$value,$url='member/forget',$back='NO'){
			$temp = $this->conn->GetRow($this->conn->Prepare("select * from ".$this->table." where ".$key."=? "),array($value));
			if($temp){
				$auth = urlencode(base64_encode(base64_encode($temp["password"])));
				$url = $this->console->MT_web['http_path'].$url.'?uID='.$temp["id"].'&auth='.$auth;

				$this->conn->AutoExecute($this->table,array('update_date' => DATE),"UPDATE",'id='.$temp["id"]);

				$data["mailTitle"] = $this->console->webSetting->getValue("webTitle").'-'.$this->console->getMessage('TITLE_USER_RESET_PASSWORD');
				$data["webUrl"] = $this->console->MT_web['http_path'];
				$data["webName"] = $this->console->webSetting->getValue("webTitle");
				$data["checkUrl"] = $url;
				$data["webEmail"] = $this->console->setting->getValue("senderEmail");

				$mail = new phpMailer($this->console);
				$mail->setMailTitle($this->console->getMessage('TITLE_USER_RESET_PASSWORD'));
				$mail->setMailAddress($temp["email"]);
				$mail->setMailBody(mailTemplate::MAIL_PASSWORD_NOTICE,array('data' => $data ,'member' => $temp));
				return $mail->sendMail($back,'RESET_PASSWORD_MAIL_SEND_OK');
				
			}else{
				$this->message = $this->console->getMessage('USER_NULL');
				return false;
			}
		}

		/**
		 * 忘記密碼認證信認證
		 * @param  [type] $id   $_GET['uID']
		 * @param  [type] $auth $_GET['auth']
		 * @return [type]       成功OR失敗
		 */
		public function recivePassword($id,$auth){
			$auth = base64_decode(base64_decode($auth));

			$temp = $this->conn->GetRow($this->conn->Prepare("select * from ".$this->table." where id=? and password=?"),array($id,$auth));
			if ($temp){
				if($this->config["time"]>0 && (strtotime(DATE)-strtotime($temp["update_date"])>$this->config["time"])){
					$this->message = $this->console->getMessage('RESET_PASSWORD_EXPIRED');
					return false;
				}
				$this->addLog($temp["account"],logType::FORGET);
				return true;
			}else{
				$this->message = $this->console->getMessage('USER_RESET_PASSWORD_ERROR');
				return false;
			}
		}

		/**
		 * 新增Log
		 * @param [type] $account 帳號
		 * @param [type] $type    0註冊,1登入,2社群登入,3登出,4信件認證,5忘記密碼
		 */
		public function addLog($account,$type){
			// echo $type;
			$data = array(	"account" => $account,
							"type" => $type,
							"IP" => $this->getIP()
						);
			$this->conn->AutoExecute($this->table."_logs",$data,"INSERT");
		}

		/**
		 * 啟動/停止email驗證
		 * @param [type] $flag true false
		 */
		public function emailCheck($flag){
			$temp = $flag? emailCheckType::CHECK_NO : emailCheckType::IS_NO_CHECK;
			$temp1 = $flag? emailCheckType::IS_NO_CHECK : emailCheckType::CHECK_NO;
			//已驗證過的不影響
			$this->conn->Execute("update ".$this->table." set emailCheck='".$temp."' where emailCheck='".$temp1."'");
		}

		/**
		 * 紅利點數操作
		 * @param  [type] $mount  位移
		 * @param  [type] $type   動作
		 * @param  [type] $id     會員id
		 */
		public function setPoint($mount,$type,$id=0){
			if(!$mount || !is_numeric($mount) || (!$this->isLogin() && $id==0)) return false;

			$temp = $this->conn->GetArray("desc ".$this->table."_point_logs");
			if(!$temp){
				$this->conn->Execute("
					CREATE TABLE `".$this->table."_point_logs` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `memberId` varchar(50) NOT NULL COMMENT '會員id',
					  `account` varchar(50) NOT NULL COMMENT '帳號',
					  `type` int(11) NOT NULL COMMENT '0取得,1使用,2回收,3補發',
					  `beforePoint` int(11) NOT NULL COMMENT '操作前點數',
					  `afterPoint` int(11) NOT NULL COMMENT '操作後點數',
					  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '時間',
					  `IP` varchar(20) NOT NULL COMMENT 'IP',
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;
				");
			}

			if(is_numeric($id) && $id!=0){
				$temp = $this->getUser($id);
				$data = array(
								"memberId" => $temp["id"],
								"account" => $temp["account"],
								"type" => $type,
								"beforePoint" => (int)$temp["point"],
								"afterPoint" => (int)$temp["point"] + $mount,
								"IP" => $this->getIP()
							);
			}else{
				$data = array(	
								"memberId" => $this->getInfo('id'),
								"account" => $this->getInfo('account'),
								"type" => $type,
								"beforePoint" => (int)$this->getInfo('point'),
								"afterPoint" => (int)$this->getInfo('point') + $mount,
								"IP" => $this->getIP()
							);
			}
			if($this->setUser($data["memberId"],array("point"=>$data["afterPoint"]))){
				$this->conn->AutoExecute($this->table."_point_logs",$data,"INSERT");
				return true;
			}
			return false;
		}
	}
}