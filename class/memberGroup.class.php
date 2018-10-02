<?php


/**
 * 會員群組模組
 * MTsung by 20180705
 */
namespace MTsung{

	class memberGroup extends center{

		function __construct($console,$table){
			parent::__construct($console,$table);
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
					  `name` varchar(191) NOT NULL COMMENT '名稱',
					  `control` int(11) NOT NULL COMMENT '權值',
					  `auth` text NOT NULL COMMENT '權限',
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


	}
}