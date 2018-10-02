<?php


/**
 * 一些const 
 * MTsung by 20180822
 */
namespace MTsung{

	/**
	 * 登入
	 */
	abstract class logType{
		const JOIN = 0;// 註冊
		const LOGIN = 1;// 登入
		const SOCIAL_LOGIN = 2;// 社群登入
		const LOGOUT = 3;// 登出
		const CHECK_EMAIL = 4;// 信件認證
		const FORGET = 5;// 忘記密碼
		const DELETE = 6;// 刪除會員
		const ACCOUNT_ERROR = 7;// 7帳號錯誤
		const PASSWORD_ERROR = 8;// 8密碼錯誤
		const VERIFCODE_ERROR = 9;// 9驗證碼失敗
	}

	/**
	 * 點數
	 */
	abstract class pointType{
		const GET_POINT = 0;// 取得
		const USE_POINT = 1;// 使用
		const BACK_POINT = 2;// 回收
		const REPLACEMENT_POINT = 3;// 補發
	}

	/**
	 * email認證
	 */
	abstract class emailCheckType{
		const IS_NO_CHECK = -1;//不用驗證
		const CHECK_NO = 0;//尚未驗證
		const CHECK_OK = 1;//已驗證
	}

	/**
	 * 付款方式
	 */
	abstract class paymentMethodType{
		const CASH_ON_DELIVERY = 1;//貨到付款
		const PHYSICAL_ATM_TRANSFER = 2;//實體ATM轉帳
		const PHYSICAL_ATM_TRANSFER_ECPAY = 3;//實體ATM轉帳(綠界)
		const INTERNET_ATM_TRANSFER_ECPAY = 4;//網路ATM轉帳(綠界)
		const ONLINE_CARD_ECPAY = 5;//線上刷卡(綠界)
		const CONVENIENCE_STORE_PICK_UP_PAYMENT_ECPAY = 6;//超商取貨付款(綠界)
		const CVS_ECPAY = 7;//超商代碼(綠界)
		const BARCODE_ECPAY = 8;//超商條碼(綠界)
	}

	/**
	 * 付款狀態
	 */
	abstract class paymentStatusType{
		const NO = 1;//未付款
		const OK = 2;//已付款
		const REFUND = 3;//退款中
	}

	/**
	 * 運送方式
	 */
	abstract class shipmentMethodType{
		const MAILING = 1;//郵寄
		const TCAT_BLACK_CAT = 2;//宅配(綠界) 黑貓
		const ECAN_HOME_DELIVERY = 3;//宅配(綠界) 宅配通
		const FAMI = 4;//超商取貨(綠界) 全家
		const UNIMART = 5;//超商取貨(綠界) 統一超商
		const HILIFE = 6;//超商取貨(綠界) 萊爾富
		const FAMIC2C = 7;//超商取貨(綠界) 全家店到店
		const UNIMARTC2C = 8;//超商取貨(綠界) 統一超商店到店
		const HILIFEC2C = 9;//超商取貨(綠界) 萊爾富店到店

		const FAMI_COLLECTION_Y = 10;//超商取貨付款(綠界) 全家
		const UNIMART_COLLECTION_Y = 11;//超商取貨付款(綠界) 統一超商
		const HILIFE_COLLECTION_Y = 12;//超商取貨付款(綠界) 萊爾富
		const FAMIC2C_COLLECTION_Y = 13;//超商取貨付款(綠界) 全家店到店
		const UNIMARTC2C_COLLECTION_Y = 14;//超商取貨付款(綠界) 統一超商店到店
		const HILIFEC2C_COLLECTION_Y = 15;//超商取貨付款(綠界) 萊爾富店到店
	}

	/**
	 * 運送狀態
	 */
	abstract class shipmentStatusType{
		const NO = 1;//未出貨
		const OK = 2;//已出貨
		const _RETURN = 3;//退貨中
	}

	/**
	 * 訂單信件type
	 */
	abstract class orderSendMailType{
		const ORDER_CHECKOUT_COMPLETED = 1;//訂單結帳完成
		const SEND_REMITTANCE_INFORMATION_MAIL = 2;//發送匯款資訊郵件
		const PAYMENT_COMPLETED = 3;//付款完成
		const SHIPPED = 4;//已出貨
		const ORDER_DATA_RECEIVED = 5;//已收到訂單資料
		const THE_GOODS_HAVE_BEEN_SENT_TO_THE_LOGISTICS_CENTER = 6;//商品已送至物流中心
		const GOODS_HAVE_BEEN_DELIVERED_TO_THE_STORE = 7;//商品已送達門市
		const SUCCESSFUL_CUSTOMER_PICKUP = 8;//消費者成功取件
		const CONSUMERS_DID_NOT_PICK_UP_THE_GOODS_FOR_SEVEN_DAYS = 9;//消費者七天未取件
		const COMING_SOON = 10;//即將出貨郵件
	}

	/**
	 * 信件樣板檔案
	 */
	abstract class mailTemplate{
		const ORDER_CHECKOUT_COMPLETED = "order_checkout_completed.html";//訂單結帳完成
		const SEND_REMITTANCE_INFORMATION_MAIL = "send_remittance_information_mail.html";//發送匯款資訊郵件
		const PAYMENT_COMPLETED = "payment_completed.html";//付款完成
		const SHIPPED = "shipped.html";//已出貨
		const ORDER_DATA_RECEIVED = "order_data_received.html";//已收到訂單資料
		// const THE_GOODS_HAVE_BEEN_SENT_TO_THE_LOGISTICS_CENTER = "";//商品已送至物流中心
		const GOODS_HAVE_BEEN_DELIVERED_TO_THE_STORE = "goods_have_been_delivered_to_the_store.html";//商品已送達門市
		const SUCCESSFUL_CUSTOMER_PICKUP = "successful_customer_pickup.html";//消費者成功取件
		const CONSUMERS_DID_NOT_PICK_UP_THE_GOODS_FOR_SEVEN_DAYS = "consumers_did_not_pick_up_the_goods_for_seven_days.html";//消費者七天未取件
		// const COMING_SOON = "";//即將出貨郵件

		const BACKUP = "backup.html";//網站備份
		const MAIL_PASSWORD_NOTICE = "mail_password_notice.html";//忘記密碼
		const MAIL_MEMBER_NOTICE = "mail_member_notice.html";//帳號開通確認信
	}
}
