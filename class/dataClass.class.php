<?php


/**
 * 分類
 * MTsung by 20180718
 */
namespace MTsung{

	class dataClass extends tree{

		function __construct($console,$table,$lang=LANG){
			parent::__construct($console,$table,$lang);
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
					  `urlKey` varchar(191) DEFAULT NULL COMMENT '自定key',
					  `pageTitle` TEXT DEFAULT NULL COMMENT '自定title',
					  `pageMeta` TEXT DEFAULT NULL COMMENT '自定meta',
					  `name` varchar(191) NOT NULL COMMENT '名稱',
					  `floor` int(11) NOT NULL COMMENT '層數',
					  `parent` int(11) NOT NULL COMMENT '上層',
					  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
					  `step` int(11) NOT NULL DEFAULT '0' COMMENT '前序順序',
					  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
					  `release_date` DATETIME COMMENT '上架時間',
					  `expire_date` DATETIME COMMENT '下架時間',
					  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
					  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最後修改時間',
					  `create_user` varchar(191) NOT NULL COMMENT '創建人',
					  `update_user` varchar(191) NOT NULL COMMENT '最後修改人',
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `urlKey` (`urlKey`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;
				");
				$this->conn->Execute("
					CREATE INDEX INDEX_NAME ON `".$this->table." (name);
				");
				return false;
			}
			if(!in_array("release_date", $this->getField())){
				$this->conn->Execute("ALTER TABLE ".$this->table." ADD release_date DATETIME COMMENT '上架時間'");
			}
			if(!in_array("expire_date", $this->getField())){
				$this->conn->Execute("ALTER TABLE ".$this->table." ADD expire_date DATETIME COMMENT '下架時間'");
			}
			return true;
		}

	}
}
