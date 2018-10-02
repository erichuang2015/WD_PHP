<?php


/**
 * HTML轉PDF
 * MTsung by 20180522
 */
namespace MTsung{

	include_once(APP_PATH.'include/dompdf/autoload.inc.php');

	class HTMLtoPDF{
		var $console;
		var $dompdf;
		var $fileName;
		var $body;
		var $passWord;

		function __construct($console){
			$this->setConsole($console);
			$this->dompdf = new Dompdf\Dompdf();
			$this->setPassWord();
		}

		/**
		 * 設定樣板
		 * @param [type] $value 樣板名稱
		 * @param [type] $data  資料
		 */
		public function setBody($value,$data){
			ob_start();

			$tpl = new Smarty();
			$tpl->left_delimiter = '({';
			$tpl->right_delimiter = '})';
			$tpl->template_dir = APP_PATH . "view/templates/PDF";
			$tpl->compile_dir = APP_PATH . "view/templates_c/PDF";
			$tpl->config_dir = APP_PATH . "view/configs/";
			$tpl->cache_dir = APP_PATH . "view/cache/";

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

			$this->body = ob_get_contents();
			ob_end_clean();
			$this->dompdf->loadHtml($this->body, 'UTF-8');
		}

		/**
		 * 設定檔案名稱
		 * @param string $value [description]
		 */
		public function setFileName($value=''){
			$this->fileName = $value;
			if(!$this->fileName){
				do{
					$this->fileName = rand(10000000,19999999).'.pdf';
				}while(is_file(APP_PATH.'output/PDF/'.$this->fileName));
			}
		}

		/**
		 * 取得檔案名稱
		 * @return [type] [description]
		 */
		public function getFileName(){
			return $this->fileName;
		}

		/**
		 * 紙張設定
		 * @param string $value  A4
		 * @param string $value1 直式portrait，橫式landscape
		 */
		public function setPaper($value='A4',$value1=''){
			$this->dompdf->setPaper($value, $value1);
		}

		/**
		 * 設定Attachment
		 * @param integer $value 0 直接開啟, 1 下載
		 */
		public function setAttachment($value=1){
			$this->Attachment = $value;
		}

		/**
		 * 設定密碼
		 * @param String $value 
		 */
		public function setPassWord($value=''){
			$this->passWord = $value;
		}

		/**
		 * 輸出PDF
		 */
		public function outputPDF(){
			$this->dompdf->render();
			if($this->passWord!=''){
				$this->dompdf->get_canvas()->get_cpdf()->setEncryption($this->passWord);
			}
			$this->dompdf->stream($this->fileName , array('Attachment' => $this->Attachment, 'compress' => '1'));
			exit;
		}

		/**
		 * 保存到本機
		 * @return [type] [description]
		 */
		public function outputPDFfile(){
			if($setting->getValue("sizeSwitch")){
				if(!(
						($this->consolesetting->getValue("webMaxSize")-$this->console->getDirSize(APP_PATH)>0) && 
						($this->consolesetting->getValue("outputMaxSize")-$this->console->getDirSize(APP_PATH.'output/')>0)
					)){
					
					return false;
					exit;
				}
			}
			$this->dompdf->render();
			if($this->passWord!=''){
				$this->dompdf->get_canvas()->get_cpdf()->setEncryption($this->passWord);
			}
			$output = $this->dompdf->output();
			if(!is_dir(APP_PATH.'output')){
				mkdir(APP_PATH.'output');
			}
			if(!is_dir(APP_PATH.'output/PDF')){
				mkdir(APP_PATH.'output/PDF');
			}
			file_put_contents(APP_PATH.'output/PDF/'.$this->fileName, $output);
			exit;
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