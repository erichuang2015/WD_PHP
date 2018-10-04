<?php


/**
 * 選單
 * MTsung by 20180625
 */
namespace MTsung{

	class menu extends tree{

		function __construct($console,$table=PREFIX.'menu'){
			parent::__construct($console,$table);
			$this->checkTable($this->table);
		}

		/**
		 * 檢查資料表是否存在 不存在就建立
		 * @param  [type] $table 	table
		 * @return [type] 			存在 true,不存在 false
		 */
		function checkTable(){
			$temp = $this->conn->GetArray("desc ".$this->table);
			if(!$temp){
				$this->conn->Execute("
					CREATE TABLE `".$this->table."` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `name` varchar(191) NOT NULL COMMENT '名稱',
					  `floor` int(11) NOT NULL COMMENT '層數',
					  `parent` int(11) NOT NULL COMMENT '上層',
					  `url` varchar(191) NOT NULL COMMENT '網址',
					  `sort` int(11) NOT NULL COMMENT '排序',
					  `step` int(11) NOT NULL COMMENT '前序順序',
					  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
					  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
					  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最後修改時間',
					  `create_user` varchar(191) NOT NULL COMMENT '創建人',
					  `update_user` varchar(191) NOT NULL COMMENT '最後修改人',
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;
				");
				return false;
			}
			return true;
		}

	}
}
