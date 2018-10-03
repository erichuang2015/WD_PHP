<?php


/**
 * 表單
 * MTsung by 
 */
namespace MTsung{

	class form extends center{

		function __construct($console,$table,$lang=LANG){
			parent::__construct($console,$table,$lang);
			$this->checkTable();
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
					  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
					  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
					  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最後修改時間',
					  `create_user` varchar(20) NOT NULL COMMENT '創建人',
					  `update_user` varchar(20) NOT NULL COMMENT '最後修改人',
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;
				");
			}
		}


		/**
		 * 寄送郵件
		 * @param  [type] $data [description]
		 * @return [type]       [description]
		 */
		function sendForm($data,$back){
			$mail = new phpMailer($this->console);
			$data["webUrl"] = $this->console->MT_web['http_path'];
			$data["webName"] = $this->console->webSetting->getValue("webTitle");

			foreach ($_FILES as $key => $value) {
				$mail->setMailFile($value["tmp_name"],$value["name"]);
			}
			$mail->setMailTitle($this->console->getMessage('CONTACT_FORM_MEIL'));
			$mail->setMailAddress($this->console->setting->getValue("recipientEmail"));			
			$mail->setMailBody("mail_forms-notice.html",array('data' => $data));
			return $mail->sendMail($back,'MAIL_SEND_OK');
		}
	}
}
