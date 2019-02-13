<?php


/**
 * 商品
 * MTsung by 20180
 */
namespace MTsung{

	class product extends center{

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
						PRIMARY KEY (`id`),
						UNIQUE KEY `urlKey` (`urlKey`)
					) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
				");
			}
		}


		/**
		 * 取得商品價格
		 * @param  [type] $id      商品ID
		 * @param  [type] $isLogin 是否登入
		 * @return [type]          [description]
		 */
		public function getPrice($id,$isLogin=false){
			$temp = $this->getData("where id=?",array($id))[0];
			if($temp){
				$price = 0;
				if($isLogin && $temp["memberPrice"]>0){
					$price = $temp["memberPrice"];
				}else if($temp["specialPrice"]>0){
					$price = $temp["specialPrice"];
				}else if($temp["originalPrice"]>0){
					$price = $temp["originalPrice"];
				}else{
					return false;
				}
				return $price;
			}
			return false;

		}

		/**
		 * 取得加價購商品價格
		 * @param  [type] $parentId       [description]
		 * @param  [type] $id             [description]
		 * @param  [type] $specifications [description]
		 * @return [type]                 [description]
		 */
		public function getAddPrice($parentId,$id,$specifications){
			$temp = $this->getProduct($parentId);
			if($temp){
				$price = 0;
				$v1 = array_keys($temp["addProduct"],$id);
				foreach ($v1 as $key => $value) {
					if($specifications == $temp["addProductSpecifications"][$value]){
						$price = $temp["addProductMoney"][$value];
					}
				}
				return $price;
			}
			return false;

		}

		/**
		 * 取得商品
		 * @param  [type] $id [description]
		 * @return
		 */
		public function getProduct($id,$isGetAdd=false){
			$temp = $this->getData("where id=?",array($id))[0];
			if($temp){
				$temp["class"] = explode("|__|",$temp["class"]);//分類
				$temp["picture"] = explode("|__|",$temp["picture"]);//圖片
				$temp["specifications"] = explode("|__|",$temp["specifications"]);//規格名稱
				$temp["specificationsID"] = explode("|__|",$temp["specificationsID"]);//規格編號
				$temp["stock"] = explode("|__|",$temp["stock"]);//規格庫存
				$temp["maxCount"] = explode("|__|",$temp["maxCount"]);//最大購買數量

				$temp["addProduct"] = explode("|__|",$temp["addProduct"]);//可加價購商品ID
				$temp["addProductSpecifications"] = explode("|__|",$temp["addProductSpecifications"]);//加價購商品規格
				foreach ($temp["addProduct"] as $key => $value) {//加價購商品規格名稱
					$temp["addProductSpecificationsName"][$key] = $this->getSpecificationsName($temp["addProduct"][$key],$temp["addProductSpecifications"][$key]);
				}
				$temp["addProductMaxCount"] = explode("|__|",$temp["addProductMaxCount"]);//加價購商品最大數量
				$temp["addProductMoney"] = explode("|__|",$temp["addProductMoney"]);//加價購商品最大數量
				if(!$temp["urlKey"]){
					$temp["urlKey"] = $temp["id"];
				}
				if($isGetAdd){
					$temp["addProduct"] = $this->getAddProduct($temp["id"]);
				}
				return $temp;
			}
			return false;

		}

		/**
		 * 取得可以加價購的商品,規格,限購數量,金額
		 * @param  [type] $id [description]
		 * @return array      格式:	array(
		 *                        			'id' => array('商品id','商品id1'),
		 *                        			'specifications' => array('商品規格','商品規格1'),
		 *         					        'maxCount' => array('限購數量','限購數量1'),
		 *         					        'money' => array('金額','金額1')
		 *         					        'online' => array('目前商品資料','目前商品資料1')
		 *         					)
		 */
		public function getAddProduct($id){
			$temp = $this->getData("where id=?",array($id))[0];
			if($temp["addProduct"]){
				$temp["addProduct"] = explode("|__|", $temp["addProduct"]);
				$temp["addProductSpecifications"] = explode("|__|", $temp["addProductSpecifications"]);
				$temp["addProductMaxCount"] = explode("|__|", $temp["addProductMaxCount"]);
				$temp["addProductMoney"] = explode("|__|", $temp["addProductMoney"]);
				$addProduct = $idAndSpecifications = $maxCount = array();
				foreach ($temp["addProduct"] as $key => $value) {
					$addProductId[] = $value;
					$specifications[] = $temp["addProductSpecifications"][$key];
					$maxCount[] = $temp["addProductMaxCount"][$key];
					$addProductMoney[] = $temp["addProductMoney"][$key];
					$addProductOnline[] = $this->getProduct($value);
				}
				$addProduct = array('id' => $addProductId,
									'specifications' => $specifications,
									'maxCount' => $maxCount,
									'addProductMoney' => $addProductMoney,
									'online' => $addProductOnline
								);
				return $addProduct;
			}
			return false;

		}

		/**
		 * 取得商品規格名稱
		 * @param  [type] $id             [description]
		 * @param  [type] $specifications [description]
		 * @return [type]                 [description]
		 */
		public function getSpecificationsName($id,$specifications){
			$temp = $this->getData("where id=?",array($id))[0];
			if($temp["specifications"]){
				$temp["specificationsID"] = explode("|__|", $temp["specificationsID"]);
				$temp["specifications"] = explode("|__|", $temp["specifications"]);

				return $temp["specifications"][array_search($specifications, $temp["specificationsID"])];
			}
			return false;

		}

		/**
		 * 修改庫存
		 * @param [type] $id             [description]
		 * @param [type] $specifications [description]
		 * @param [type] $stock          [description]
		 */
		public function setStock($id,$specifications,$stock){
			$temp = $this->getData("where id=".$id)[0];
			$data["specificationsID"] = explode("|__|",$temp["specificationsID"]);
			$data["stock"] = explode("|__|",$temp["stock"]);
			$data["stock"][array_search($specifications, $data["specificationsID"])] = $stock;
			
			$data["id"] = $id;
			$data["specificationsID"] = implode("|__|",$data["specificationsID"]);
			$data["stock"] = implode("|__|",$data["stock"]);
			$this->setData($data);
		}

	}
}
