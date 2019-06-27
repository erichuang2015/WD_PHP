-- 產生時間： 2019 年 06 月 26 日 12:09 網站host localhost

DROP TABLE IF EXISTS `database_about__zh_tw`;

CREATE TABLE `database_about__zh_tw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urlKey` varchar(191) DEFAULT NULL COMMENT '自定key',
  `pageTitle` text COMMENT '自定title',
  `pageMeta` text COMMENT '自定meta',
  `class` text COMMENT '分類',
  `name` varchar(191) DEFAULT NULL COMMENT '名稱',
  `memo` text COMMENT '簡單內容',
  `detail` text COMMENT '內容',
  `picture` text COMMENT '圖片',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
  `release_date` datetime DEFAULT NULL COMMENT '上架時間',
  `expire_date` datetime DEFAULT NULL COMMENT '下架時間',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '最後修改時間',
  `create_user` varchar(191) NOT NULL COMMENT '創建人',
  `update_user` varchar(191) NOT NULL COMMENT '最後修改人',
  PRIMARY KEY (`id`),
  UNIQUE KEY `urlKey` (`urlKey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `database_admin`;

CREATE TABLE `database_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account` varchar(191) NOT NULL COMMENT '帳號',
  `password` varchar(191) DEFAULT NULL COMMENT '密碼',
  `groupID` int(11) NOT NULL COMMENT '群組',
  `name` varchar(191) DEFAULT NULL COMMENT '姓名',
  `email` varchar(191) NOT NULL COMMENT '電子郵件',
  `sex` varchar(20) NOT NULL COMMENT '1=男 2=女 3=不提供',
  `picture` mediumtext NOT NULL COMMENT '照片',
  `country` varchar(191) DEFAULT NULL COMMENT '國家',
  `county` varchar(191) DEFAULT NULL COMMENT '城市',
  `city` varchar(191) DEFAULT NULL COMMENT '區域',
  `zipcode` int(10) NOT NULL COMMENT '郵遞區號',
  `address` mediumtext NOT NULL COMMENT '住址',
  `company` varchar(100) NOT NULL COMMENT '公司名稱',
  `landline` varchar(100) NOT NULL COMMENT '家用電話',
  `landline2` varchar(100) NOT NULL COMMENT '家用電話2',
  `phone` varchar(100) NOT NULL COMMENT '手機號碼',
  `phone2` varchar(100) NOT NULL COMMENT '手機號碼2',
  `fax` varchar(100) NOT NULL COMMENT '傳真號碼',
  `fax2` varchar(100) NOT NULL COMMENT '傳真號碼2',
  `emailCheck` int(11) DEFAULT '-1' COMMENT '-1=不用驗證 0=未驗證 1=已驗證',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最後修改時間',
  `create_user` varchar(191) NOT NULL COMMENT '創建人',
  `update_user` varchar(191) NOT NULL COMMENT '最後修改人',
  `fbID` varchar(100) DEFAULT NULL COMMENT 'fb帳號連結',
  `fbName` varchar(100) DEFAULT NULL COMMENT 'fb名字',
  `fbEmail` varchar(100) DEFAULT NULL COMMENT 'fb-email',
  `fbPicture` mediumtext COMMENT 'fb大頭貼',
  `googleID` varchar(100) DEFAULT NULL COMMENT 'google帳號連結',
  `googleName` varchar(100) DEFAULT NULL COMMENT 'google名字',
  `googleEmail` varchar(100) DEFAULT NULL COMMENT 'google-email',
  `googlePicture` mediumtext COMMENT 'google大頭貼',
  `lineID` varchar(100) DEFAULT NULL COMMENT 'line帳號連結',
  `lineName` varchar(100) DEFAULT NULL COMMENT 'line名字',
  `lineEmail` varchar(100) DEFAULT NULL COMMENT 'line-email',
  `linePicture` mediumtext COMMENT 'line大頭貼',
  `memo` mediumtext NOT NULL COMMENT '備註',
  `point` mediumtext COMMENT 'ATOU',
  `doingTime` int(11) DEFAULT '3600' COMMENT 'ATOU',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account` (`account`),
  UNIQUE KEY `fbID` (`fbID`),
  UNIQUE KEY `googleID` (`googleID`),
  UNIQUE KEY `lineID` (`lineID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;


INSERT INTO `database_admin` (`id`,`account`,`password`,`groupID`,`name`,`email`,`sex`,`picture`,`country`,`county`,`city`,`zipcode`,`address`,`company`,`landline`,`landline2`,`phone`,`phone2`,`fax`,`fax2`,`emailCheck`,`status`,`create_date`,`update_date`,`create_user`,`update_user`,`fbID`,`fbName`,`fbEmail`,`fbPicture`,`googleID`,`googleName`,`googleEmail`,`googlePicture`,`lineID`,`lineName`,`lineEmail`,`linePicture`,`memo`,`point`,`doingTime`) VALUES
('1','vipadmin','$2y$10$vdcAEXgjaN2oCoElBmIOru6J9WB.hIkwGRg1G6lkEB9RhhpGXhxye','1','vipadmin','toby@vipcase.net','','','','','','0','','','','','','','','','-1','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,'','','',NULL,'','','',NULL,'','','','',NULL,'0'),
('2','service','$2y$10$0e14rlQE82WLN62PaOBgX.K3Q96hbmioOkO5ksWbkscVbBGoamJYy','2','客戶管理者','','','','','','','0','','','','','','','','','-1','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,'','','',NULL,'','','',NULL,'','','','',NULL,'3600'),
('4','wdadmin','$2y$10$5LUQOkppr.KvWTrSA/pqwO2N/aWCvWwbNWjgAH08E86RRM/LEZvTK','1','網動','','','',NULL,NULL,NULL,'0','','','','','','','','','-1','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,'3600');

DROP TABLE IF EXISTS `database_admin_group`;

CREATE TABLE `database_admin_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(191) DEFAULT NULL COMMENT '名稱',
  `control` int(11) NOT NULL COMMENT '權值',
  `auth` mediumtext NOT NULL COMMENT '權限',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最後修改時間',
  `create_user` varchar(191) NOT NULL COMMENT '創建人',
  `update_user` varchar(191) NOT NULL COMMENT '最後修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;


INSERT INTO `database_admin_group` (`id`,`name`,`control`,`auth`,`status`,`create_date`,`update_date`,`create_user`,`update_user`) VALUES
('1','系統管理員','-999','123,79,80,81,1,3,17,71,72,2,18,19,38,39,40,64,4,92,5,6,7,70,8,89,88,21,36,93,22,87,91,85,83,84,86,90,95,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin'),
('2','客戶管理者','0','1,3,17,71,72,2,18,19,38,39,40,4,6,123','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin');

DROP TABLE IF EXISTS `database_admin_logs`;

CREATE TABLE `database_admin_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account` varchar(50) NOT NULL COMMENT '帳號',
  `type` int(11) NOT NULL COMMENT '0註冊,1登入,2社群登入,3登出,4信件認證,5忘記密碼,6刪除,7帳號錯誤,8密碼錯誤,9驗證碼失敗',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '時間',
  `IP` varchar(20) NOT NULL COMMENT 'IP',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `database_album__zh_tw`;

CREATE TABLE `database_album__zh_tw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urlKey` varchar(191) DEFAULT NULL COMMENT '自定key',
  `pageTitle` text COMMENT '自定title',
  `pageMeta` text COMMENT '自定meta',
  `class` text COMMENT '分類',
  `name` varchar(191) DEFAULT NULL COMMENT '名稱',
  `memo` text COMMENT '簡單內容',
  `detail` text COMMENT '內容',
  `picture` text COMMENT '圖片',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
  `release_date` datetime DEFAULT NULL COMMENT '上架時間',
  `expire_date` datetime DEFAULT NULL COMMENT '下架時間',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '最後修改時間',
  `create_user` varchar(191) NOT NULL COMMENT '創建人',
  `update_user` varchar(191) NOT NULL COMMENT '最後修改人',
  PRIMARY KEY (`id`),
  UNIQUE KEY `urlKey` (`urlKey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `database_album_class__zh_tw`;

CREATE TABLE `database_album_class__zh_tw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urlKey` varchar(191) DEFAULT NULL COMMENT '自定key',
  `pageTitle` text COMMENT '自定title',
  `pageMeta` text COMMENT '自定meta',
  `name` varchar(191) NOT NULL COMMENT '名稱',
  `floor` int(11) NOT NULL COMMENT '層數',
  `parent` int(11) NOT NULL COMMENT '上層',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `step` int(11) NOT NULL DEFAULT '0' COMMENT '前序順序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
  `release_date` datetime DEFAULT NULL COMMENT '上架時間',
  `expire_date` datetime DEFAULT NULL COMMENT '下架時間',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最後修改時間',
  `create_user` varchar(191) NOT NULL COMMENT '創建人',
  `update_user` varchar(191) NOT NULL COMMENT '最後修改人',
  PRIMARY KEY (`id`),
  UNIQUE KEY `urlKey` (`urlKey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `database_contact__zh_tw`;

CREATE TABLE `database_contact__zh_tw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urlKey` varchar(191) DEFAULT NULL COMMENT '自定key',
  `pageTitle` text COMMENT '自定title',
  `pageMeta` text COMMENT '自定meta',
  `class` text COMMENT '分類',
  `name` varchar(191) DEFAULT NULL COMMENT '名稱',
  `memo` text COMMENT '簡單內容',
  `detail` text COMMENT '內容',
  `picture` text COMMENT '圖片',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
  `release_date` datetime DEFAULT NULL COMMENT '上架時間',
  `expire_date` datetime DEFAULT NULL COMMENT '下架時間',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '最後修改時間',
  `create_user` varchar(191) NOT NULL COMMENT '創建人',
  `update_user` varchar(191) NOT NULL COMMENT '最後修改人',
  PRIMARY KEY (`id`),
  UNIQUE KEY `urlKey` (`urlKey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `database_contact_form__zh_tw`;

CREATE TABLE `database_contact_form__zh_tw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最後修改時間',
  `create_user` varchar(191) NOT NULL DEFAULT '_AUTO_' COMMENT '創建人',
  `update_user` varchar(191) NOT NULL DEFAULT '_AUTO_' COMMENT '最後修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `database_coupon__zh_tw`;

CREATE TABLE `database_coupon__zh_tw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urlKey` varchar(191) DEFAULT NULL COMMENT '自定key',
  `pageTitle` text COMMENT '自定title',
  `pageMeta` text COMMENT '自定meta',
  `class` text COMMENT '分類',
  `name` varchar(191) DEFAULT NULL COMMENT '名稱',
  `memo` text COMMENT '簡單內容',
  `detail` text COMMENT '內容',
  `picture` text COMMENT '圖片',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
  `release_date` datetime DEFAULT NULL COMMENT '上架時間',
  `expire_date` datetime DEFAULT NULL COMMENT '下架時間',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '最後修改時間',
  `create_user` varchar(191) NOT NULL COMMENT '創建人',
  `update_user` varchar(191) NOT NULL COMMENT '最後修改人',
  PRIMARY KEY (`id`),
  UNIQUE KEY `urlKey` (`urlKey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `database_edm__zh_tw`;

CREATE TABLE `database_edm__zh_tw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urlKey` varchar(191) DEFAULT NULL COMMENT '自定key',
  `pageTitle` text COMMENT '自定title',
  `pageMeta` text COMMENT '自定meta',
  `class` text COMMENT '分類',
  `name` varchar(191) DEFAULT NULL COMMENT '名稱',
  `memo` text COMMENT '簡單內容',
  `detail` text COMMENT '內容',
  `picture` text COMMENT '圖片',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
  `release_date` datetime DEFAULT NULL COMMENT '上架時間',
  `expire_date` datetime DEFAULT NULL COMMENT '下架時間',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '最後修改時間',
  `create_user` varchar(191) NOT NULL COMMENT '創建人',
  `update_user` varchar(191) NOT NULL COMMENT '最後修改人',
  PRIMARY KEY (`id`),
  UNIQUE KEY `urlKey` (`urlKey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `database_faq__zh_tw`;

CREATE TABLE `database_faq__zh_tw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urlKey` varchar(191) DEFAULT NULL COMMENT '自定key',
  `pageTitle` text COMMENT '自定title',
  `pageMeta` text COMMENT '自定meta',
  `class` text COMMENT '分類',
  `name` varchar(191) DEFAULT NULL COMMENT '名稱',
  `memo` text COMMENT '簡單內容',
  `detail` text COMMENT '內容',
  `picture` text COMMENT '圖片',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
  `release_date` datetime DEFAULT NULL COMMENT '上架時間',
  `expire_date` datetime DEFAULT NULL COMMENT '下架時間',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '最後修改時間',
  `create_user` varchar(191) NOT NULL COMMENT '創建人',
  `update_user` varchar(191) NOT NULL COMMENT '最後修改人',
  PRIMARY KEY (`id`),
  UNIQUE KEY `urlKey` (`urlKey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `database_faq_class__zh_tw`;

CREATE TABLE `database_faq_class__zh_tw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urlKey` varchar(191) DEFAULT NULL COMMENT '自定key',
  `pageTitle` text COMMENT '自定title',
  `pageMeta` text COMMENT '自定meta',
  `name` varchar(191) NOT NULL COMMENT '名稱',
  `floor` int(11) NOT NULL COMMENT '層數',
  `parent` int(11) NOT NULL COMMENT '上層',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `step` int(11) NOT NULL DEFAULT '0' COMMENT '前序順序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
  `release_date` datetime DEFAULT NULL COMMENT '上架時間',
  `expire_date` datetime DEFAULT NULL COMMENT '下架時間',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最後修改時間',
  `create_user` varchar(191) NOT NULL COMMENT '創建人',
  `update_user` varchar(191) NOT NULL COMMENT '最後修改人',
  PRIMARY KEY (`id`),
  UNIQUE KEY `urlKey` (`urlKey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `database_foor__zh_tw`;

CREATE TABLE `database_foor__zh_tw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urlKey` varchar(191) DEFAULT NULL COMMENT '自定key',
  `pageTitle` text COMMENT '自定title',
  `pageMeta` text COMMENT '自定meta',
  `class` text COMMENT '分類',
  `name` varchar(191) DEFAULT NULL COMMENT '名稱',
  `memo` text COMMENT '簡單內容',
  `detail` text COMMENT '內容',
  `picture` text COMMENT '圖片',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
  `release_date` datetime DEFAULT NULL COMMENT '上架時間',
  `expire_date` datetime DEFAULT NULL COMMENT '下架時間',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '最後修改時間',
  `create_user` varchar(191) NOT NULL COMMENT '創建人',
  `update_user` varchar(191) NOT NULL COMMENT '最後修改人',
  PRIMARY KEY (`id`),
  UNIQUE KEY `urlKey` (`urlKey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `database_index__zh_tw`;

CREATE TABLE `database_index__zh_tw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urlKey` varchar(191) DEFAULT NULL COMMENT '自定key',
  `pageTitle` text COMMENT '自定title',
  `pageMeta` text COMMENT '自定meta',
  `class` text COMMENT '分類',
  `name` varchar(191) DEFAULT NULL COMMENT '名稱',
  `memo` text COMMENT '簡單內容',
  `detail` text COMMENT '內容',
  `picture` text COMMENT '圖片',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
  `release_date` datetime DEFAULT NULL COMMENT '上架時間',
  `expire_date` datetime DEFAULT NULL COMMENT '下架時間',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '最後修改時間',
  `create_user` varchar(191) NOT NULL COMMENT '創建人',
  `update_user` varchar(191) NOT NULL COMMENT '最後修改人',
  PRIMARY KEY (`id`),
  UNIQUE KEY `urlKey` (`urlKey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `database_member`;

CREATE TABLE `database_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account` varchar(191) NOT NULL COMMENT '帳號',
  `password` varchar(191) NOT NULL COMMENT '密碼',
  `groupID` int(11) DEFAULT NULL COMMENT '群組',
  `name` varchar(191) DEFAULT NULL COMMENT '姓名',
  `email` varchar(191) DEFAULT NULL COMMENT '電子郵件',
  `doingTime` int(11) DEFAULT '3600' COMMENT '閒置登出時間',
  `sex` varchar(20) DEFAULT NULL COMMENT '1=男 2=女 3=不提供',
  `point` int(11) DEFAULT '0' COMMENT '紅利點數',
  `picture` text COMMENT '照片',
  `country` varchar(191) DEFAULT NULL COMMENT '國家',
  `county` varchar(191) DEFAULT NULL COMMENT '城市',
  `city` varchar(191) DEFAULT NULL COMMENT '區域',
  `zipcode` int(10) DEFAULT NULL COMMENT '郵遞區號',
  `address` text COMMENT '住址',
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
  `fbPicture` text COMMENT 'fb大頭貼',
  `googleID` varchar(100) DEFAULT NULL COMMENT 'google帳號連結',
  `googleName` varchar(100) DEFAULT NULL COMMENT 'google名字',
  `googleEmail` varchar(100) DEFAULT NULL COMMENT 'google-email',
  `googlePicture` text COMMENT 'google大頭貼',
  `lineID` varchar(100) DEFAULT NULL COMMENT 'line帳號連結',
  `lineName` varchar(100) DEFAULT NULL COMMENT 'line名字',
  `lineEmail` varchar(100) DEFAULT NULL COMMENT 'line-email',
  `linePicture` text COMMENT 'line大頭貼',
  `memo` text NOT NULL COMMENT '備註',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account` (`account`),
  UNIQUE KEY `fbID` (`fbID`),
  UNIQUE KEY `googleID` (`googleID`),
  UNIQUE KEY `lineID` (`lineID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `database_memberField`;

CREATE TABLE `database_memberField` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urlKey` varchar(191) DEFAULT NULL COMMENT '自定key',
  `pageTitle` text COMMENT '自定title',
  `pageMeta` text COMMENT '自定meta',
  `class` text COMMENT '分類',
  `name` varchar(191) DEFAULT NULL COMMENT '名稱',
  `memo` text COMMENT '簡單內容',
  `detail` text COMMENT '內容',
  `picture` text COMMENT '圖片',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
  `release_date` datetime DEFAULT NULL COMMENT '上架時間',
  `expire_date` datetime DEFAULT NULL COMMENT '下架時間',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '最後修改時間',
  `create_user` varchar(191) NOT NULL COMMENT '創建人',
  `update_user` varchar(191) NOT NULL COMMENT '最後修改人',
  PRIMARY KEY (`id`),
  UNIQUE KEY `urlKey` (`urlKey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `database_member_group`;

CREATE TABLE `database_member_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL COMMENT '名稱',
  `control` int(11) NOT NULL COMMENT '權值',
  `auth` text NOT NULL COMMENT '權限',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最後修改時間',
  `create_user` varchar(191) NOT NULL COMMENT '創建人',
  `update_user` varchar(191) NOT NULL COMMENT '最後修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `database_member_logs`;

CREATE TABLE `database_member_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account` varchar(50) NOT NULL COMMENT '帳號',
  `type` int(11) NOT NULL COMMENT '0註冊,1登入,2社群登入,3登出,4信件認證,5忘記密碼,6刪除,7帳號錯誤,8密碼錯誤,9驗證碼失敗',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '時間',
  `IP` varchar(20) NOT NULL COMMENT 'IP',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `database_menu`;

CREATE TABLE `database_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(191) DEFAULT NULL COMMENT '名稱',
  `floor` int(11) NOT NULL COMMENT '層數',
  `parent` int(11) NOT NULL COMMENT '上層',
  `url` varchar(191) DEFAULT NULL COMMENT '網址',
  `sort` int(11) NOT NULL COMMENT '排序',
  `step` int(11) NOT NULL COMMENT '前序順序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最後修改時間',
  `create_user` varchar(191) NOT NULL COMMENT '創建人',
  `update_user` varchar(191) NOT NULL COMMENT '最後修改人',
  `features` text COMMENT 'ATOU',
  `alias` text COMMENT 'ATOU',
  `addMaxFloor` text COMMENT 'ATOU',
  `count` text COMMENT 'ATOU',
  `pageSetting` text COMMENT 'ATOU',
  `formData` text COMMENT 'ATOU',
  `mailingCheck` text COMMENT 'ATOU',
  `tcatBlackCatCheck` text COMMENT 'ATOU',
  `ecanHomeDeliveryCheck` text COMMENT 'ATOU',
  `famiCheck` text COMMENT 'ATOU',
  `unimartCheck` text COMMENT 'ATOU',
  `hilifeCheck` text COMMENT 'ATOU',
  `famiC2CCheck` text COMMENT 'ATOU',
  `unimartC2CCheck` text COMMENT 'ATOU',
  `hilifeC2CCheck` text COMMENT 'ATOU',
  `cashOnDeliveryCheck` text COMMENT 'ATOU',
  `physicalATMTransferCheck` text COMMENT 'ATOU',
  `physicalATMTransferECPayCheck` text COMMENT 'ATOU',
  `internetATMTransferECPayCheck` text COMMENT 'ATOU',
  `onlineCardECPayCheck` text COMMENT 'ATOU',
  `convenienceStorePickUpPaymentECPayCheck` text COMMENT 'ATOU',
  `cvsECPayCheck` text COMMENT 'ATOU',
  `barcodeECPayCheck` text COMMENT 'ATOU',
  `dataName` text COMMENT 'ATOU',
  `dataType` text COMMENT 'ATOU',
  `dataOption` text COMMENT 'ATOU',
  `dataRequired` text COMMENT 'ATOU',
  `releaseAndExpire` text COMMENT 'ATOU',
  `faceToFaceCheck` text COMMENT 'ATOU',
  `customShipment1Check` text COMMENT 'ATOU',
  `customShipment2Check` text COMMENT 'ATOU',
  `customShipment3Check` text COMMENT 'ATOU',
  `customShipment4Check` text COMMENT 'ATOU',
  `customShipment5Check` text COMMENT 'ATOU',
  `faceToFaceCheck1` text COMMENT 'ATOU',
  `payFiscPayCheck` text COMMENT 'ATOU',
  `dataKey` text COMMENT 'ATOU',
  `dataCount` text COMMENT 'ATOU',
  `dataSearch` text COMMENT 'ATOU',
  `dataExtension` text COMMENT 'ATOU',
  `dataSuggestText` text COMMENT 'ATOU',
  `dataTextOther` text COMMENT 'ATOU',
  `dataTextOtherText` text COMMENT 'ATOU',
  `dataTextareaOther` text COMMENT 'ATOU',
  `dataTextareaOtherText` text COMMENT 'ATOU',
  `dataSearchCount` text COMMENT 'ATOU',
  `tablesName` text COMMENT 'ATOU',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=123 DEFAULT CHARSET=utf8mb4;


INSERT INTO `database_menu` (`id`,`name`,`floor`,`parent`,`url`,`sort`,`step`,`status`,`create_date`,`update_date`,`create_user`,`update_user`,`features`,`alias`,`addMaxFloor`,`count`,`pageSetting`,`formData`,`mailingCheck`,`tcatBlackCatCheck`,`ecanHomeDeliveryCheck`,`famiCheck`,`unimartCheck`,`hilifeCheck`,`famiC2CCheck`,`unimartC2CCheck`,`hilifeC2CCheck`,`cashOnDeliveryCheck`,`physicalATMTransferCheck`,`physicalATMTransferECPayCheck`,`internetATMTransferECPayCheck`,`onlineCardECPayCheck`,`convenienceStorePickUpPaymentECPayCheck`,`cvsECPayCheck`,`barcodeECPayCheck`,`dataName`,`dataType`,`dataOption`,`dataRequired`,`releaseAndExpire`,`faceToFaceCheck`,`customShipment1Check`,`customShipment2Check`,`customShipment3Check`,`customShipment4Check`,`customShipment5Check`,`faceToFaceCheck1`,`payFiscPayCheck`,`dataKey`,`dataCount`,`dataSearch`,`dataExtension`,`dataSuggestText`,`dataTextOther`,`dataTextOtherText`,`dataTextareaOther`,`dataTextareaOtherText`,`dataSearchCount`,`tablesName`) VALUES
('1','FORESTAGE','0','0','','2','3','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('2','BACKSTAGE','0','0','','3','36','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('3','OTHER_MANAGEMENT','1','1','_null','8','31','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','_null','','','','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('4','SYSTEM_MANAGEMENT','1','2','','3','43','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('5','SYSTEM_MENU','2','4','systemMenu','2','45','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('6','SYSTEM_SETTING','2','4','systemSetting','3','46','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('7','LANGUAGE_MANAGEMENT','2','4','language','4','47','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('8','OPERATION_RECORD','2','4','systemLog','6','49','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('17','WEB_SETTING','2','3','setting/web','2','33','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','setting/web','','','','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('18','PERSONAL_MANAGEMENT','1','2','','1','37','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('19','PROFILE','2','18','profile','1','38','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('21','TEMPLATE_MANAGEMENT','1','2','','4','52','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('22','MAIL_TEMPLATE','2','21','template/mail','3','55','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('36','WEB_TEMPLATE','2','21','template/web','1','53','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('37','PDF_TEMPLATE','2','21','template/PDF','5','57','0','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('38','ADMIN_MANAGEMENT','1','2','','2','39','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('39','ADMIN_LIST','2','38','admin','1','40','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('40','ADMIN_GROUP','2','38','adminGroup','2','41','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('64','MEMBER_LOG','2','38','memberLog/admin','3','42','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('70','LANGUAGE_COPY','2','4','languageCopy','5','48','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('71','PAYMENT_SETTING','2','3','setting/payment','3','34','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','setting/payment','','','','0','0','0','0','0','0','0','0','0','0','0','1','1','0','0','0','0','0','0','','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('72','SHIPMENT_SETTING','2','3','setting/shipment','4','35','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','setting/shipment','','','','0','0','1','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('79','WEB_ANALYSIS','0','0','','1','0','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('80','WEB_ANALYSIS','1','79','','1','1','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('81','USER_ANALYSIS','2','80','analytics','1','2','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('87','HTML404_FILE','2','21','file/404.html','4','56','0','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('88','ROBOTS_TXT','2','4','file/robots.txt','8','51','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('89','HTACCESS_FILE','2','4','file/.htaccess','7','50','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('91','FILE_SETTING','1','2','','5','58','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('92','FRONT_SYSTEM_MENU','2','4','systemMenuFront','1','44','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('93','MODUEL_TEMPLATE','2','21','file/module','2','54','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('94','FILE_SETTING','2','91','file/data/10000','1','59','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('95','INDEX_MANAGEMENT','1','1','_null','1','4','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','_null','','','','0','0','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','','','','','0','1','1','1','1','1','1','1','1','','','','','','','','','',NULL,NULL),
('96','PAGE_MANAGEMENT','1','1','_null','2','7','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','_null','','','','0','0','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','','','','','0','1','1','1','1','1','1','1','1','','','','','','','','','',NULL,NULL),
('97','MEMBER_MANAGEMENT','1','1','_null','4','19','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','_null','','','','0','0','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','','','','','0','1','1','1','1','1','1','1','1','','','','','','','','','',NULL,NULL),
('98','PRODUCT_MANAGEMENT','1','1','_null','3','16','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','_null','','','','0','0','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','','','','','0','1','1','1','1','1','1','1','1','','','','','','','','','',NULL,NULL),
('99','ACTIVITY_MANAGEMENT','1','1','_null','5','24','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','_null','','','','0','0','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','','','','','0','1','1','1','1','1','1','1','1','','','','','','','','','',NULL,NULL),
('100','FORM_MANAGEMENT','1','1','_null','6','26','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','_null','','','','0','0','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','','','','','0','1','1','1','1','1','1','1','1','','','','','','','','','',NULL,NULL),
('101','EDM','2','95','basic/edm','1','5','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','_other_basic','edm','','0','0','0','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','圖片|__|連結網址','imageModule|__|text','','','0','1','1','1','1','1','1','1','1','picture|__|url','1|__|','|__|','|__|','1200x400|__|','|__|','|__|','|__|','|__|','|__|','edm'),
('102','CUSTOM_FIELD','1','1','_null','7','28','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','_null','','','','0','0','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','','','','','0','1','1','1','1','1','1','1','1','','','','','','','','','',NULL,NULL),
('103','CUSTOM_MEMBER_FIELD','2','102','memberField','2','30','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','memberField','','','','0','0','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','','','','','0','1','1','1','1','1','1','1','1','','','','','','','','','',NULL,NULL),
('104','CUSTOM_ORDER_FIELD','2','102','orderField','1','29','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','orderField','','','','0','0','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','','','','','0','1','1','1','1','1','1','1','1','','','','','','','','','',NULL,NULL),
('105','MEMBER_LEVEL','2','97','memberGroup','1','20','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','memberGroup','','','','0','0','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','','','','','0','1','1','1','1','1','1','1','1','','','','','','','','','',NULL,NULL),
('106','MEMBER_LIST','2','97','member','2','21','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','member','','','','0','0','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','','','','','0','1','1','1','1','1','1','1','1','','','','','','','','','',NULL,NULL),
('107','ORDERS','2','97','order','4','23','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','order','','','','0','0','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','','','','','0','1','1','1','1','1','1','1','1','','','','','','','','','',NULL,NULL),
('108','MEMBER_LOG','2','97','memberLog/member','3','22','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','memberLog/member','','','','0','0','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','','','','','0','1','1','1','1','1','1','1','1','','','','','','','','','',NULL,NULL),
('109','COUPON','2','99','basic/coupon','1','25','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','basic/coupon','','','','0','0','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','','','','','0','1','1','1','1','1','1','1','1','','','','','','','','','',NULL,NULL),
('110','PRODUCT_CATEGORY','2','98','class/product','1','17','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','class/product','product','1','','1','0','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','說明','aceEditor','','','0','1','1','1','1','1','1','1','1','memo','','','','','','','','','','product_class'),
('111','PRODUCT_LIST','2','98','basic/product','2','18','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','basic/product','product','','12','1','0','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','列表圖片|__|商品圖片|__|簡單敘述|__|內容','imageModule|__|imageModule|__|aceEditor|__|aceEditor','','','1','1','1','1','1','1','1','1','1','pictureList|__|picture|__|memo|__|detail','1|__|4|__||__|','|__||__||__|','|__||__||__|','800x800|__|800x800|__||__|','|__|Alt|__||__|','|__|alt|__||__|','|__||__||__|','|__||__||__|','|__||__||__|','product'),
('112','CONTACT','2','100','form/contact','1','27','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','_other_form','contact','','','0','0','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','','','','','0','1','1','1','1','1','1','1','1','','','','','','','','','',NULL,'contact_form'),
('113','FOOR_MANAGEMENT','2','3','basicOne/foor','1','32','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','_other_basicOne','foor','','','0','0','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','公司資訊','aceEditor','','','0','1','1','1','1','1','1','1','1','detail','','','','','','','','','','foor'),
('114','CONTACT','2','96','basicOne/contact','8','15','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','_other_basicOne','contact','','','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','公司位置','googleMap','','','0','1','1','1','1','1','1','1','1','googleMap','','','','','','','','','','contact'),
('115','FAQ','2','96','basic/faq','7','14','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','_other_basic','faq','','10','0','0','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','回答','aceEditor','','','0','1','1','1','1','1','1','1','1','detail','','','','','','','','','','faq'),
('116','FAQ','2','96','class/faq','6','13','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','_other_class','faq','0','','1','0','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','','','','','0','1','1','1','1','1','1','1','1','','','','','','','','','',NULL,'faq_class'),
('117','PHOTO_ALBUM','2','96','basic/album','5','12','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','_other_basic','album','','12','0','0','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','圖片|__|說明文字','imageModule|__|textarea','','','0','1','1','1','1','1','1','1','1','picture|__|detail','1|__|','|__|','|__|','|__|','|__|','|__|','|__|','|__|','|__|','album'),
('118','PHOTO_ALBUM','2','96','class/album','4','11','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','_other_class','album','1','','1','0','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','說明','aceEditor','','','0','1','1','1','1','1','1','1','1','memo','','','','','','','','','','album_class'),
('119','NEWS','2','96','basic/news','3','10','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','_other_basic','news','','10','1','0','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','日期|__|內容','date|__|aceEditor','','','1','1','1','1','1','1','1','1','1','date|__|detail','|__|','|__|','|__|','|__|','|__|','|__|','|__|','|__|','|__|','news'),
('120','NEWS','2','96','class/news','2','9','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','_other_class','news','0','','1','0','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','','','','','0','1','1','1','1','1','1','1','1','','','','','','','','','',NULL,'news_class'),
('121','ABOUT','2','96','basic/about','1','8','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','_other_basic','about','','0','1','0','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','內容','aceEditor','','','0','1','1','1','1','1','1','1','1','detail','','','','','','','','','','about'),
('122','INDEX_MANAGEMENT','2','95','basicOne/index','2','6','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','_other_basicOne','index','','','0','0','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','產品搜尋下方圖片|__|最新消息|__|相關連結|__|銷售 TOP','imageModule|__|search|__|imageModule|__|search','','','0','1','1','1','1','1','1','1','1','productPicture|__|news|__|links|__|productTop','1|__||__|8|__|','|__|news|__||__|product','|__||__||__|','500x217|__||__|400x300|__|','Href,Alt|__||__|Href,Alt|__|','網址,alt|__||__|網址,alt|__|','|__||__||__|','|__||__||__|','|__|6|__||__|10','index'),
('123','網址重定向','2','4','basic/redirect','9','24','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

DROP TABLE IF EXISTS `database_news__zh_tw`;

CREATE TABLE `database_news__zh_tw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urlKey` varchar(191) DEFAULT NULL COMMENT '自定key',
  `pageTitle` text COMMENT '自定title',
  `pageMeta` text COMMENT '自定meta',
  `class` text COMMENT '分類',
  `name` varchar(191) DEFAULT NULL COMMENT '名稱',
  `memo` text COMMENT '簡單內容',
  `detail` text COMMENT '內容',
  `picture` text COMMENT '圖片',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
  `release_date` datetime DEFAULT NULL COMMENT '上架時間',
  `expire_date` datetime DEFAULT NULL COMMENT '下架時間',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '最後修改時間',
  `create_user` varchar(191) NOT NULL COMMENT '創建人',
  `update_user` varchar(191) NOT NULL COMMENT '最後修改人',
  PRIMARY KEY (`id`),
  UNIQUE KEY `urlKey` (`urlKey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `database_news_class__zh_tw`;

CREATE TABLE `database_news_class__zh_tw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urlKey` varchar(191) DEFAULT NULL COMMENT '自定key',
  `pageTitle` text COMMENT '自定title',
  `pageMeta` text COMMENT '自定meta',
  `name` varchar(191) NOT NULL COMMENT '名稱',
  `floor` int(11) NOT NULL COMMENT '層數',
  `parent` int(11) NOT NULL COMMENT '上層',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `step` int(11) NOT NULL DEFAULT '0' COMMENT '前序順序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
  `release_date` datetime DEFAULT NULL COMMENT '上架時間',
  `expire_date` datetime DEFAULT NULL COMMENT '下架時間',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最後修改時間',
  `create_user` varchar(191) NOT NULL COMMENT '創建人',
  `update_user` varchar(191) NOT NULL COMMENT '最後修改人',
  PRIMARY KEY (`id`),
  UNIQUE KEY `urlKey` (`urlKey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `database_orderField`;

CREATE TABLE `database_orderField` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urlKey` varchar(191) DEFAULT NULL COMMENT '自定key',
  `pageTitle` text COMMENT '自定title',
  `pageMeta` text COMMENT '自定meta',
  `class` text COMMENT '分類',
  `name` varchar(191) DEFAULT NULL COMMENT '名稱',
  `memo` text COMMENT '簡單內容',
  `detail` text COMMENT '內容',
  `picture` text COMMENT '圖片',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
  `release_date` datetime DEFAULT NULL COMMENT '上架時間',
  `expire_date` datetime DEFAULT NULL COMMENT '下架時間',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '最後修改時間',
  `create_user` varchar(191) NOT NULL COMMENT '創建人',
  `update_user` varchar(191) NOT NULL COMMENT '最後修改人',
  PRIMARY KEY (`id`),
  UNIQUE KEY `urlKey` (`urlKey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `database_payment_setting__zh_tw`;

CREATE TABLE `database_payment_setting__zh_tw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL COMMENT 'name',
  `detail` text NOT NULL COMMENT '內容',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `database_product__zh_tw`;

CREATE TABLE `database_product__zh_tw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urlKey` varchar(191) DEFAULT NULL COMMENT '自定key',
  `class` text COMMENT '分類',
  `name` varchar(191) DEFAULT NULL COMMENT '名稱',
  `memo` text COMMENT '簡單內容',
  `detail` text COMMENT '內容',
  `picture` text COMMENT '圖片',
  `originalPrice` int(11) DEFAULT '0' COMMENT '原價',
  `specialPrice` int(11) DEFAULT '0' COMMENT '優惠價',
  `memberPrice` int(11) DEFAULT '0' COMMENT '會員價',
  `specificationsID` text COMMENT '規格編號',
  `specifications` text COMMENT '規格',
  `stock` text COMMENT '庫存',
  `maxCount` text COMMENT '單次最大限購數量',
  `addProduct` text COMMENT '加價購商品ID',
  `addProductSpecifications` text COMMENT '加價購商品規格編號',
  `addProductMaxCount` text COMMENT '加價購單次最大限購數量',
  `addProductMoney` text COMMENT '加價購金額',
  `suggestProduct` text COMMENT '推薦商品ID',
  `sort` int(11) NOT NULL COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最後修改時間',
  `create_user` varchar(191) NOT NULL COMMENT '創建人',
  `update_user` varchar(191) NOT NULL COMMENT '最後修改人',
  `pictureAlt` text COMMENT 'ATOU',
  `pageTitle` text COMMENT 'ATOU',
  `pageMeta` text COMMENT 'ATOU',
  `release_date` datetime DEFAULT NULL COMMENT '上架時間',
  `expire_date` datetime DEFAULT NULL COMMENT '下架時間',
  PRIMARY KEY (`id`),
  UNIQUE KEY `urlKey` (`urlKey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `database_product_class__zh_tw`;

CREATE TABLE `database_product_class__zh_tw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urlKey` varchar(191) DEFAULT NULL COMMENT '自定key',
  `pageTitle` text COMMENT '自定title',
  `pageMeta` text COMMENT '自定meta',
  `name` varchar(191) NOT NULL COMMENT '名稱',
  `floor` int(11) NOT NULL COMMENT '層數',
  `parent` int(11) NOT NULL COMMENT '上層',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `step` int(11) NOT NULL DEFAULT '0' COMMENT '前序順序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
  `release_date` datetime DEFAULT NULL COMMENT '上架時間',
  `expire_date` datetime DEFAULT NULL COMMENT '下架時間',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最後修改時間',
  `create_user` varchar(191) NOT NULL COMMENT '創建人',
  `update_user` varchar(191) NOT NULL COMMENT '最後修改人',
  PRIMARY KEY (`id`),
  UNIQUE KEY `urlKey` (`urlKey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `database_setting`;

CREATE TABLE `database_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(191) DEFAULT NULL COMMENT 'name',
  `detail` mediumtext NOT NULL COMMENT 'detail',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4;


INSERT INTO `database_setting` (`id`,`name`,`detail`) VALUES
('1','recipientEmail','toby@vipcase.net'),
('2','senderEmail','toby@vipcase.net'),
('3','senderName','toby'),
('4','smtpSMTPSecure','ssl'),
('5','smtpHost',''),
('6','smtpPort','465'),
('7','smtpUsername',''),
('8','smtpPassword',''),
('9','sizeSwitch','1'),
('10','webMaxSize','1099511627776'),
('11','uploadMaxSize','104857600'),
('12','outputMaxSize','52428800'),
('13','sqlMaxSize','104857600'),
('14','reCAPTCHASiteKey','6Lc57HIUAAAAAIVxZM3_5vXLk5eOJCdDX6CzYTJb'),
('15','reCAPTCHASecretKey','6Lc57HIUAAAAAHKgMav8javzVBC6YCxfC6bX3A2U'),
('16','googleAuthAppID',''),
('17','fbAuthAppID',''),
('18','lineAuthClientID',''),
('19','lineAuthClientSecret',''),
('20','update_date','2019-01-01 00:00:00'),
('21','update_user','vipadmin'),
('22','icon',''),
('23','backupSwitch','0'),
('24','backupDeleteDay','15'),
('25','backupDay','0.5'),
('26','backupMailUser',''),
('27','backupMailSwitch','0'),
('28','watermark',''),
('29','csrfWhitelist','www.ecpay.com.tw\r\nlogistics.ecpay.com.tw\r\nlogistics-stage.ecpay.com.tw\r\n175.99.72.1\r\n175.99.72.11\r\n175.99.72.24\r\n175.99.72.28\r\n175.99.72.32\r\nwww.focas-test.fisc.com.tw\r\nwww.focas.fisc.com.tw'),
('30','ecpayMerchantID','2000132'),
('31','ecpayHashKey','5294y06JbISpM5x9'),
('32','ecpayHashIV','v77hoKGq4kWxNNIS'),
('33','otherCode',''),
('34','indexPATH','demo'),
('35','oneUploadMaxSize','1048576'),
('36','analyticsCheck','0'),
('37','analyticsResetDay',''),
('38','recaptchaValidation','1'),
('39','memberCheck','1'),
('40','orderCheck','1'),
('41','ecpayCheck','0'),
('42','stockMode','1'),
('43','originalPriceCheck','1'),
('44','specialPriceCheck','1'),
('45','memberPriceCheck','1'),
('46','addProductCheck','1'),
('47','pointCheck','1'),
('48','couldMessageKey',''),
('49','messagingSenderId',''),
('50','couldMessageCode',''),
('51','fiscMerID',''),
('52','fiscMerchantID',''),
('53','fiscTerminalID','');

DROP TABLE IF EXISTS `database_shipment_setting__zh_tw`;

CREATE TABLE `database_shipment_setting__zh_tw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL COMMENT 'name',
  `detail` text NOT NULL COMMENT '內容',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `database_shopping_cart__zh_tw`;

CREATE TABLE `database_shopping_cart__zh_tw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderNumber` varchar(191) NOT NULL COMMENT '訂單編號',
  `memberId` int(11) DEFAULT '0' COMMENT '會員ID',
  `step` int(11) DEFAULT '1' COMMENT '步驟 1=未結帳,2=已結帳待付款,3=付款完成',
  `total` int(11) DEFAULT '0' COMMENT '總額',
  `freight` int(11) DEFAULT '0' COMMENT '運費',
  `deshprice` float DEFAULT '1' COMMENT '折扣',
  `deshpriceMoney` int(11) DEFAULT '0' COMMENT '折扣減少的錢',
  `getPoint` int(11) DEFAULT '0' COMMENT '取得的紅利',
  `getPointStatus` tinyint(1) DEFAULT '0' COMMENT '取得的紅利狀態',
  `usePoint` int(11) DEFAULT '0' COMMENT '使用的紅利',
  `usePointStatus` tinyint(1) DEFAULT '0' COMMENT '使用的紅利狀態',
  `pointDownMoney` int(11) DEFAULT '0' COMMENT '使用紅利減少的錢',
  `coupon` varchar(50) DEFAULT NULL COMMENT '折扣卷序號',
  `couponMoney` int(11) DEFAULT '0' COMMENT '使用的折扣卷扣的錢',
  `paymentMethod` int(11) DEFAULT '0' COMMENT '付款方式',
  `paymentStatus` int(11) DEFAULT '0' COMMENT '付款狀態',
  `shipmentMethod` int(11) DEFAULT '0' COMMENT '出貨方式',
  `shipmentStatus` int(11) DEFAULT '0' COMMENT '出貨狀態',
  `formData` text COMMENT '表單資料(json)',
  `memo` text COMMENT '備註',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最後修改時間',
  `payment_date` datetime DEFAULT NULL COMMENT '付款時間',
  `shipment_date` datetime DEFAULT NULL COMMENT '出貨時間',
  `create_user` varchar(191) NOT NULL DEFAULT '_AUTO_' COMMENT '創建人',
  `update_user` varchar(191) NOT NULL DEFAULT '_AUTO_' COMMENT '最後修改人',
  PRIMARY KEY (`id`),
  UNIQUE KEY `orderNumber` (`orderNumber`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;


INSERT INTO `database_shopping_cart__zh_tw` (`id`,`orderNumber`,`memberId`,`step`,`total`,`freight`,`deshprice`,`deshpriceMoney`,`getPoint`,`getPointStatus`,`usePoint`,`usePointStatus`,`pointDownMoney`,`coupon`,`couponMoney`,`paymentMethod`,`paymentStatus`,`shipmentMethod`,`shipmentStatus`,`formData`,`memo`,`status`,`create_date`,`update_date`,`payment_date`,`shipment_date`,`create_user`,`update_user`) VALUES
('2','5J9J40FWK','0','1','0','200','1','0','0','0','0','0','0','','0','0','0','0','0',NULL,NULL,'1','2019-01-01 00:00:00','2019-06-26 12:05:21',NULL,NULL,'vipadmin','vipadmin');

DROP TABLE IF EXISTS `database_shopping_cart_list__zh_tw`;

CREATE TABLE `database_shopping_cart_list__zh_tw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shoppingCartId` int(11) NOT NULL COMMENT '購物車ID',
  `parentId` int(11) DEFAULT NULL COMMENT '加價購母商品ID',
  `productId` int(11) NOT NULL COMMENT '商品ID',
  `specifications` text COMMENT '商品規格',
  `specificationsName` text COMMENT '商品規格名稱',
  `name` varchar(191) DEFAULT NULL COMMENT '商品名稱',
  `isDeshprice` int(11) NOT NULL COMMENT '是否享有打折',
  `count` int(11) DEFAULT '1' COMMENT '商品數量',
  `memo` text COMMENT '商品簡單內容',
  `detail` text COMMENT '商品內容',
  `picture` text COMMENT '商品圖片',
  `price` int(11) NOT NULL COMMENT '商品價格',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最後修改時間',
  `create_user` varchar(191) NOT NULL DEFAULT '_AUTO_' COMMENT '創建人',
  `update_user` varchar(191) NOT NULL DEFAULT '_AUTO_' COMMENT '最後修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `database_system_log`;

CREATE TABLE `database_system_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(191) NOT NULL COMMENT '語系',
  `type` varchar(191) NOT NULL COMMENT '異動模式',
  `dataTable` varchar(191) NOT NULL COMMENT '異動資料表',
  `oldData` text NOT NULL COMMENT '原始內容(json)',
  `newData` text NOT NULL COMMENT '異動內容(json)',
  `url` text NOT NULL COMMENT '異動網址',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '異動時間',
  `create_user` varchar(191) NOT NULL COMMENT '異動人',
  `IP` varchar(20) NOT NULL COMMENT 'IP',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `database_template`;

CREATE TABLE `database_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(191) DEFAULT NULL COMMENT 'type',
  `name` varchar(191) DEFAULT NULL COMMENT 'name',
  `detail` mediumtext NOT NULL COMMENT 'detail',
  `sort` int(11) NOT NULL COMMENT '排序',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最後修改時間',
  `create_user` varchar(191) NOT NULL COMMENT '創建人',
  `update_user` varchar(191) NOT NULL COMMENT '最後修改人',
  `useTables` text COMMENT '使用資料',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`type`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4;


INSERT INTO `database_template` (`id`,`type`,`name`,`detail`,`sort`,`create_date`,`update_date`,`create_user`,`update_user`,`useTables`) VALUES
('2','web','index.html','首頁','16','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL),
('5','mail','mail_forms-notice.html','聯絡我們','9','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL),
('6','mail','mail_member_notice.html','帳號開通確認信','11','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL),
('9','mail','mail_password_notice.html','忘記密碼','10','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL),
('13','mail','backup.html','網站備份','12','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL),
('14','mail','consumers_did_not_pick_up_the_goods_for_seven_days.html','7日未取件','8','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL),
('15','mail','successful_customer_pickup.html','消費者成功取件','7','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL),
('16','mail','goods_have_been_delivered_to_the_store.html','商品已送達門市','6','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL),
('17','mail','order_data_received.html','已收到訂單資料','5','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL),
('18','mail','shipped.html','已出貨','4','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL),
('19','mail','payment_completed.html','付款完成','3','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL),
('20','mail','send_remittance_information_mail.html','匯款資訊郵件','2','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL),
('21','mail','order_checkout_completed.html','訂單結帳完成','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL),
('22','web','top.html','上版','17','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','foor|__|news_class|__|product_class'),
('23','web','foor.html','下板','18','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',''),
('40','web','demo.html','建構中頁面','19','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL),
('41','web','member_detail.html','會員專區 會員資料','15','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',''),
('42','web','member_forget.html','會員專區 忘記密碼','14','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',''),
('43','web','member_join.html','會員專區 加入會員','13','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',''),
('44','web','member_login.html','會員專區 會員登入','12','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',''),
('45','web','member_order.html','會員專區 訂單查詢','11','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',''),
('46','web','member_password.html','會員專區 修改密碼','10','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',''),
('47','web','product.html','產品總覽','4','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',''),
('48','web','shopping_1.html','購物車 step 1','9','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',''),
('49','web','shopping_2.html','購物車 step 2','8','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',''),
('50','web','shopping_3.html','購物車 step 3','7','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',''),
('51','web','404.html','404頁面','20','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',''),
('52','web','about.html','關於我們','6','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',''),
('53','web','news.html','最新消息','5','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',''),
('54','web','album.html','相片集','3','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',''),
('55','web','faq.html','FAQ','2','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',''),
('56','web','contact.html','聯絡我們','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin','');

DROP TABLE IF EXISTS `database_web_setting__zh_tw`;

CREATE TABLE `database_web_setting__zh_tw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL COMMENT 'name',
  `detail` text NOT NULL COMMENT '內容',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4;


INSERT INTO `database_web_setting__zh_tw` (`id`,`name`,`detail`) VALUES
('1','webTitle','網站名稱'),
('2','webMeta','<meta name=\"keywords\" content=\"關鍵字\">\n<meta name=\"description\" content=\"內容說明\">\n<meta name=\"RATING\" content=\"general\">'),
('3','emailCheck','1'),
('4','noLoginOrder','0'),
('5','bonusMoney','0'),
('6','bonusPoint','0'),
('7','bonusDiscountMoney','0'),
('8','bonusDiscountPoint','0'),
('9','freight','200'),
('10','freeFreightMoney','1000'),
('11','senderName',''),
('12','senderPhone',''),
('13','senderCellPhone',''),
('14','senderZipCode',''),
('15','senderAddress',''),
('16','fbMsgBotCheck','0'),
('17','fanPageId',''),
('18','fbMsgBotColor',''),
('19','loggedInGreeting',''),
('20','loggedOutGreeting',''),
('21','update_date','2019-01-01 00:00:00'),
('22','update_user','vipadmin'),
('23','companyPhone',''),
('24','companyEmail',''),
('25','companyAddress','');

