<?php


/**
 * 購物車
 * MTsung by 20180
 */
namespace MTsung{

	class shoppingCart extends center{
		var $console;
		var $conn;
		var $lang;
		var $pointSetting;
		var $order;//購物車
		var $orderList;//購物車內容
		var $member;//會員
		var $memberInfo;//會員資料
		var $product;//商品
		var $table;//資料表名稱(購物車)
		var $tableList;//資料表名稱(購物車商品清單)
		var $message;//訊息
		var $isStockMode = true;//庫存模式

		function __construct($console,$member,$product,$table=PREFIX."shopping_cart",$lang=LANG){
			parent::__construct($console,$table,$lang);
			$this->isStockMode = $this->console->setting->getValue("stockMode");
			$this->member = $member;
			$this->memberInfo = $member->getInfo();
			$this->product = $product;
			$this->tableList = $table.'_list__'.str_replace("-","_",$this->lang);//不能用-
			$this->checkTable();
			// $this->reloadCart();
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
					  `orderNumber` varchar(191) NOT NULL COMMENT '訂單編號',
					  `memberId` int(11) DEFAULT '0' COMMENT '會員ID',
					  `step` int(11) DEFAULT 1 COMMENT '步驟 1=未結帳,2=已結帳待付款,3=付款完成',
					  `total` int(11) DEFAULT '0' COMMENT '總額',
					  `freight` int(11) DEFAULT '0' COMMENT '運費',
					  `deshprice` float DEFAULT 1 COMMENT '折扣',
					  `deshpriceMoney` int(11) DEFAULT '0' COMMENT '折扣減少的錢',
					  `getPoint` int(11) DEFAULT '0' COMMENT '取得的紅利',
					  `getPointStatus` tinyint(1) DEFAULT '0' COMMENT '取得的紅利狀態',
					  `usePoint` int(11) DEFAULT '0' COMMENT '使用的紅利',
					  `usePointStatus` tinyint(1) DEFAULT '0' COMMENT '使用的紅利狀態',
					  `pointDownMoney` int(11) DEFAULT '0' COMMENT '使用紅利減少的錢',
					  `coupon` varchar(50) COMMENT '折扣卷序號',
					  `couponMoney` int(11) DEFAULT '0' COMMENT '使用的折扣卷扣的錢',
					  `paymentMethod` int(11) DEFAULT '0' COMMENT '付款方式',
					  `paymentStatus` int(11) DEFAULT '0' COMMENT '付款狀態',
					  `shipmentMethod` int(11) DEFAULT '0' COMMENT '出貨方式',
					  `shipmentStatus` int(11) DEFAULT '0' COMMENT '出貨狀態',
					  `formData` TEXT DEFAULT NULL COMMENT '表單資料(json)',
					  `memo` TEXT DEFAULT NULL COMMENT '備註',

					  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
					  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
					  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最後修改時間',
					  `payment_date` datetime DEFAULT NULL COMMENT '付款時間',
					  `shipment_date` datetime DEFAULT NULL COMMENT '出貨時間',
					  `create_user` varchar(191) NOT NULL DEFAULT '_AUTO_' COMMENT '創建人',
					  `update_user` varchar(191) NOT NULL DEFAULT '_AUTO_' COMMENT '最後修改人',
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `orderNumber` (`orderNumber`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;
				");
			}

			$temp = $this->conn->GetArray("desc ".$this->tableList);
			if(!$temp){
				$this->conn->Execute("
					CREATE TABLE `".$this->tableList."` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `shoppingCartId` int(11) NOT NULL COMMENT '購物車ID',

					  `parentId` int(11) DEFAULT NULL COMMENT '加價購母商品ID',
					  `productId` int(11) NOT NULL COMMENT '商品ID',
					  `specifications` TEXT DEFAULT NULL COMMENT '商品規格',
					  `specificationsName` TEXT DEFAULT NULL COMMENT '商品規格名稱',
					  `name` varchar(191) DEFAULT NULL COMMENT '商品名稱',
					  `isDeshprice` int(11) NOT NULL COMMENT '是否享有打折',
					  `count` int(11) DEFAULT 1 COMMENT '商品數量',
					  `memo` TEXT DEFAULT NULL COMMENT '商品簡單內容',
					  `detail` TEXT DEFAULT NULL COMMENT '商品內容',
					  `picture` TEXT DEFAULT NULL COMMENT '商品圖片',
					  `price` int(11) NOT NULL COMMENT '商品價格',

					  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=開啟,0=關閉',
					  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
					  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最後修改時間',
					  `create_user` varchar(191) NOT NULL DEFAULT '_AUTO_' COMMENT '創建人',
					  `update_user` varchar(191) NOT NULL DEFAULT '_AUTO_' COMMENT '最後修改人',
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;
				");
			}
		}

		/**
		 * 購物車配置
		 * @return [type] [description]
		 */
		function reloadCart(){
			//非會員購物車最後更新時間2天後刪除
			$rmDate = date('Y-m-d H:i:s',strtotime(DATE)-86400*2);
			$sql = "delete ".$this->table." from ".$this->table." left join ".$this->tableList." on ".$this->tableList.".shoppingCartId=".$this->table.".id WHERE memberId='0' and step='1' and ".$this->table.".update_date<'".$rmDate."'";
			$this->conn->Execute($sql);

			do{
				$data["orderNumber"] = strtoupper(base_convert(microtime(true),10,36));
			}while($this->conn->GetRow("select * from ".$this->table." where step='1' and orderNumber='".$data["orderNumber"]."'"));

			//是否登入
			if($this->member->isLogin()){

				//session內有購物車的話取代 
				if(isset($_SESSION[FRAME_NAME]["shoppingCart"])){
					$shoppingCartId = $this->conn->GetRow("select * from ".$this->table." where step='1' and orderNumber='".$_SESSION[FRAME_NAME]["shoppingCart"]."'")["id"];
					if($this->conn->GetArray("select * from ".$this->tableList." where shoppingCartId='".$shoppingCartId."'")){
						$data["memberId"] = $this->memberInfo["id"];
						$this->conn->Execute("delete ".$this->table.",".$this->tableList." from ".$this->table." INNER JOIN ".$this->tableList." ON ".$this->table.".id=".$this->tableList.".shoppingCartId where step='1' and memberId='".$data["memberId"]."'");
						$this->conn->Execute("delete from ".$this->table." where step='1' and memberId='".$data["memberId"]."'");
						$this->conn->AutoExecute($this->table,$data,"UPDATE","orderNumber='".$_SESSION[FRAME_NAME]["shoppingCart"]."'");
						unset($_SESSION[FRAME_NAME]["shoppingCart"]);
					}
				}

				//沒購物車新增
				if (!$this->conn->GetRow("select * from ".$this->table." where step='1' and memberId='".$this->memberInfo["id"]."'")){
					$data["memberId"] = $this->memberInfo["id"];
					$this->conn->AutoExecute($this->table,$data,"INSERT");
				}

				//購物車資訊
				$this->order = $this->conn->GetRow("select * from ".$this->table." where step='1' and memberId='".$this->memberInfo["id"]."'");
			}else{
				//使用session記憶購物車orderNumber
				if(!isset($_SESSION[FRAME_NAME]["shoppingCart"]) || !$this->order = $this->conn->GetRow("select * from ".$this->table." where step='1' and orderNumber='".$_SESSION[FRAME_NAME]["shoppingCart"]."'")){
					$_SESSION[FRAME_NAME]["shoppingCart"] = $data["orderNumber"];
					$this->conn->AutoExecute($this->table,$data,"INSERT");
				}
				//購物車資訊
				$this->order = $this->conn->GetRow("select * from ".$this->table." where step='1' and orderNumber='".$_SESSION[FRAME_NAME]["shoppingCart"]."'");	
			}
			//購物車清單reload
			$this->reloadCartList();
			//計算總額(不含運費)
			$this->caluTotal();
			//計算紅利
			$this->caluPoint();

			$this->order = $this->conn->GetRow("select * from ".$this->table." where step='1' and id='".$this->order["id"]."'");
		}

		/**
		 * 購物車內容檢查，有異動會更新成最新資料
		 * @return [type] [description]
		 */
		function reloadCartList(){

			$orderList = $this->conn->GetArray("select * from ".$this->tableList." where shoppingCartId='".$this->order["id"]."'");
			if($orderList){
				foreach ($orderList as $key => $value) {
					$temp = $this->product->getProduct($value["productId"]);
					if($temp && in_array($value["specifications"],$temp["specificationsID"]) && $value["price"]>0){

						$name = $temp["specifications"][array_search($value["specifications"],$temp["specificationsID"])];
						if($value["specificationsName"] != $name) $data["specificationsName"] = $name;
						if($value["isDeshprice"] != $temp["isDeshprice"]) $data["isDeshprice"] = $temp["isDeshprice"];
						if($value["name"] != $temp["name"]) $data["name"] = $temp["name"];
						if($value["memo"] != $temp["memo"]) $data["memo"] = $temp["memo"];
						if($value["detail"] != $temp["detail"]) $data["detail"] = $temp["detail"];
						if($value["picture"] != $temp["picture"][0]) $data["picture"] = $temp["picture"][0];
						if($value["parentId"]){
							if($value["price"] != $this->product->getAddPrice($value["parentId"],$value["productId"],$value["specifications"])) $data["price"] = $this->product->getAddPrice($value["parentId"],$value["productId"],$value["specifications"]);
						}else{
							if($value["price"] != $this->product->getPrice($value["productId"],$this->member->isLogin())) $data["price"] = $this->product->getPrice($value["productId"],$this->member->isLogin());
						}

						//商品內容有更動才修改資料
						if(isset($data)){
							$this->conn->AutoExecute($this->tableList,$data,"UPDATE","shoppingCartId='".$this->order["id"]."' and productId='".$value["productId"]."' and specifications='".$value["specifications"]."'");
						}
					}else{
						//刪除商品規格不存在的資料
						$this->conn->Execute("DELETE FROM ".$this->tableList." where shoppingCartId='".$this->order["id"]."' and productId='".$value["productId"]."' and specifications='".$value["specifications"]."'");
					}
				}
			}

			$this->orderList = $this->conn->GetArray("select * from ".$this->tableList." where shoppingCartId='".$this->order["id"]."'");
			$this->order = $this->conn->GetRow("select * from ".$this->table." where step='1' and id='".$this->order["id"]."'");
		}

		/**
		 * 計算總額(不含運費)
		 * @return [type]        [description]
		 */
		function caluTotal(){
			$total = 0;
			if($this->orderList){
				foreach ($this->orderList as $key => $value) {
					$total += ($value["price"]*$value["count"]);
				}
			}

			//折扣
			$tempTotal = $total;
			if(is_numeric($this->order["deshprice"]) && $this->order["deshprice"]<=1 && $this->order["deshprice"]>0){
				$total *= $this->order["deshprice"];
				$total = round($total);
			}
			$this->updateOrder(array("deshpriceMoney"=>$tempTotal-$total));

			//確認使用紅利沒超過會員身上的點數
			$memberPoint = $this->member->getInfo("point")?$this->member->getInfo("point"):0;
			if($this->order["usePoint"] > $memberPoint){
				$this->updateOrder(array("usePoint"=>$memberPoint));
			}
			
			//使用紅利減少的錢
			$pointDownMoney = 0;
			if($this->pointSetting[2]>0){
				$pointDownMoney = floor(($this->pointSetting[3] / $this->pointSetting[2]) * $this->order['usePoint']);
			}
			if($pointDownMoney>$total){
				$pointDownMoney = 0;
				$this->updateOrder(array("usePoint"=>0));
			}

			$total -= $pointDownMoney;

			//折扣卷
			if(is_numeric($this->order["couponMoney"])){
				$total -= $this->order["couponMoney"];
			}

			$this->updateOrder(array("pointDownMoney"=>$pointDownMoney));
			$this->updateOrder(array("total"=>$total));
		}

		/**
		 * 使用折價卷
		 * @param  [type] $detail [description]
		 * @return [type]         [description]
		 */
		function useCoupon($detail){
			if(!$this->member->isLogin()){
				$this->message = $this->console->getMessage("MEMBER_LIMITED_FEATURES");
				return false;
			}
			if(!$detail){
				$this->updateOrder(array("coupon" => "","couponMoney" => ""));
				return true;
			}
			$cartTotal = ($this->order["total"]+$this->order["couponMoney"]);
			$coupon = new dataList($this->console,PREFIX."coupon",$this->lang);

			//是否使用過
			if($temp = $this->getData("where memberId=? and coupon=? and status=1 and step>1",array($this->member->getInfo("id"),$detail))){
				$this->message = $this->console->getMessage("COUPON_IS_USED");
				$this->updateOrder(array("coupon" => "","couponMoney" => ""));
				return false;
			}

			//是否存在
			if(!$temp = $coupon->getData("where status=1 
												and (effectiveDate<='".DATE."' or effectiveDate is null or effectiveDate='') 
												and (invalidDate>='".DATE."' or invalidDate is null or invalidDate='') 
												and detail=?
										",array($detail))){
				$this->message = $this->console->getMessage("INPUT_ERROR");
				$this->updateOrder(array("coupon" => "","couponMoney" => ""));
				return false;
			}
			$value = $temp[0];

			//是否大於最小訂單金額
			if($value["minOrderMoney"]>$cartTotal){
				$this->message = $this->console->getMessage("MIN_ORDER_MONEY",array($value["minOrderMoney"]));
				$this->updateOrder(array("coupon" => "","couponMoney" => ""));
				return false;
			}

			//大於訂單金額全額折扣
			if($value["money"]>$cartTotal){
				$this->updateOrder(array("coupon" => $detail,"couponMoney" => $cartTotal));
				return true;
			}

			$this->updateOrder(array("coupon" => $detail,"couponMoney" => $value["money"]));
			return true;
		}


		/**
		 * 計算紅利
		 * @return [type]        [description]
		 */
		function caluPoint(){
			if($this->pointSetting){
				$total = $this->order["total"];
				$getPoint = 0;
				if($this->pointSetting[0]){
					$getPoint = floor($total / $this->pointSetting[0]) * $this->pointSetting[1];
				}
				$this->updateOrder(array("getPoint"=>$getPoint));
			}
		}

		/**
		 * 使用紅利
		 * @param  integer $usePoint [description]
		 * @return [type]            [description]
		 */
		function usePoint($usePoint=0){
			if(!$this->member->isLogin()){
				$this->message = $this->console->getMessage("MEMBER_LIMITED_FEATURES");
				return false;
			}
			if($this->pointSetting){
				if(is_numeric($usePoint) && $usePoint >= 0){
					$memberPoint = $this->member->getInfo("point")?$this->member->getInfo("point"):0;
					if($usePoint > $memberPoint){
						$this->message = $this->console->getMessage("MEMBER_POINT_NOT_ENOUGH",array($usePoint,$memberPoint));
						return false;
					}
					
					$pointDownMoney = 0;
					if($this->pointSetting[2]){
						$pointDownMoney = floor(($this->pointSetting[3] / $this->pointSetting[2]) * ($usePoint-$this->order['usePoint']));
					}
					if($pointDownMoney > $this->order["total"]){
						$this->message = $this->console->getMessage("POINT_GT_TOTAL");
						return false;
					}
					$this->updateOrder(array("pointDownMoney"=>$pointDownMoney));
					$this->updateOrder(array("usePoint"=>$usePoint));
					return true;
				}
			}
			$this->message = $this->console->getMessage("INPUT_ERROR");
			return false;
		}
		
		/**
		 * 加入商品
		 * @param [type] $id             [description]
		 * @param [type] $count          [description]
		 * @param [type] $specifications [description]
		 */
		function addProduct($id,$count,$specifications){
			if(!is_numeric($count) || $count<1){
				$this->message = $this->console->getMessage("COUNT_ERROR");
				return false;
			}
			//加價購數量的也要算進去
			$addCount = @$this->conn->GetRow($this->conn->Prepare("select count from ".$this->tableList." where parentId is NOT NULL and shoppingCartId=? and productId=? and specifications=?"),array($this->order["id"],$id,$specifications))[0];
			if(!$addCount) $addCount = 0;

			$temp = $this->conn->GetRow($this->conn->Prepare("select * from ".$this->tableList." where parentId is NULL and shoppingCartId=? and productId=? and specifications=?"),array($this->order["id"],$id,$specifications));
			if($temp){
				return $this->editProductCount($id,$temp["count"]+$count,$specifications);
			}else{
				$temp = $this->product->getProduct($id);
				if($temp && in_array($specifications,$temp["specificationsID"])){
					$maxCount = $temp["maxCount"][array_search($specifications, $temp["specificationsID"])];
					if(($count > $maxCount) && $maxCount){
						$this->message = $this->console->getMessage("ERROR_PRODUCT_MAX_COUNT");
						return false;
					}
					if($count+$addCount > $temp["stock"][array_search($specifications, $temp["specificationsID"])] && $this->isStockMode){
						$this->message = $this->console->getMessage("ERROR_PRODUCT_STOCK",array($temp["name"]." ".$temp["specifications"][array_search($specifications, $temp["specificationsID"])],$count+$addCount));
						return false;
					}
					$data["shoppingCartId"] = $this->order["id"];
					$data["productId"] = $id;
					$data["specifications"] = $specifications;
					$data["name"] = $temp["name"];
					$data["count"] = $count;
					$data["memo"] = $temp["memo"];
					$data["detail"] = $temp["detail"];
					$data["picture"] = $temp["picture"][0];
					$data["price"] = $this->product->getPrice($id,$this->member->isLogin());

					if(!$data["price"]){
						$this->message = $this->console->getMessage("PRICE_IS_NULL");
						return false;
					}
					if($this->conn->AutoExecute($this->tableList,$data,"INSERT")){
						$this->message = $this->console->getMessage("ADD_PRODUCT_OK");
						return true;
					}else{
						$this->message = $this->console->getMessage("ADD_PRODUCT_ERROR");
						return false;
					}
				}else{
					$this->message = $this->console->getMessage("PRODUCT_NULL");
					return false;
				}
			}
		}

		/**
		 * 編輯商品數量
		 * @param  [type] $id             [description]
		 * @param  [type] $count          [description]
		 * @param  [type] $specifications [description]
		 * @return [type]                 [description]
		 */
		function editProductCount($id,$count,$specifications){
			if(!is_numeric($count) || $count<1){
				$this->message = $this->console->getMessage("COUNT_ERROR");
				return false;
			}
			//加價購數量的也要算進去
			$addCount = @$this->conn->GetRow($this->conn->Prepare("select count from ".$this->tableList." where parentId is NOT NULL and shoppingCartId=? and productId=? and specifications=?"),array($this->order["id"],$id,$specifications))[0];
			if(!$addCount) $addCount = 0;

			$temp = $this->conn->GetRow($this->conn->Prepare("select * from ".$this->tableList." where parentId is NULL and shoppingCartId=? and productId=? and specifications=?"),array($this->order["id"],$id,$specifications));
			if($temp){
				$temp = $this->product->getProduct($id);
				$maxCount = $temp["maxCount"][array_search($specifications, $temp["specificationsID"])];
				if(($count > $maxCount) && $maxCount){
					$this->message = $this->console->getMessage("ERROR_PRODUCT_MAX_COUNT");
					return false;
				}
				if($count+$addCount > $temp["stock"][array_search($specifications, $temp["specificationsID"])] && $this->isStockMode){
					$this->message = $this->console->getMessage("ERROR_PRODUCT_STOCK",array($temp["name"]." ".$temp["specifications"][array_search($specifications, $temp["specificationsID"])],$count+$addCount));
					return false;
				}
				$data["count"] = $count;
				if($this->conn->AutoExecute($this->tableList,$data,"UPDATE"," parentId is NULL and shoppingCartId='".$this->order["id"]."' and productId='".$id."' and specifications='".$specifications."'")){
					$this->message = $this->console->getMessage("EDIT_PRODUCT_COUNT_OK");
					return true;
				}else{
					$this->message = $this->console->getMessage("EDIT_PRODUCT_COUNT_ERROR");
					return false;
				}
			}else{
				$this->message = $this->console->getMessage("SHOOPING_CART_PRODUCT_NULL");
				return false;
			}
		}

		/**
		 * 刪除商品
		 * @param  [type] $id             [description]
		 * @param  [type] $specifications [description]
		 * @return [type]                 [description]
		 */
		function rmProduct($id,$specifications){
			$temp = $this->conn->GetRow($this->conn->Prepare("select * from ".$this->tableList." where parentId is NULL and shoppingCartId=? and productId=? and specifications=?"),array($this->order["id"],$id,$specifications));
			if($temp){
				if($this->conn->Execute("DELETE FROM ".$this->tableList." where shoppingCartId='".$this->order["id"]."' and productId='".$id."' and specifications='".$specifications."'")){
					$this->conn->Execute("DELETE FROM ".$this->tableList." where parentId='".$id."' and shoppingCartId='".$this->order["id"]."'");
					$this->message = $this->console->getMessage("DELETE_PRODUCT_COUNT_OK");
					return true;
				}else{
					$this->message = $this->console->getMessage("DELETE_PRODUCT_COUNT_ERROR");
					return false;
				};
			}else{
				$this->message = $this->console->getMessage("SHOOPING_CART_PRODUCT_NULL");
				return false;
			}
		}

		/**
		 * 加入加價購商品
		 * @param [type] $parentId       [description]
		 * @param [type] $id             [description]
		 * @param [type] $count          [description]
		 * @param [type] $specifications [description]
		 */
		function addAddProduct($parentId,$id,$count,$specifications){
			if(!is_numeric($count) || $count<1){
				$this->message = $this->console->getMessage("COUNT_ERROR");
				return false;
			}
			//原商品數量的也要算進去
			$parentCount = @$this->conn->GetRow($this->conn->Prepare("select count from ".$this->tableList." where parentId is NULL and shoppingCartId=? and productId=? and specifications=?"),array($this->order["id"],$id,$specifications))[0];
			if(!$parentCount) $parentCount = 0;

			$temp = $this->conn->GetRow($this->conn->Prepare("select * from ".$this->tableList." where shoppingCartId=? and parentId=? and productId=? and specifications=?"),array($this->order["id"],$parentId,$id,$specifications));
			if($temp){
				return $this->editAddProductCount($parentId,$id,$temp["count"]+$count,$specifications);
			}else{
				$temp = $this->conn->GetRow($this->conn->Prepare("select * from ".$this->tableList." where shoppingCartId=? and productId=?"),array($this->order["id"],$parentId));
				if($temp){
					$tempParent = $this->product->getProduct($parentId);
					$tempAdd = $this->product->getProduct($id);
					if(in_array($specifications,$tempAdd["specificationsID"]) && in_array($specifications,$tempParent["addProductSpecifications"])){
						$addMaxCount = $tempParent["addProductMaxCount"][array_search($specifications, $tempParent["addProductSpecifications"])];
						if(($count > $addMaxCount) && $addMaxCount){
							$this->message = $this->console->getMessage("ERROR_PRODUCT_MAX_COUNT");
							return false;
						}
						if($count+$parentCount > $tempAdd["stock"][array_search($specifications, $tempAdd["specificationsID"])] && $this->isStockMode){
							$this->message = $this->console->getMessage("ERROR_PRODUCT_STOCK",array($tempAdd["name"],$count+$parentCount));
							return false;
						}
						$data["shoppingCartId"] = $this->order["id"];
						$data["parentId"] = $parentId;
						$data["productId"] = $id;
						$data["specifications"] = $specifications;
						$data["name"] = $tempAdd["name"];
						$data["count"] = $count;
						$data["memo"] = $tempAdd["memo"];
						$data["detail"] = $tempAdd["detail"];
						$data["picture"] = $tempAdd["picture"][0];
						$data["price"] = $this->product->getAddPrice($parentId,$id,$specifications);

						if(!$data["price"]){
							$this->message = $this->console->getMessage("PRICE_IS_NULL");
							return false;
						}
						if($this->conn->AutoExecute($this->tableList,$data,"INSERT")){
							$this->message = $this->console->getMessage("ADD_ADD_PRODUCT_OK");
							return true;
						}else{
							$this->message = $this->console->getMessage("ADD_ADD_PRODUCT_ERROR");
							return false;
						}
					}else{
						$this->message = $this->console->getMessage("PRODUCT_NULL");
						return false;
					}

				}else{
					$this->message = $this->console->getMessage("ADD_PRODUCT_PARENT_NULL");
					return false;
				}


			}

		}

		/**
		 * 編輯加價購商品數量
		 * @param  [type] $parentId       [description]
		 * @param  [type] $id             [description]
		 * @param  [type] $count          [description]
		 * @param  [type] $specifications [description]
		 * @return [type]                 [description]
		 */
		function editAddProductCount($parentId,$id,$count,$specifications){
			if(!is_numeric($count) || $count<1){
				$this->message = $this->console->getMessage("COUNT_ERROR");
				return false;
			}
			//原商品數量的也要算進去
			$parentCount = @$this->conn->GetRow($this->conn->Prepare("select count from ".$this->tableList." where parentId is NULL and shoppingCartId=? and productId=? and specifications=?"),array($this->order["id"],$id,$specifications))[0];
			if(!$parentCount) $parentCount = 0;

			$temp = $this->conn->GetRow($this->conn->Prepare("select * from ".$this->tableList." where shoppingCartId=? and parentId=? and productId=? and specifications=?"),array($this->order["id"],$parentId,$id,$specifications));
			if($temp){

				$tempParent = $this->product->getProduct($parentId);
				$tempAdd = $this->product->getProduct($id);
				$addMaxCount = $tempParent["addProductMaxCount"][array_search($specifications, $tempParent["addProductSpecifications"])];
				if(($count > $addMaxCount) && $addMaxCount){
					$this->message = $this->console->getMessage("ERROR_PRODUCT_MAX_COUNT");
					return false;
				}
				if($count+$parentCount > $tempAdd["stock"][array_search($specifications, $tempAdd["specificationsID"])] && $this->isStockMode){
					$this->message = $this->console->getMessage("ERROR_PRODUCT_STOCK",array($tempAdd["name"],$count+$parentCount));
					return false;
				}
				$data["count"] = $count;
				if($this->conn->AutoExecute($this->tableList,$data,"UPDATE"," parentId='".$parentId."' and shoppingCartId='".$this->order["id"]."' and productId='".$id."' and specifications='".$specifications."'")){
					$this->message = $this->console->getMessage("EDIT_ADD_PRODUCT_COUNT_OK");
					return true;
				}else{
					$this->message = $this->console->getMessage("EDIT_ADD_PRODUCT_COUNT_ERROR");
					return false;
				}
			}else{
				$this->message = $this->console->getMessage("SHOOPING_CART_PRODUCT_NULL");
				return false;
			}
		}

		/**
		 * 刪除加價購商品
		 * @param  [type] $parentId       [description]
		 * @param  [type] $id             [description]
		 * @param  [type] $specifications [description]
		 * @return [type]                 [description]
		 */
		function rmAddProduct($parentId,$id,$specifications){
			$temp = $this->conn->GetRow($this->conn->Prepare("select * from ".$this->tableList." where parentId=? and shoppingCartId=? and productId=? and specifications=?"),array($parentId,$this->order["id"],$id,$specifications));
			if($temp){
				if($this->conn->Execute("DELETE FROM ".$this->tableList." where parentId='".$parentId."' and shoppingCartId='".$this->order["id"]."' and productId='".$id."' and specifications='".$specifications."'")){
					$this->message = $this->console->getMessage("DELETE_ADD_PRODUCT_COUNT_OK");
					return true;
				}else{
					$this->message = $this->console->getMessage("DELETE_ADD_PRODUCT_COUNT_ERROR");
					return false;
				};
			}else{
				$this->message = $this->console->getMessage("SHOOPING_CART_PRODUCT_NULL");
				return false;
			}
		}

		/**
		 * 庫存檢查
		 * @return [type] [description]
		 */
		function checkStock($rm=false){
			if(!$this->isStockMode){
				return true;
			}
			$this->message = '';
			$temp =  $this->conn->GetArray("select productId,specifications,SUM(count) as count from ".$this->tableList." where shoppingCartId='".$this->order["id"]."' group by productId,specifications");
			if($temp){
				foreach ($temp as $key => $value) {
					$productTemp = $this->product->getProduct($value["productId"]);
					if($value["count"] > $productTemp["stock"][array_search($value["specifications"], $productTemp["specificationsID"])]){
						$this->message .= $this->console->getMessage('PRODUCT_STOCK_IS_INSUFFICIENT',array($productTemp["name"])).'\n';
					}
				}
			}
			if($this->message){
				return false;
			}
			//扣除
			if($rm){
				foreach ($temp as $key => $value) {
					$productTemp = $this->product->getProduct($value["productId"]);
					$newCount = $productTemp["stock"][array_search($value["specifications"], $productTemp["specificationsID"])]-$value["count"];
					$this->product->setStock($value["productId"],$value["specifications"],$newCount);
				}
			}
			return true;
		}

		/**
		 * 限購數量檢查
		 * @return [type] [description]
		 */
		function checkMaxCount(){
			$temp = $this->getShoppingCartList();
			if($temp){
				foreach ($temp as $key => $value) {
					$productTemp = $this->product->getProduct($value["productId"]);
					$maxCount = $productTemp["maxCount"][array_search($value["specifications"], $productTemp["specificationsID"])];
					if(($value["count"] > $maxCount) && $maxCount){
						$this->message .= $this->console->getMessage("PRODUCT_MAX_COUNT",array($value["name"])).'\n';
					}
					if($value["addProductList"]){
						foreach ($value["addProductList"] as $keyAdd => $valueAdd) {
							$tempParent = $this->product->getProduct($valueAdd["parentId"]);
							$addMaxCount = $tempParent["addProductMaxCount"][array_search($valueAdd["specifications"], $tempParent["addProductSpecifications"])];
							if(($valueAdd["count"] > $addMaxCount) && $addMaxCount){
								$this->message .= $this->console->getMessage("ADD_PRODUCT_MAX_COUNT",array($valueAdd["name"])).'\n';
							}
						}
					}
				}
			}
			if($this->message){
				return false;
			}
			return true;
		}

		/**
		 * 結帳購物車
		 * @param string  $prefix 訂單編號前墜
		 * @param integer $length 訂單編號長度
		 * @param integer $start  訂單編號起始值
		 */
		function payBill($prefix=ORDER_PREFIX,$length=ORDER_SIZE,$start=1){
			$this->reloadCart();

			$this->conn->Execute("BEGIN;");

			switch ($this->order["shipmentMethod"]) {

				case shipmentMethodType::FAMI:									//超商取貨(綠界) 全家
				case shipmentMethodType::UNIMART:								//超商取貨(綠界) 統一超商
				case shipmentMethodType::HILIFE:								//超商取貨(綠界) 萊爾富
				case shipmentMethodType::FAMIC2C:								//超商取貨(綠界) 全家店到店
				case shipmentMethodType::UNIMARTC2C:							//超商取貨(綠界) 統一超商店到店
				case shipmentMethodType::HILIFEC2C:								//超商取貨(綠界) 萊爾富店到店
				case shipmentMethodType::FAMI_COLLECTION_Y:						//超商取貨付款(綠界) 全家
				case shipmentMethodType::UNIMART_COLLECTION_Y:					//超商取貨付款(綠界) 統一超商
				case shipmentMethodType::HILIFE_COLLECTION_Y:					//超商取貨付款(綠界) 萊爾富
				case shipmentMethodType::FAMIC2C_COLLECTION_Y:					//超商取貨付款(綠界) 全家店到店
				case shipmentMethodType::UNIMARTC2C_COLLECTION_Y:				//超商取貨付款(綠界) 統一超商店到店
				case shipmentMethodType::HILIFEC2C_COLLECTION_Y:				//超商取貨付款(綠界) 萊爾富店到店
					if(($this->order["total"]+$this->order["freight"])>20000){
						$this->message =  $this->console->getMessage('MONEY_MAX_20000');
						return false;
					}
					break;
			}
			
			//購物車內容
			if(!$this->getShoppingCartList()){
				$this->message = $this->console->getMessage('CAR_ITEM_IS_NULL_TO_PAY');
				return false;
			}

			//限購數量檢查
			if (!$this->checkMaxCount()){
				return false;
			}

			//庫存檢查
			if (!$this->checkStock(true)){
				return false;
			}

			//檢查付款方式
			if (!$this->checkPaymentMethod($this->order['paymentMethod'])){
				return false;
			}

			//檢查運送方式
			if (!$this->checkShipmentMethod($this->order['shipmentMethod'])){
				return false;
			}


			//南蠻堂特殊功能 多一個收件人多一次運費
			// if(is_array($_POST["ReceiverName"])){
			// 	if(count($_POST["ReceiverName"])>1){
			// 		$this->setFreight((count($_POST["ReceiverName"])-floor($this->order["total"]/$this->console->webSetting->getValue("freeFreightMoney")))*$this->console->webSetting->getValue("freight"));
			// 	}
			// }

			//扣除點數
			if((int)$this->order['usePoint']>0){
				if($this->member->setPoint(($this->order['usePoint']*-1),pointType::USE_POINT)){
					$this->conn->Execute($this->conn->Prepare("UPDATE ".$this->table." SET usePointStatus=1 where id=?"),array($this->order["id"]));
				}else{
					$this->message = $this->console->getMessage('SET_POINT_ERROR');
					return false;
				}
			}
			$this->updateOrder(array("paymentStatus" => paymentStatusType::NO,"shipmentStatus" =>shipmentStatusType::NO));

			//取得訂單編號起始值
			$allLanguage = $this->console->getLanguageArray("array");								//尋找所有語系中最大的編號
			$noLangTableName = str_replace("__".str_replace("-","_",$this->lang),"",$this->table);
			foreach ($allLanguage as $key => $value) {
				$table = $noLangTableName."__".str_replace("-","_",$key);
				$temp = $this->conn->GetRow("select *,REPLACE(orderNumber,'".$prefix."','') as _orderNumber from `".$table."` where orderNumber like '".$prefix."%' order by _orderNumber desc limit 1");
				if($temp && ($start < ((int)$temp["_orderNumber"]+1))){
					$start = (int)$temp["_orderNumber"]+1;
				}
			}
			$orderNumber = sprintf($prefix."%'.0".($length-strlen($prefix))."d", $start++);

			$this->conn->Execute("UPDATE ".$this->table." SET orderNumber='".$orderNumber."',step='2',create_date='".DATE."' where id='".$this->order['id']."'");

			$this->conn->Execute("COMMIT;");
			
			//發送訂單結帳完成信件
			$this->sendMail($orderNumber,orderSendMailType::ORDER_CHECKOUT_COMPLETED);

			return $orderNumber;
		}

		/**
		 * 金流呼叫
		 * @param  [type] $orderNumber [description]
		 * @return [type]              [description]
		 */
		function payment($orderNumber){
			$order = $this->getOrder($orderNumber);
			$orderList = $this->getOrderList($orderNumber);

			if(!$order || !$orderList){
				return false;
			}

			if($order["paymentStatus"] == paymentStatusType::OK){
				$this->message = $this->console->getMessage("ALREADY_PAY");
				return false;
			}

			switch ($order["paymentMethod"]) {
				case paymentMethodType::FACE_TO_FACE:										//面交
				case paymentMethodType::CASH_ON_DELIVERY:									//貨到付款
					break;

				case paymentMethodType::PHYSICAL_ATM_TRANSFER:								//實體ATM轉帳
					//發送匯款資訊郵件
					$this->sendMail($orderNumber,orderSendMailType::SEND_REMITTANCE_INFORMATION_MAIL);
					break;

				case paymentMethodType::PHYSICAL_ATM_TRANSFER_ECPAY:						//實體ATM轉帳(綠界)
				case paymentMethodType::INTERNET_ATM_TRANSFER_ECPAY:						//網路ATM轉帳(綠界)
				case paymentMethodType::ONLINE_CARD_ECPAY:									//線上刷卡(綠界)
				case paymentMethodType::CVS_ECPAY:											//超商代碼(綠界)
				case paymentMethodType::BARCODE_ECPAY:										//超商條碼(綠界)
					$ECPay = new ECPay($this->console->setting->getValue("ecpayMerchantID"),$this->console->setting->getValue("ecpayHashKey"),$this->console->setting->getValue("ecpayHashIV"));
					$ECPay->createOrder($order,$orderList);
					break;

				case paymentMethodType::CONVENIENCE_STORE_PICK_UP_PAYMENT_ECPAY:			//超商取貨付款(綠界)
					$this->shipment($orderNumber);
					break;

				case paymentMethodType::FISC_PAY:											//信用卡刷卡(財金公司國際)
					$fisc = new payFiscPay(
						$this->console->setting->getValue("fiscMerID"),
						$this->console->setting->getValue("fiscMerchantID"),
						$this->console->setting->getValue("fiscTerminalID")
					);
					$fisc->createOrder($order,$orderList);
					break;

				default:
					$this->message = $this->console->getMessage("PAYMENT_METHOD_ERROR");
					return false;
					break;
			}
			return true;
		}

		/**
		 * 物流呼叫
		 * @param  [type] $orderNumber [description]
		 * @return [type]              [description]
		 */
		function shipment($orderNumber,$isBG=false){
			$order = $this->getOrder($orderNumber);
			$orderList = $this->getOrderList($orderNumber);

			if(!$order || !$orderList){
				return false;
			}

	        $ECPayLog = new ECPayLog($this->console);
			// if($ECPayLog->getData("where RtnCode='300' and MerchantTradeNo like '".$orderNumber."%'")){
			// 	$this->message = $this->console->getMessage("ALREADY_SHIPMENT");
			// 	return false;
			// }

			switch ($order["shipmentMethod"]) {
				case shipmentMethodType::CUSTOM_SHIPMENT1:						//物流自訂1
				case shipmentMethodType::CUSTOM_SHIPMENT2:						//物流自訂2
				case shipmentMethodType::CUSTOM_SHIPMENT3:						//物流自訂3
				case shipmentMethodType::CUSTOM_SHIPMENT4:						//物流自訂4
				case shipmentMethodType::CUSTOM_SHIPMENT5:						//物流自訂5
				case shipmentMethodType::FACE_TO_FACE:							//面交
				case shipmentMethodType::MAILING:								//郵寄
					break;

				case shipmentMethodType::TCAT_BLACK_CAT:						//宅配(綠界) 黑貓
				case shipmentMethodType::ECAN_HOME_DELIVERY:					//宅配(綠界) 宅配通
				case shipmentMethodType::FAMI:									//超商取貨(綠界) 全家
				case shipmentMethodType::UNIMART:								//超商取貨(綠界) 統一超商
				case shipmentMethodType::HILIFE:								//超商取貨(綠界) 萊爾富
				case shipmentMethodType::FAMIC2C:								//超商取貨(綠界) 全家店到店
				case shipmentMethodType::UNIMARTC2C:							//超商取貨(綠界) 統一超商店到店
				case shipmentMethodType::HILIFEC2C:								//超商取貨(綠界) 萊爾富店到店
					if($order["paymentStatus"] != paymentStatusType::OK){
						$this->message = $this->console->getMessage("NOT_YET_PAY");
						return false;
					}
				case shipmentMethodType::FAMI_COLLECTION_Y:						//超商取貨付款(綠界) 全家
				case shipmentMethodType::UNIMART_COLLECTION_Y:					//超商取貨付款(綠界) 統一超商
				case shipmentMethodType::HILIFE_COLLECTION_Y:					//超商取貨付款(綠界) 萊爾富
				case shipmentMethodType::FAMIC2C_COLLECTION_Y:					//超商取貨付款(綠界) 全家店到店
				case shipmentMethodType::UNIMARTC2C_COLLECTION_Y:				//超商取貨付款(綠界) 統一超商店到店
				case shipmentMethodType::HILIFEC2C_COLLECTION_Y:				//超商取貨付款(綠界) 萊爾富店到店

					$ECPay = new ECPay($this->console->setting->getValue("ecpayMerchantID"),$this->console->setting->getValue("ecpayHashKey"),$this->console->setting->getValue("ecpayHashIV"));
					$temp = $ECPay->createShippingOrder($order,$orderList,$isBG);
					if(isset($temp["ErrorMessage"])){
						$this->message = $temp["ErrorMessage"];
						return false;
					}
					break;

				default:
					$this->message = $this->console->getMessage("SHIPMENT_METHOD_ERROR");
					return false;
					break;
			}
			return true;
		}

		/**
		 * 列印需要的單子
		 * @param  [type] $orderNumber [description]
		 * @return [type]              [description]
		 */
		function printBill($orderNumber){
			$order = $this->getOrder($orderNumber);
			$orderList = $this->getOrderList($orderNumber);

			if(!$order || !$orderList){
				return false;
			}

	        $ECPayLog = new ECPayLog($this->console);
	        $log = $ECPayLog->getData("where RtnCode='300' and type='shipment' and MerchantTradeNo like '".$orderNumber."%' order by create_date desc limit 1");
			if(!$log){
				$this->message = $this->console->getMessage("NOT_YET_SHIPMENT");
				return false;
			}
			$log = $log[0];

			$ECPay = new ECPay($this->console->setting->getValue("ecpayMerchantID"),$this->console->setting->getValue("ecpayHashKey"),$this->console->setting->getValue("ecpayHashIV"));

			switch ($order["shipmentMethod"]) {
				case shipmentMethodType::TCAT_BLACK_CAT:						//宅配(綠界) 黑貓
				case shipmentMethodType::ECAN_HOME_DELIVERY:					//宅配(綠界) 宅配通
				case shipmentMethodType::FAMI:									//超商取貨(綠界) 全家 B2C
				case shipmentMethodType::UNIMART:								//超商取貨(綠界) 統一超商 B2C
				case shipmentMethodType::HILIFE:								//超商取貨(綠界) 萊爾富 B2C
				case shipmentMethodType::FAMI_COLLECTION_Y:						//超商取貨付款(綠界) 全家 B2C
				case shipmentMethodType::UNIMART_COLLECTION_Y:					//超商取貨付款(綠界) 統一超商 B2C
				case shipmentMethodType::HILIFE_COLLECTION_Y:					//超商取貨付款(綠界) 萊爾富 B2C
					if($order["paymentStatus"] != paymentStatusType::OK){
						$this->message = $this->console->getMessage("NOT_YET_PAY");
						return false;
					}
					$ECPay->printTradeDoc($log["AllPayLogisticsID"]);
					break;

				case shipmentMethodType::FAMIC2C:								//超商取貨(綠界) 全家店到店
				case shipmentMethodType::FAMIC2C_COLLECTION_Y:					//超商取貨付款(綠界) 全家店到店
					$ECPay->printFamilyC2CBill($log["AllPayLogisticsID"],$log["CVSPaymentNo"]);
					break;

				case shipmentMethodType::UNIMARTC2C:							//超商取貨(綠界) 統一超商店到店
				case shipmentMethodType::UNIMARTC2C_COLLECTION_Y:				//超商取貨付款(綠界) 統一超商店到店
					$ECPay->printUnimartC2CBill($log["AllPayLogisticsID"],$log["CVSPaymentNo"],$log["CVSValidationNo"]);
					break;

				case shipmentMethodType::HILIFEC2C:								//超商取貨(綠界) 萊爾富店到店
				case shipmentMethodType::HILIFEC2C_COLLECTION_Y:				//超商取貨付款(綠界) 萊爾富店到店
					$ECPay->printHiLifeC2CBill($log["AllPayLogisticsID"],$log["CVSPaymentNo"]);
					break;


				default:
					$this->message = $this->console->getMessage("SHIPMENT_METHOD_ERROR");
					return false;
					break;
			}
			return true;
		}

		/**
		 * 選擇超商
		 * @param  [type] $orderNumber [description]
		 * @return [type]              [description]
		 */
		function cvsMap($orderNumber){
			$order = $this->conn->GetRow($this->conn->Prepare("select * from ".$this->table." where orderNumber=?"),array($orderNumber));

			if(!$order){
				return false;
			}

			switch ($order["shipmentMethod"]) {

				case shipmentMethodType::FAMI:									//超商取貨(綠界) 全家
				case shipmentMethodType::UNIMART:								//超商取貨(綠界) 統一超商
				case shipmentMethodType::HILIFE:								//超商取貨(綠界) 萊爾富
				case shipmentMethodType::FAMIC2C:								//超商取貨(綠界) 全家店到店
				case shipmentMethodType::UNIMARTC2C:							//超商取貨(綠界) 統一超商店到店
				case shipmentMethodType::HILIFEC2C:								//超商取貨(綠界) 萊爾富店到店
				case shipmentMethodType::FAMI_COLLECTION_Y:						//超商取貨付款(綠界) 全家
				case shipmentMethodType::UNIMART_COLLECTION_Y:					//超商取貨付款(綠界) 統一超商
				case shipmentMethodType::HILIFE_COLLECTION_Y:					//超商取貨付款(綠界) 萊爾富
				case shipmentMethodType::FAMIC2C_COLLECTION_Y:					//超商取貨付款(綠界) 全家店到店
				case shipmentMethodType::UNIMARTC2C_COLLECTION_Y:				//超商取貨付款(綠界) 統一超商店到店
				case shipmentMethodType::HILIFEC2C_COLLECTION_Y:				//超商取貨付款(綠界) 萊爾富店到店

					$ECPay = new ECPay($this->console->setting->getValue("ecpayMerchantID"),$this->console->setting->getValue("ecpayHashKey"),$this->console->setting->getValue("ecpayHashIV"));
					$ECPay->cvsMap($order);
					return true;
					break;
			}
			return false;
		}

		/**
		 * 設定運費
		 * @param [type] $freight [description]
		 */
		function setFreight($freight){
			$this->updateOrder(array("freight"=>$freight));
		}

		/**
		 * 取得名稱
		 * @param [type] $paymentMethod [description]
		 */
		function getPaymentTitle($paymentMethod=""){
			if($paymentMethod && !$this->checkPaymentMethod($paymentMethod)){
				return false;
			}
			$paymentSetting = new webSetting($this->console,PREFIX."payment_setting",$this->lang);
			$array = array(
				paymentMethodType::CASH_ON_DELIVERY => $this->console->getLabel("CASH_ON_DELIVERY"),
				paymentMethodType::PHYSICAL_ATM_TRANSFER => $this->console->getLabel("PHYSICAL_ATM_TRANSFER"),
				paymentMethodType::PHYSICAL_ATM_TRANSFER_ECPAY => $this->console->getLabel("PHYSICAL_ATM_TRANSFER_ECPAY"),
				paymentMethodType::INTERNET_ATM_TRANSFER_ECPAY => $this->console->getLabel("INTERNET_ATM_TRANSFER_ECPAY"),
				paymentMethodType::ONLINE_CARD_ECPAY => $this->console->getLabel("ONLINE_CARD_ECPAY"),
				paymentMethodType::CONVENIENCE_STORE_PICK_UP_PAYMENT_ECPAY => $this->console->getLabel("CONVENIENCE_STORE_PICK_UP_PAYMENT_ECPAY"),
				paymentMethodType::CVS_ECPAY => $this->console->getLabel("CVS_ECPAY"),
				paymentMethodType::BARCODE_ECPAY => $this->console->getLabel("BARCODE_ECPAY"),
				paymentMethodType::FACE_TO_FACE => $this->console->getLabel("FACE_TO_FACE"),
				paymentMethodType::FISC_PAY => $this->console->getLabel("FISC_PAY")
			);
			if($paymentMethod){
				return $array[$paymentMethod];
			}
			return $array;
		}

		/**
		 * 取得說明文字
		 * @param [type] $paymentMethod [description]
		 */
		function getPaymentText($paymentMethod){
			if(!$this->checkPaymentMethod($paymentMethod)){
				return false;
			}
			$paymentSetting = new webSetting($this->console,PREFIX."payment_setting",$this->lang);
			$array = array(
				paymentMethodType::CASH_ON_DELIVERY => $paymentSetting->getValue("cashOnDeliveryDetail"),
				paymentMethodType::PHYSICAL_ATM_TRANSFER => $paymentSetting->getValue("physicalATMTransferDetail"),
				paymentMethodType::PHYSICAL_ATM_TRANSFER_ECPAY => $paymentSetting->getValue("physicalATMTransferECPayDetail"),
				paymentMethodType::INTERNET_ATM_TRANSFER_ECPAY => $paymentSetting->getValue("internetATMTransferECPayDetail"),
				paymentMethodType::ONLINE_CARD_ECPAY => $paymentSetting->getValue("onlineCardECPayDetail"),
				paymentMethodType::CONVENIENCE_STORE_PICK_UP_PAYMENT_ECPAY => $paymentSetting->getValue("convenienceStorePickUpPaymentECPayDetail"),
				paymentMethodType::CVS_ECPAY => $paymentSetting->getValue("cvsECPayDetail"),
				paymentMethodType::BARCODE_ECPAY => $paymentSetting->getValue("barcodeECPayDetail"),
				paymentMethodType::FACE_TO_FACE => $paymentSetting->getValue("faceToFaceDetail"),
				paymentMethodType::FISC_PAY => $paymentSetting->getValue("payFiscPayDetail")
			);
			return $array[$paymentMethod];
		}

		/**
		 * 取得付款方式陣列
		 */
		function getPaymentMethodArray(){
			$paymentSetting = new webSetting($this->console,PREFIX."payment_setting",$this->lang);
			$array = array(
				paymentMethodType::CASH_ON_DELIVERY => $paymentSetting->getValue("cashOnDeliveryCheck"),
				paymentMethodType::PHYSICAL_ATM_TRANSFER => $paymentSetting->getValue("physicalATMTransferCheck"),
				paymentMethodType::PHYSICAL_ATM_TRANSFER_ECPAY => $paymentSetting->getValue("physicalATMTransferECPayCheck"),
				paymentMethodType::INTERNET_ATM_TRANSFER_ECPAY => $paymentSetting->getValue("internetATMTransferECPayCheck"),
				paymentMethodType::ONLINE_CARD_ECPAY => $paymentSetting->getValue("onlineCardECPayCheck"),
				paymentMethodType::CONVENIENCE_STORE_PICK_UP_PAYMENT_ECPAY => $paymentSetting->getValue("convenienceStorePickUpPaymentECPayCheck"),
				paymentMethodType::CVS_ECPAY => $paymentSetting->getValue("cvsECPayCheck"),
				paymentMethodType::BARCODE_ECPAY => $paymentSetting->getValue("barcodeECPayCheck"),
				paymentMethodType::FACE_TO_FACE => $paymentSetting->getValue("faceToFaceCheck"),
				paymentMethodType::FISC_PAY => $paymentSetting->getValue("payFiscPayCheck")
			);
			return $array;
		}

		/**
		 * 檢查付款方式是否開啟
		 * @param [type] $paymentMethod [description]
		 */
		function checkPaymentMethod($paymentMethod){
			$array = $this->getPaymentMethodArray();
			if(isset($array[$paymentMethod]) && $array[$paymentMethod]){
				return true;
			}else{
				$this->message = $this->console->getMessage("PAYMENT_METHOD_ERROR");
				return false;
			}
		}

		/**
		 * 設定付款方式
		 * @param [type] $paymentMethod [description]
		 */
		function setPaymentMethod($paymentMethod){
			if(!$this->checkPaymentMethod($paymentMethod)){
				$this->updateOrder(array("paymentMethod"=>''));
				return false;
			}
			$this->updateOrder(array("paymentMethod"=>$paymentMethod));
			return true;
		}

		/**
		 * 取得運送方式名稱
		 * @param [type] $shipmentMethod [description]
		 */
		function getShipmentTitle($shipmentMethod=""){
			if($shipmentMethod && !$this->checkShipmentMethod($shipmentMethod)){
				return false;
			}
			$array = array(
				shipmentMethodType::MAILING =>  $this->console->getLabel("MAILING"),
				shipmentMethodType::TCAT_BLACK_CAT =>  $this->console->getLabel("TCAT_BLACK_CAT"),
				shipmentMethodType::ECAN_HOME_DELIVERY =>  $this->console->getLabel("ECAN_HOME_DELIVERY"),
				shipmentMethodType::FAMI =>  $this->console->getLabel("FAMI"),
				shipmentMethodType::UNIMART =>  $this->console->getLabel("UNIMART"),
				shipmentMethodType::HILIFE =>  $this->console->getLabel("HILIFE"),
				shipmentMethodType::FAMIC2C =>  $this->console->getLabel("FAMIC2C"),
				shipmentMethodType::UNIMARTC2C =>  $this->console->getLabel("UNIMARTC2C"),
				shipmentMethodType::HILIFEC2C =>  $this->console->getLabel("HILIFEC2C"),
				shipmentMethodType::FAMI_COLLECTION_Y =>  $this->console->getLabel("FAMI_COLLECTION_Y"),
				shipmentMethodType::UNIMART_COLLECTION_Y =>  $this->console->getLabel("UNIMART_COLLECTION_Y"),
				shipmentMethodType::HILIFE_COLLECTION_Y =>  $this->console->getLabel("HILIFE_COLLECTION_Y"),
				shipmentMethodType::FAMIC2C_COLLECTION_Y =>  $this->console->getLabel("FAMIC2C_COLLECTION_Y"),
				shipmentMethodType::UNIMARTC2C_COLLECTION_Y =>  $this->console->getLabel("UNIMARTC2C_COLLECTION_Y"),
				shipmentMethodType::HILIFEC2C_COLLECTION_Y =>  $this->console->getLabel("HILIFEC2C_COLLECTION_Y"),
				shipmentMethodType::FACE_TO_FACE =>  $this->console->getLabel("FACE_TO_FACE"),
				shipmentMethodType::CUSTOM_SHIPMENT1 =>  $this->console->getLabel("CUSTOM_SHIPMENT1"),
				shipmentMethodType::CUSTOM_SHIPMENT2 =>  $this->console->getLabel("CUSTOM_SHIPMENT2"),
				shipmentMethodType::CUSTOM_SHIPMENT3 =>  $this->console->getLabel("CUSTOM_SHIPMENT3"),
				shipmentMethodType::CUSTOM_SHIPMENT4 =>  $this->console->getLabel("CUSTOM_SHIPMENT4"),
				shipmentMethodType::CUSTOM_SHIPMENT5 =>  $this->console->getLabel("CUSTOM_SHIPMENT5")
			);
			if($shipmentMethod){
				return $array[$shipmentMethod];
			}
			return $array;
		}

		/**
		 * 取得運送方式說明文字
		 * @param [type] $shipmentMethod [description]
		 */
		function getShipmentText($shipmentMethod){
			if(!$this->checkShipmentMethod($shipmentMethod)){
				return false;
			}
			$shipmentSetting = new webSetting($this->console,PREFIX."shipment_setting",$this->lang);
			$array = array(
				shipmentMethodType::MAILING => $shipmentSetting->getValue("mailingDetail"),
				shipmentMethodType::TCAT_BLACK_CAT => $shipmentSetting->getValue("tcatBlackCatDetail"),
				shipmentMethodType::ECAN_HOME_DELIVERY => $shipmentSetting->getValue("ecanHomeDeliveryDetail"),
				shipmentMethodType::FAMI => $shipmentSetting->getValue("famiDetail"),
				shipmentMethodType::UNIMART => $shipmentSetting->getValue("unimartDetail"),
				shipmentMethodType::HILIFE => $shipmentSetting->getValue("hilifeDetail"),
				shipmentMethodType::FAMIC2C => $shipmentSetting->getValue("famiC2CDetail"),
				shipmentMethodType::UNIMARTC2C => $shipmentSetting->getValue("unimartC2CDetail"),
				shipmentMethodType::HILIFEC2C => $shipmentSetting->getValue("hilifeC2CDetail"),
				shipmentMethodType::FAMI_COLLECTION_Y => $shipmentSetting->getValue("famiDetail"),
				shipmentMethodType::UNIMART_COLLECTION_Y => $shipmentSetting->getValue("unimartDetail"),
				shipmentMethodType::HILIFE_COLLECTION_Y => $shipmentSetting->getValue("hilifeDetail"),
				shipmentMethodType::FAMIC2C_COLLECTION_Y => $shipmentSetting->getValue("famiC2CDetail"),
				shipmentMethodType::UNIMARTC2C_COLLECTION_Y => $shipmentSetting->getValue("unimartC2CDetail"),
				shipmentMethodType::HILIFEC2C_COLLECTION_Y => $shipmentSetting->getValue("hilifeC2CDetail"),
				shipmentMethodType::FACE_TO_FACE => $shipmentSetting->getValue("faceToFaceDetail"),
				shipmentMethodType::CUSTOM_SHIPMENT1 => $shipmentSetting->getValue("customShipment1Detail"),
				shipmentMethodType::CUSTOM_SHIPMENT2 => $shipmentSetting->getValue("customShipment2Detail"),
				shipmentMethodType::CUSTOM_SHIPMENT3 => $shipmentSetting->getValue("customShipment3Detail"),
				shipmentMethodType::CUSTOM_SHIPMENT4 => $shipmentSetting->getValue("customShipment4Detail"),
				shipmentMethodType::CUSTOM_SHIPMENT5 => $shipmentSetting->getValue("customShipment5Detail")
			);
			return $array[$shipmentMethod];
		}

		/**
		 * 取得運送方式陣列
		 */
		function getShipmentMethodArray($paymentMethod=""){
			$shipmentSetting = new webSetting($this->console,PREFIX."shipment_setting",$this->lang);
			$array = array(
				//郵寄
				shipmentMethodType::MAILING => $shipmentSetting->getValue("mailingCheck"),
				//宅配(綠界) 黑貓
				shipmentMethodType::TCAT_BLACK_CAT => $shipmentSetting->getValue("tcatBlackCatCheck"),
				//宅配(綠界) 宅配通
				shipmentMethodType::ECAN_HOME_DELIVERY => $shipmentSetting->getValue("ecanHomeDeliveryCheck"),
				//超商取貨(綠界) 全家
				shipmentMethodType::FAMI => $shipmentSetting->getValue("famiPpurePickupCheck") && $shipmentSetting->getValue("famiCheck"),
				//超商取貨(綠界) 統一超商
				shipmentMethodType::UNIMART => $shipmentSetting->getValue("unimartPpurePickupCheck") && $shipmentSetting->getValue("unimartCheck"),
				//超商取貨(綠界) 萊爾富
				shipmentMethodType::HILIFE => $shipmentSetting->getValue("hilifePpurePickupCheck") && $shipmentSetting->getValue("hilifeCheck"),
				//超商取貨(綠界) 全家店到店
				shipmentMethodType::FAMIC2C => $shipmentSetting->getValue("famiC2CPpurePickupCheck") && $shipmentSetting->getValue("famiC2CCheck"),
				//超商取貨(綠界) 統一超商店到店
				shipmentMethodType::UNIMARTC2C => $shipmentSetting->getValue("unimartC2CPpurePickupCheck") && $shipmentSetting->getValue("unimartC2CCheck"),
				//超商取貨(綠界) 萊爾富店到店
				shipmentMethodType::HILIFEC2C => $shipmentSetting->getValue("hilifeC2CPpurePickupCheck") && $shipmentSetting->getValue("hilifeC2CCheck"),
				//超商取貨付款(綠界) 全家
				shipmentMethodType::FAMI_COLLECTION_Y => $shipmentSetting->getValue("famiPickUpPaymentCheck") && $shipmentSetting->getValue("famiCheck"),
				//超商取貨付款(綠界) 統一超商
				shipmentMethodType::UNIMART_COLLECTION_Y => $shipmentSetting->getValue("unimartPickUpPaymentCheck") && $shipmentSetting->getValue("unimartCheck"),
				//超商取貨付款(綠界) 萊爾富
				shipmentMethodType::HILIFE_COLLECTION_Y => $shipmentSetting->getValue("hilifePickUpPaymentCheck") && $shipmentSetting->getValue("hilifeCheck"),
				//超商取貨付款(綠界) 全家店到店
				shipmentMethodType::FAMIC2C_COLLECTION_Y => $shipmentSetting->getValue("famiC2CPickUpPaymentCheck") && $shipmentSetting->getValue("famiC2CCheck"),
				//超商取貨付款(綠界) 統一超商店到店
				shipmentMethodType::UNIMARTC2C_COLLECTION_Y => $shipmentSetting->getValue("unimartC2CPickUpPaymentCheck") && $shipmentSetting->getValue("unimartC2CCheck"),
				//超商取貨付款(綠界) 萊爾富店到店
				shipmentMethodType::HILIFEC2C_COLLECTION_Y => $shipmentSetting->getValue("hilifeC2CPickUpPaymentCheck") && $shipmentSetting->getValue("hilifeC2CCheck"),
				//面交
				shipmentMethodType::FACE_TO_FACE => $shipmentSetting->getValue("faceToFaceCheck"),
				shipmentMethodType::CUSTOM_SHIPMENT1 => $shipmentSetting->getValue("customShipment1Check"),
				shipmentMethodType::CUSTOM_SHIPMENT2 => $shipmentSetting->getValue("customShipment2Check"),
				shipmentMethodType::CUSTOM_SHIPMENT3 => $shipmentSetting->getValue("customShipment3Check"),
				shipmentMethodType::CUSTOM_SHIPMENT4 => $shipmentSetting->getValue("customShipment4Check"),
				shipmentMethodType::CUSTOM_SHIPMENT5 => $shipmentSetting->getValue("customShipment5Check")
			);
			if($paymentMethod){
				switch ($paymentMethod) {
					case paymentMethodType::FACE_TO_FACE:										//面交
						$array = array(shipmentMethodType::FACE_TO_FACE => $shipmentSetting->getValue("faceToFaceCheck"));
						break;
					case paymentMethodType::CASH_ON_DELIVERY:									//貨到付款
						unset(
							$array[shipmentMethodType::TCAT_BLACK_CAT],
							$array[shipmentMethodType::ECAN_HOME_DELIVERY],
							$array[shipmentMethodType::FAMI],
							$array[shipmentMethodType::UNIMART],
							$array[shipmentMethodType::HILIFE],
							$array[shipmentMethodType::FAMIC2C],
							$array[shipmentMethodType::UNIMARTC2C],
							$array[shipmentMethodType::HILIFEC2C],
							$array[shipmentMethodType::FACE_TO_FACE],
							$array[shipmentMethodType::FAMI_COLLECTION_Y],
							$array[shipmentMethodType::UNIMART_COLLECTION_Y],
							$array[shipmentMethodType::HILIFE_COLLECTION_Y],
							$array[shipmentMethodType::FAMIC2C_COLLECTION_Y],
							$array[shipmentMethodType::UNIMARTC2C_COLLECTION_Y],
							$array[shipmentMethodType::HILIFEC2C_COLLECTION_Y]
						);
						break;

					case paymentMethodType::FISC_PAY:											//信用卡刷卡(財金公司國際)
					case paymentMethodType::PHYSICAL_ATM_TRANSFER:								//實體ATM轉帳
					case paymentMethodType::PHYSICAL_ATM_TRANSFER_ECPAY:						//實體ATM轉帳(綠界)
					case paymentMethodType::INTERNET_ATM_TRANSFER_ECPAY:						//網路ATM轉帳(綠界)
					case paymentMethodType::ONLINE_CARD_ECPAY:									//線上刷卡(綠界)
					case paymentMethodType::CVS_ECPAY:											//超商代碼(綠界)
					case paymentMethodType::BARCODE_ECPAY:										//超商條碼(綠界)
						unset(
							$array[shipmentMethodType::FAMI_COLLECTION_Y],
							$array[shipmentMethodType::UNIMART_COLLECTION_Y],
							$array[shipmentMethodType::HILIFE_COLLECTION_Y],
							$array[shipmentMethodType::FAMIC2C_COLLECTION_Y],
							$array[shipmentMethodType::UNIMARTC2C_COLLECTION_Y],
							$array[shipmentMethodType::HILIFEC2C_COLLECTION_Y]
						);
						break;

					case paymentMethodType::CONVENIENCE_STORE_PICK_UP_PAYMENT_ECPAY:			//超商取貨付款(綠界)
						unset(
							$array[shipmentMethodType::MAILING],
							$array[shipmentMethodType::TCAT_BLACK_CAT],
							$array[shipmentMethodType::ECAN_HOME_DELIVERY],
							$array[shipmentMethodType::FAMI],
							$array[shipmentMethodType::UNIMART],
							$array[shipmentMethodType::HILIFE],
							$array[shipmentMethodType::FAMIC2C],
							$array[shipmentMethodType::UNIMARTC2C],
							$array[shipmentMethodType::HILIFEC2C],
							$array[shipmentMethodType::FACE_TO_FACE],
							$array[shipmentMethodType::CUSTOM_SHIPMENT1],
							$array[shipmentMethodType::CUSTOM_SHIPMENT2],
							$array[shipmentMethodType::CUSTOM_SHIPMENT3],
							$array[shipmentMethodType::CUSTOM_SHIPMENT4],
							$array[shipmentMethodType::CUSTOM_SHIPMENT5]
						);
						break;
				}
			}
			return $array;
		}

		/**
		 * 檢查運送方式是否開啟
		 * @param [type] $shipmentMethod [description]
		 */
		function checkShipmentMethod($shipmentMethod){
			$array = $this->getShipmentMethodArray();
			if(isset($array[$shipmentMethod]) && $array[$shipmentMethod]){
				return true;
			}else{
				$this->message = $this->console->getMessage("SHIPMENT_METHOD_ERROR");
				return false;
			}
		}

		/**
		 * 設定運送方式
		 * @param [type] $shipmentMethod [description]
		 */
		function setShipmentMethod($shipmentMethod){
			if(!$this->checkShipmentMethod($shipmentMethod)){
				$this->updateOrder(array("shipmentMethod"=>''));
				return false;
			}
			$this->updateOrder(array("shipmentMethod"=>$shipmentMethod));
			return true;
		}

		/**
		 * 設定紅利點數
		 * @param [type] $v1 每 v1 元
		 * @param [type] $v2 得到 v2 點
		 * @param [type] $v3 每 v3 點
		 * @param [type] $v4 可折 v4 元
		 */
		function setPoint($v1,$v2,$v3,$v4){
			$this->pointSetting = array($v1,$v2,$v3,$v4);
		}

		/**
		 * 折扣
		 * @param float $deshprice 0~1 
		 */
		function setDeshprice($deshprice=1.0){
			if(is_numeric($deshprice) && $deshprice<=1 && $deshprice>0){
				$this->updateOrder(array("deshprice"=>$deshprice));
			}else{
				$this->updateOrder(array("deshprice"=>1));
			}
		}

		/**
		 * 設定表單資料
		 * @param [type] $data [description]
		 * @param string  $checkArray    合法key，選填
		 * @param string  $requiredArray 必填key，選填
		 * @return [type]          		 失敗 false，成功true，若有必填欄位未填回傳未填的key陣列
		 */
		function setFormData($data,$checkArray='',$requiredArray=array()){
			$data = $this->checkData($data,$checkArray);
			if($temp = $this->requiredData($data,$requiredArray)){
				$this->message = $this->console->getMessage('REQURED_NULL');
				return $temp;			
			}
			$this->updateOrder(array("formData"=>json_encode($data,JSON_UNESCAPED_UNICODE)));
		}

		/**
		 * 取得表單資料
		 * @return [type] [description]
		 */
		function getFormData(){
			$temp = $this->getShoppingCart("formData");
			if($temp){
				return json_decode($temp,true);
			}
			return false;
		}

		/**
		 * 取得購物車資訊
		 * @return [type] [description]
		 */
		function getShoppingCart($key=''){
			$this->reloadCart();
			if($key && isset($this->order[$key])){
				return $this->order[$key];
			}
			return $this->order;
		}

		/**
		 * 取得購物車內容資訊
		 * @return [type] [description]
		 */
		function getShoppingCartList(){
			// $this->reloadCart();
			$temp = $this->conn->GetArray("select * from ".$this->tableList." where shoppingCartId='".$this->order["id"]."' and parentId IS NULL");
			if($temp){
				foreach ($temp as $key => $value) {
					$temp[$key]["addProductList"] = $this->conn->GetArray("select * from ".$this->tableList." where shoppingCartId='".$this->order["id"]."' and parentId='".$value["productId"]."'");
					$temp[$key]["onlineProduct"] = $this->product->getProduct($value["productId"],true);
				}
				$temp[0]["listCount"] = $this->getShoppingCartListCount();
			}
			return $temp;
		}

		/**
		 * 取得購物車商品數量(不含加價購)
		 * @return [type] [description]
		 */
		function getShoppingCartListCount(){
			$temp = $this->conn->GetRow("select sum(count) as count from ".$this->tableList." where shoppingCartId='".$this->order["id"]."' and parentId IS NULL");
			return $temp["count"] ?: 0;
		}

		/**
		 * 更新購物車資訊
		 * @param  [type] $data [description]
		 * @return [type]       [description]
		 */
		function updateOrder($data){
			$this->autoAddKey($data);
			$this->conn->AutoExecute($this->table,$data,"UPDATE","id='".$this->order["id"]."'");
			$this->order = $this->conn->GetRow("select * from ".$this->table." where step='1' and id='".$this->order["id"]."'");
		}

	    /**
	     * 設定console
	     * @param Mtsung/main $console 
	     */
	    function setConsole($console){
	    	$this->console = $console;
	    }




		/**
		 * 寄送email
		 * @param  [type] $orderNumber   	
		 * @return [type]       			失敗為false
		 */
		function sendMail($orderNumber,$mailType){
			//自訂欄位
			$tempField = new dataList($this->console,PREFIX."orderField","");
			if($tempSystem = $tempField->getData()){
				$tempSystem = $tempSystem[0];
				foreach (array(
								"dataName",
								"dataType",
								"dataKey",
								"dataCount",
								"dataFa",
								"dataRequired",
								"dataOption",
							) as $key => $value) {
						$tempSystem[$value] = explode("|__|", $tempSystem[$value]);
				}
				
				if(is_array($tempSystem["dataOption"])){
					foreach ($tempSystem["dataOption"] as $key => $value) {
						$tempSystem["dataOption"][$key] = explode(",", $value);
					}
				}

				$data["otherField"] = $tempSystem;

				//必填欄位
				foreach ($data["otherField"]["dataKey"] as $key => $value) {
					if($data["otherField"]["dataRequired"][$key]){
						$data["otherField"]["dataRequiredKey"][] = $value;
					}
				}
			}


			$order = $this->getOrder($orderNumber);
			$orderList = $this->orderListReload($this->getOrderList($orderNumber));

			if($order){
				$order["formData"] = json_decode($order["formData"],true);
				$email[] = $order["formData"]["BuyEmail"];
				// $email[] = $order["formData"]["ReceiverEmail"];
				$email = implode(",",$email);

				switch ($mailType) {
					case orderSendMailType::ORDER_CHECKOUT_COMPLETED://訂單結帳完成
						$templates = mailTemplate::ORDER_CHECKOUT_COMPLETED;
						$title = $this->console->getMessage('ORDER_CHECKOUT_COMPLETED');
						break;

					case orderSendMailType::SEND_REMITTANCE_INFORMATION_MAIL://發送匯款資訊郵件
						$templates = mailTemplate::SEND_REMITTANCE_INFORMATION_MAIL;
						$title = $this->console->getMessage('SEND_REMITTANCE_INFORMATION_MAIL');
						break;

					case orderSendMailType::PAYMENT_COMPLETED://付款完成
						$templates = mailTemplate::PAYMENT_COMPLETED;
						$title = $this->console->getMessage('PAYMENT_COMPLETED');
						break;

					case orderSendMailType::SHIPPED://已出貨
						$templates = mailTemplate::SHIPPED;
						$title = $this->console->getMessage('SHIPPED');
						break;

					case orderSendMailType::ORDER_DATA_RECEIVED://已收到訂單資料
						$templates = mailTemplate::ORDER_DATA_RECEIVED;
						$title = $this->console->getMessage('ORDER_DATA_RECEIVED');
						break;

					// case orderSendMailType::THE_GOODS_HAVE_BEEN_SENT_TO_THE_LOGISTICS_CENTER://商品已送至物流中心
					// 	$templates = mailTemplate::THE_GOODS_HAVE_BEEN_SENT_TO_THE_LOGISTICS_CENTER;
					// 	$title = $this->console->getMessage('THE_GOODS_HAVE_BEEN_SENT_TO_THE_LOGISTICS_CENTER');
					// 	break;

					case orderSendMailType::GOODS_HAVE_BEEN_DELIVERED_TO_THE_STORE://商品已送達門市
						$templates = mailTemplate::GOODS_HAVE_BEEN_DELIVERED_TO_THE_STORE;
						$title = $this->console->getMessage('GOODS_HAVE_BEEN_DELIVERED_TO_THE_STORE');
						break;

					case orderSendMailType::SUCCESSFUL_CUSTOMER_PICKUP://消費者成功取件
						$templates = mailTemplate::SUCCESSFUL_CUSTOMER_PICKUP;
						$title = $this->console->getMessage('SUCCESSFUL_CUSTOMER_PICKUP');
						break;

					case orderSendMailType::CONSUMERS_DID_NOT_PICK_UP_THE_GOODS_FOR_SEVEN_DAYS://消費者七天未取件
						$templates = mailTemplate::CONSUMERS_DID_NOT_PICK_UP_THE_GOODS_FOR_SEVEN_DAYS;
						$title = $this->console->getMessage('CONSUMERS_DID_NOT_PICK_UP_THE_GOODS_FOR_SEVEN_DAYS');
						break;

					// case orderSendMailType::COMING_SOON://即將出貨郵件
					// 	$templates = mailTemplate::COMING_SOON;
					// 	$title = $this->console->getMessage('COMING_SOON');
					// 	break;
				}

				$paymentSetting = new webSetting($this->console,PREFIX."payment_setting",$this->lang);

				$data["mailTitle"] = $this->console->webSetting->getValue("webTitle").'-'.$title;
				$data["webUrl"] = HTTP_PATH;
				$data["webName"] = $this->console->webSetting->getValue("webTitle");
				$data["webEmail"] = $this->console->setting->getValue("senderEmail");
				$data["ATMMemo"] = $paymentSetting->getValue("physicalATMTransferMemo");
				$data["shipmentTitle"] = $this->getShipmentTitle();
				$data["paymentTitle"] = $this->getPaymentTitle();
		        $ECPayLog = new ECPayLog($this->console);
		        $data["orderECPayLog"] = $ECPayLog->getData("where RtnCode='300' and type='shipment' and MerchantTradeNo like '".$data["order"]["orderNumber"]."%' order by create_date desc limit 1")[0];
				$member = $this->member->getUser($order["memberId"]);

				$mail = new phpMailer($this->console);
				$mail->setMailTitle($title);
				$mail->setMailAddress($email);
				$mail->setMailBody($templates,array("console" => $this->console,"data" => $data,"member" => $member ,"order" => $order , "orderList" => $orderList));
				return $mail->sendMail('','');
			}else{
				return false;
			}
		}

	    /**
	     * 取得訂單資訊
	     * @param  [type] $orderNumber [description]
	     * @return [type]              [description]
	     */
		function getOrder($orderNumber){
			return $this->conn->GetRow($this->conn->Prepare("select * from ".$this->table." where step>1 and orderNumber=?"),array($orderNumber));
		}

		/**
		 * 取得訂單內容
		 * @param  [type] $orderNumber 	  [description]
		 * @return [type]                 [description]
		 */
		function getOrderList($orderNumber){
			$id = $this->conn->GetRow($this->conn->Prepare("select * from ".$this->table." where step>1 and orderNumber=?"),array($orderNumber))[0];
			return $this->conn->GetArray($this->conn->Prepare("select * from ".$this->tableList." where shoppingCartId=?"),array($id));
		}

		/**
		 * 訂單內容整理(加價購放到addList內)
		 * @param  [type] $orderList 	  [description]
		 * @return [type]                 [description]
		 */
		function orderListReload($orderList){
			$temp = $isAdd = array();
			foreach ($orderList as $key => $value) {
				if($value["parentId"]){
					$isAdd[] = $value;
				}else{
					$temp[$value["productId"]."_".$value["specifications"]] = $value;
				}
			}
			foreach ($isAdd as $key => $value) {
				foreach ($temp as $keyTemp => $valueTemp) {
					if(explode("_", $keyTemp)[0]==$value["parentId"]){
						$temp[$keyTemp]["addList"][] = $value;
					}
					continue;
				}
			}
			if($temp = array_values($temp)){
				foreach ($temp as $key => $value) {
					$temp[$key]["onlineProduct"] = $this->product->getProduct($value["productId"]);
				}
			}
			return $temp;
		}

	    /**
	     * 付款成功
	     * @param  [type] $orderNumber [description]
	     * @return [type]              [description]
	     */
		function paymentIsOk($orderNumber){
			$this->conn->Execute($this->conn->Prepare("UPDATE ".$this->table." SET step='3',update_user=?,update_date=?,paymentStatus=?,payment_date=? where orderNumber=?"),array($this->getUpdateUser(),DATE,paymentStatusType::OK,DATE,$orderNumber));
			$this->sendMail($orderNumber,orderSendMailType::PAYMENT_COMPLETED);
		}

	    /**
	     * 已出貨
	     * @param  [type] $orderNumber [description]
	     * @return [type]              [description]
	     */
		function shipmentIsOk($orderNumber){
			$this->conn->Execute($this->conn->Prepare("UPDATE ".$this->table." SET step='3',update_user=?,update_date=?,shipmentStatus=?,shipment_date=? where orderNumber=?"),array($this->getUpdateUser(),DATE,shipmentStatusType::OK,DATE,$orderNumber));
			$this->sendMail($orderNumber,orderSendMailType::SHIPPED);
		}

	    /**
	     * 修改付款狀態
	     * @param  [type] $orderNumber [description]
	     * @return [type]              [description]
	     */
		function setPayment($orderNumber,$status){
			$date = NULL;
			if($status==paymentStatusType::OK){
				$this->paymentIsOk($orderNumber);
				return true;
			}
			$this->conn->Execute($this->conn->Prepare("UPDATE ".$this->table." SET step='3',update_user=?,update_date=?,paymentStatus=?,payment_date=? where orderNumber=?"),array($this->getUpdateUser(),DATE,$status,$date,$orderNumber));
		}

	    /**
	     * 修改出貨狀態
	     * @param  [type] $orderNumber [description]
	     * @return [type]              [description]
	     */
		function setShipment($orderNumber,$status){
			$date = NULL;
			if($status==shipmentStatusType::OK){
				$this->shipmentIsOk($orderNumber);
				return true;
			}
			$this->conn->Execute($this->conn->Prepare("UPDATE ".$this->table." SET step='3',update_user=?,update_date=?,shipmentStatus=?,shipment_date=? where orderNumber=?"),array($this->getUpdateUser(),DATE,$status,$date,$orderNumber));
		}

		/**
		 * 扣除使用的點數
		 * @param  [type] $orderNumber [description]
		 * @return [type]              [description]
		 */
		function deductionPoint($orderNumber){
			$data = $this->conn->GetRow($this->conn->Prepare("select * from ".$this->table." where orderNumber=?"),array($orderNumber));
				
			if ($data["memberId"] && $data["usePointStatus"]=='0'){
				$this->member->setPoint(($data['usePoint']*-1),pointType::USE_POINT,$data["memberId"]);
				$this->conn->Execute($this->conn->Prepare("UPDATE ".$this->table." SET usePointStatus=1,update_user=?,update_date=? where orderNumber=?"),array($this->getUpdateUser(),DATE,$orderNumber));
			}
			return true;
		}

		/**
		 * 補回使用的點數
		 * @param  [type] $orderNumber [description]
		 * @return [type]              [description]
		 */
		function replacementPoint($orderNumber){
			$data = $this->conn->GetRow($this->conn->Prepare("select * from ".$this->table." where orderNumber=?"),array($orderNumber));
				
			if ($data["memberId"] && $data["usePointStatus"]=='1'){
				$this->member->setPoint($data['usePoint'],pointType::REPLACEMENT_POINT,$data["memberId"]);
				$this->conn->Execute($this->conn->Prepare("UPDATE ".$this->table." SET usePointStatus=0,update_user=?,update_date=? where orderNumber=?"),array($this->getUpdateUser(),DATE,$orderNumber));
			}
			return true;
		}

		/**
		 * 發放點數
		 * @param  [type] $orderNumber [description]
		 * @return [type]              [description]
		 */
		function payPoint($orderNumber){
			$data = $this->conn->GetRow($this->conn->Prepare("select * from ".$this->table." where orderNumber=?"),array($orderNumber));
				
			if ($data["memberId"] && $data["getPointStatus"]!='1'){
				$this->member->setPoint($data['getPoint'],pointType::GET_POINT,$data["memberId"]);
				$this->conn->Execute($this->conn->Prepare("UPDATE ".$this->table." SET getPointStatus=1,update_user=?,update_date=? where orderNumber=?"),array($this->getUpdateUser(),DATE,$orderNumber));
			}
			return true;
		}
		
		/**
		 * 回收點數
		 * @param  [type] $orderNumber [description]
		 * @return [type]              [description]
		 */
		function backPoint($orderNumber){
			$data = $this->conn->GetRow($this->conn->Prepare("select * from ".$this->table." where orderNumber=?"),array($orderNumber));
				
			if ($data["memberId"] && $data["getPointStatus"]=='1'){
				$this->member->setPoint(($data['getPoint']*-1),pointType::BACK_POINT,$data["memberId"]);
				$this->conn->Execute($this->conn->Prepare("UPDATE ".$this->table." SET getPointStatus=0,update_user=?,update_date=? where orderNumber=?"),array($this->getUpdateUser(),DATE,$orderNumber));
			}
			return true;
		}

		/**
		 * 刪除訂單
		 * @param  [type] $id [description]
		 * @return [type]     [description]
		 */
		function rmData($id){
			$list = new center($this->console,$this->table.'_list',$this->lang);
			$deleteID = (is_array($id)) ? implode(",",$id) : $id;

			$temp = $list->getData("where shoppingCartId in (".$deleteID.")");
			if($temp){
				foreach ($temp as $key => $value) {
					$list->rmData($value["id"]);
				}
			}
			parent::rmData($id);
			return true;
		}
	}
}