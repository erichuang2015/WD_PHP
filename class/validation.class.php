<?php


/**
 * 表單驗證
 * MTsung by 20180703
 */
namespace MTsung{

	class validation{
		
		function __construct(){

		}


		/**
		 * 驗證email，檢查domain有沒有設定MX，沒設定無法收發信
		 * @param  [type]  $email [description]
		 * @return boolean        [description]
		 */
		public function isEmail($email){
		    trim($email);
		    $temp = explode('@',$email);
		    if(count($temp)==2){
			    $name = $temp[0];
			    $domain = $temp[1];

			    if(!$name){
			    	return false;
				}else{
			    	return checkdnsrr($domain, 'MX');
				}
		    }else{
			    return false;
		    }
		}

		/**
		 * 驗證合法密碼
		 * @param  [type]  $pwd [description]
		 * @return boolean      [description]
		 */
		public function isPassword($pwd){
			return  ($pwd != "") &&
					(strlen($pwd) > 7) &&
					(strlen($pwd) < 255);

		}

		/**
		 * 驗證合法帳號
		 * @param  [type]  $account [description]
		 * @return boolean          [description]
		 */
		public function isAccount($account){
			return  ($account != "") &&
					(!preg_match("/[^a-zA-Z0-9_@.]/", $account)) &&
					(strlen($account) > 4) &&
					(strlen($account) < 50);
		}

		/**
		 * 驗證合法身分證
		 * @param  [type]  $ID [description]
		 * @return boolean     [description]
		 */
		public function isIDCard($ID){
		    $head = "ABCDEFGHJKLMNPQRSTUVXYWZIO";
		    $A1 = array(1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 3, 3, 3, 3, 3, 3);
		    $A2 = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 0, 1, 2, 3, 4, 5);
		    $Mx = array(0, 8, 7, 6, 5, 4, 3, 2, 1, 1);
		    $i = strrpos($head, $ID[0]);
		    if((strlen($ID) != 10) || ($i == -1)){
		    	return false;
		    }

		    $sum = $A1[$i] + $A2[$i] * 9;

		    for($i = 1; $i < 10; $i++){
		        $v = intval($ID[$i]);
		        if(!is_numeric($v)){
		        	return false;
		        }
		        $sum = $sum + $v * $Mx[$i];
		    }

		    return (($sum % 10) == 0);
		}	

		/**
		 * 兩數相同and值不為空
		 * @param  [type]  $v1 [description]
		 * @param  [type]  $v2 [description]
		 * @return boolean     [description]
		 */
		public function isSame($v1,$v2){
			return (isset($v1) && isset($v2) && ($v1 != NULL) && ($v1 != "") && ($v1 == $v2));
		}
		
	}
}