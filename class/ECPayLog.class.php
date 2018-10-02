<?php


/**
 * 綠界回傳資料
 * MTsung by 20180904
 */
namespace MTsung{

	class ECPayLog extends center{

		function __construct($console,$table=PREFIX."ecpay_log"){
			parent::__construct($console,$table,"");
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
					  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;
				");
			}
		}
	}
}
