<?php


/**
 * 樣板輸出
 * MTsung by 20180522
 */
namespace MTsung{
    
    include_once(APP_PATH.'include/smarty/libs/Smarty.class.php');// 文件 https://www.smarty.net/docsv2/en/

    class design{
    	var $console;
    	var $tpl;

    	/**
    	 * 基本Smarty設定
    	 */
        function __construct(){
        	$this->tpl = new \Smarty();
    		$this->tpl->left_delimiter = '({';
    		$this->tpl->right_delimiter = '})';
    		$this->tpl->template_dir = APP_PATH.DATA_PATH."view/templates/web";
    		$this->tpl->compile_dir = APP_PATH.DATA_PATH."view/templates_c/web";
    		$this->tpl->config_dir = APP_PATH . "view/configs/";
    		$this->tpl->cache_dir = APP_PATH . "view/cache/";
        } 

        /**
         * 載入樣板
         * @param  String $value 樣板檔名 e.g.,index.html
         */
        public function loadDisplay($value){
        	if(is_file($this->tpl->getTemplateDir(0).$value)){
                $this->tpl->loadFilter('output','trimwhitespace');
        		$this->tpl->display($value);
        	}else{
        		echo $this->console->getMessage('DISPLAY_NULL',array($value));
        		exit;
        	}
        }

        /**
         * assign
         * @param [type] $name 變數名稱
         * @param [type] $data 資料
         */
        public function setData($name,$data){
    		$this->tpl->assign($name, $data);
        }

        /**
         * 設定console
         * @param Mtsung/main $console 
         */
        public function setConsole($console){
        	$this->console = $console;
        }

        /**
         * 後台樣板
         */
        public function setSerbackDir(){
    		$this->tpl->template_dir = APP_PATH . "view/templates/serback";
    		$this->tpl->compile_dir = APP_PATH . "view/templates_c/serback";
        }
    }
}