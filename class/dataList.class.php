<?php


/**
 * 資料
 * MTsung by 20180724
 */
namespace MTsung{

	class dataList extends center{

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
					  `urlKey` varchar(191) DEFAULT NULL COMMENT '自定key',
					  `pageTitle` TEXT DEFAULT NULL COMMENT '自定title',
					  `pageMeta` TEXT DEFAULT NULL COMMENT '自定meta',
					  `class` TEXT DEFAULT NULL COMMENT '分類',
					  `name` varchar(191) DEFAULT NULL COMMENT '名稱',
					  `memo` TEXT DEFAULT NULL COMMENT '簡單內容',
					  `detail` TEXT DEFAULT NULL COMMENT '內容',
					  `picture` TEXT DEFAULT NULL COMMENT '圖片',

					  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
					  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
					  `release_date` DATETIME COMMENT '上架時間',
					  `expire_date` DATETIME COMMENT '下架時間',
					  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
					  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '最後修改時間',
					  `create_user` varchar(191) NOT NULL COMMENT '創建人',
					  `update_user` varchar(191) NOT NULL COMMENT '最後修改人',
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `urlKey` (`urlKey`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;
				");
				$this->conn->Execute("
					CREATE INDEX INDEX_NAME ON `".$this->table." (name);
				");
			}
			if(!in_array("release_date", $this->getField())){
				$this->conn->Execute("ALTER TABLE ".$this->table." ADD release_date DATETIME COMMENT '上架時間'");
			}
			if(!in_array("expire_date", $this->getField())){
				$this->conn->Execute("ALTER TABLE ".$this->table." ADD expire_date DATETIME COMMENT '下架時間'");
			}
		}
	}
}
