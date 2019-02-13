
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
-- ('3','admin','$2y$10$40GaEV/xy0vgHTte66pkle6aZ6DEQKNZgwzDDnut6VOPwY5jjctN.','3','設計師','','','',NULL,NULL,NULL,'0','','','','','','','','','-1','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,'3600'),
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
('1','系統管理員','-999','79,80,81,1,3,17,71,72,2,18,19,38,39,40,64,4,92,5,6,7,70,8,89,88,21,36,93,22,87,91,85,83,84,86,90,95,94','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin'),
('2','客戶管理者','0','1,3,17,71,72,2,18,19,38,39,40,4,6','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin');
-- ('3','設計師','0','1,3,17,71,72,2,18,19,21,36,93,22,87,91,85,83,84,94,95','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin');

DROP TABLE IF EXISTS `database_admin_logs`;

CREATE TABLE `database_admin_logs` (
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4;


INSERT INTO `database_menu` (`id`, `name`, `floor`, `parent`, `url`, `sort`, `step`, `status`, `create_date`, `update_date`, `create_user`, `update_user`, `features`, `alias`, `addMaxFloor`, `count`, `pageSetting`, `formData`, `mailingCheck`, `tcatBlackCatCheck`, `ecanHomeDeliveryCheck`, `famiCheck`, `unimartCheck`, `hilifeCheck`, `famiC2CCheck`, `unimartC2CCheck`, `hilifeC2CCheck`, `cashOnDeliveryCheck`, `physicalATMTransferCheck`, `physicalATMTransferECPayCheck`, `internetATMTransferECPayCheck`, `onlineCardECPayCheck`, `convenienceStorePickUpPaymentECPayCheck`, `cvsECPayCheck`, `barcodeECPayCheck`, `dataName`, `dataType`, `dataOption`, `dataRequired`) VALUES
(1, 'FORESTAGE', 0, 0, '', 2, 3, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'BACKSTAGE', 0, 0, '', 3, 8, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'OTHER_MANAGEMENT', 1, 1, '_null', 1, 4, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', '_null', '', '', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '', '', ''),
(4, 'SYSTEM_MANAGEMENT', 1, 2, '', 3, 15, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'SYSTEM_MENU', 2, 4, 'systemMenu', 2, 17, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'SYSTEM_SETTING', 2, 4, 'systemSetting', 3, 18, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'LANGUAGE_MANAGEMENT', 2, 4, 'language', 4, 19, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'OPERATION_RECORD', 2, 4, 'systemLog', 6, 21, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 'WEB_SETTING', 2, 3, 'setting/web', 1, 5, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', 'setting/web', '', '', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '', '', ''),
(18, 'PERSONAL_MANAGEMENT', 1, 2, '', 1, 9, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 'PROFILE', 2, 18, 'profile', 1, 10, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 'TEMPLATE_MANAGEMENT', 1, 2, '', 4, 24, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 'MAIL_TEMPLATE', 2, 21, 'template/mail', 3, 27, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(36, 'WEB_TEMPLATE', 2, 21, 'template/web', 1, 25, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(37, 'PDF_TEMPLATE', 2, 21, 'template/PDF', 5, 29, 0, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(38, 'ADMIN_MANAGEMENT', 1, 2, '', 2, 11, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(39, 'ADMIN_LIST', 2, 38, 'admin', 1, 12, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(40, 'ADMIN_GROUP', 2, 38, 'adminGroup', 2, 13, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(64, 'MEMBER_LOG', 2, 38, 'memberLog/admin', 3, 14, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(70, 'LANGUAGE_COPY', 2, 4, 'languageCopy', 5, 20, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(71, 'PAYMENT_SETTING', 2, 3, 'setting/payment', 2, 6, 0, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', 'setting/payment', '', '', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1', '1', '0', '0', '0', '0', '0', '0', '', '', '', ''),
(72, 'SHIPMENT_SETTING', 2, 3, 'setting/shipment', 3, 7, 0, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', 'setting/shipment', '', '', '', '0', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '', '', ''),
(79, 'WEB_ANALYSIS', 0, 0, '', 1, 0, 0, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(80, 'WEB_ANALYSIS', 1, 79, '', 1, 1, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(81, 'USER_ANALYSIS', 2, 80, 'analytics', 1, 2, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(83, 'CSS', 2, 91, 'file/css', 2, 32, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(84, 'JS', 2, 91, 'file/js', 3, 33, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(85, 'IMAGE_FILE', 2, 91, 'file/images', 1, 31, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(86, 'UPLOAD_FILE', 2, 91, 'file/upload', 6, 36, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(87, 'HTML404_FILE', 2, 21, 'file/404.html', 4, 28, 0, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(88, 'ROBOTS_TXT', 2, 4, 'file/robots.txt', 8, 23, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(89, 'HTACCESS_FILE', 2, 4, 'file/.htaccess', 7, 22, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(90, 'OUTPUT_FILE', 2, 91, 'file/output', 7, 37, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(91, 'FILE_SETTING', 1, 2, '', 5, 30, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(92, 'FRONT_SYSTEM_MENU', 2, 4, 'systemMenuFront', 1, 16, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(93, 'MODUEL_TEMPLATE', 2, 21, 'file/module', 2, 26, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(94, 'SVG_FILE_SETTING', 2, 91, 'file/svg', 5, 35, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(95, 'FONT_FILE_SETTING', 2, 91, 'file/fonts', 4, 34, 1, '2018-12-31 16:00:00', '2018-12-31 16:00:00', 'vipadmin', 'vipadmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

DROP TABLE IF EXISTS `database_setting`;

CREATE TABLE `database_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(191) DEFAULT NULL COMMENT 'name',
  `detail` mediumtext NOT NULL COMMENT 'detail',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4;


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
('10','webMaxSize','524288000'),
('11','uploadMaxSize','52428800'),
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
('29','csrfWhitelist','www.ecpay.com.tw\nlogistics.ecpay.com.tw\nlogistics-stage.ecpay.com.tw\n175.99.72.1\n175.99.72.11\n175.99.72.24\n175.99.72.28\n175.99.72.32'),
('30','ecpayMerchantID','2000132'),
('31','ecpayHashKey','5294y06JbISpM5x9'),
('32','ecpayHashIV','v77hoKGq4kWxNNIS'),
('33','otherCode',''),
('34','indexPATH','demo'),
('35','oneUploadMaxSize','1048576');

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
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4;


INSERT INTO `database_template` (`id`,`type`,`name`,`detail`,`sort`,`create_date`,`update_date`,`create_user`,`update_user`) VALUES
('2','web','index.html','首頁','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin'),
('5','mail','mail_forms-notice.html','聯絡我們','9','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin'),
('6','mail','mail_member_notice.html','帳號開通確認信','11','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin'),
('9','mail','mail_password_notice.html','忘記密碼','10','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin'),
('12','PDF','PDF.html','電子檢測報告_PCR','11','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin'),
('13','mail','backup.html','網站備份','12','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin'),
('14','mail','consumers_did_not_pick_up_the_goods_for_seven_days.html','7日未取件','8','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin'),
('15','mail','successful_customer_pickup.html','消費者成功取件','7','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin'),
('16','mail','goods_have_been_delivered_to_the_store.html','商品已送達門市','6','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin'),
('17','mail','order_data_received.html','已收到訂單資料','5','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin'),
('18','mail','shipped.html','已出貨','4','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin'),
('19','mail','payment_completed.html','付款完成','3','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin'),
('20','mail','send_remittance_information_mail.html','匯款資訊郵件','2','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin'),
('21','mail','order_checkout_completed.html','訂單結帳完成','1','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin'),
('22','web','top.html','上版','2','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin'),
('23','web','foor.html','下板','3','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin'),
('40','web','demo.html','建構中頁面','4','2019-01-01 00:00:00','2019-01-01 00:00:00','vipadmin','vipadmin');

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

