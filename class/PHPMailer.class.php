<?php


/**
 * 郵件模組
 * MTsung by 20180522
 */	
namespace MTsung{
	
	include_once(APP_PATH.'include/PHPMailer/PHPMailerAutoload.php');

	class phpMailer extends \PHPMailer{
		var $console;

		/**
		 * PHPMailer設定
		 * @param [type] $console [description]
		 */
		function __construct($console){
			$this->setConsole($console);
			// $this->SMTPDebug = 4;
			$this->IsSMTP(); 
			$this->SMTPAuth = true;
			$this->CharSet = "utf-8"; 
			$this->SMTPOptions = array(
			    'ssl' => array(
			        'verify_peer' => false,
			        'verify_peer_name' => false,
			        'allow_self_signed' => true
			    )
			);

			$setData = array(
				'SMTPSecure' => $console->setting->getValue("smtpSMTPSecure"),
				'Host' => $console->setting->getValue("smtpHost"),
				'Port' => $console->setting->getValue("smtpPort"),
				'Username' => $console->setting->getValue("smtpUsername"),
				'Password' => $console->setting->getValue("smtpPassword"),
			);
			$this->setMailSMTP($setData);
			$this->setMailFrom($console->setting->getValue("senderEmail"),$console->setting->getValue("senderName"));
		}

		/**
		 * SMTP設定
		 * @param array $data 
		 */
		public function setMailSMTP($data){
			$this->SMTPSecure = $data["SMTPSecure"];
			$this->Host = $data["Host"];
			$this->Port = $data["Port"]*1;
			$this->Username = $data["Username"];
			$this->Password = $data["Password"];
		}

		/**
		 * 設定信件標題
		 * @param String $value 信件標題
		 */
		public function setMailTitle($value){
			$this->Subject = $value; 
		}

		/**
		 * 設定寄件者
		 * @param String $value 寄件者信箱
		 * @param String $name  寄件者姓名
		 */
		public function setMailFrom($value,$name){
			$this->From = $value;
			$this->FromName = $name;
		}

		/**
		 * 新增檔案
		 * @param [type] $file  檔案路徑
		 * @param [type] $name  顯示的檔案名稱
		 */
		public function setMailFile($file,$name){
			if(is_file($file)){
				$this->AddAttachment($file,$name);
			}
		}

		/**
		 * 收件者郵件及(名稱)
		 * @param Array $data 收件者郵件，複數使用','區隔
		 */
		public function setMailAddress($data){
			$validation = new validation();
			$str = '';
			$data = explode(',', $data);
			foreach ($data as $key => $value){
				if($validation->isEmail($value)){
					$this->AddAddress($value);
				}else{
					if($value){
						$str .= $value.' ';
					}
				}				
			}
			// if($str){
			// 	$this->console->alert($this->console->getMessage('MAIL_ERROR',array($str)),'NO');
			// }
		}

		/**
		 * 設定樣板Smarty
		 * @param String $value 樣板名稱 e.g.,mail.html
		 * @param Array $data  要傳送的資料
		 */
		public function setMailBody($value,$data=array()){
			ob_start();

			$tpl = new \Smarty();
			$tpl->left_delimiter = '({';
			$tpl->right_delimiter = '})';
			$tpl->template_dir = APP_PATH.DATA_PATH."view/templates/mail";
			$tpl->compile_dir = APP_PATH.DATA_PATH."view/templates_c/mail";
			$tpl->config_dir = APP_PATH."view/configs/";
			$tpl->cache_dir = APP_PATH."view/cache/";

			$tpl->assign("console",$this->console);

			if(is_array($data)){
				foreach ($data as $k => $v) {
					$tpl->assign($k,$v);
				}
			}
			if(is_file($tpl->getTemplateDir(0).$value)){
				$tpl->display($value);
			}else{
				echo $this->console->getMessage('DISPLAY_NULL',array($value));
				exit;
			}

			$this->Body = ob_get_contents();
			ob_end_clean();
		}

		/**
		 * 寄送郵件
		 * @param  string $back 轉跳頁
		 * @return [type]       [description]
		 */
		public function sendMail($back='NO',$message='MAIL_SEND_OK',$message1=''){
			$this->IsHTML(true); //郵件內容為html
			$BCC = explode(',', $this->console->setting->getValue("recipientEmail"));
			foreach ($BCC as $key => $value){
				$this->AddBCC($value);		
			}
			if(!$this->Send()){
				$this->console->alert($this->console->getMessage('MAIL_SEND_ERROR'),'NO');
				error_log("Send mail error: " . $this->ErrorInfo);
				return false;
			}else{
				if($message1){//msg1優先度高
					$this->console->alert($message1,$back);
				}else if($message){
					$this->console->alert($this->console->getMessage($message),$back);
				}
				return true;
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