<?php
/**
 * 後台上板
 */
if($_POST){
	echo "<title>Loading...</title>";
}

$memberSessionName = 'serback';
$member = new MTsung\member($console,PREFIX.'admin',$memberSessionName);

$loginUrl = $console->MT_web["serback_path"].'/login';

/**
 * 判斷有沒有登入
 */
if(!$member->isLogin()){
	if($member->message){
		$console->alert($member->message,$loginUrl);
	}else{
		$_SESSION[FRAME_NAME]["BACK_URI"] = $_SERVER["REQUEST_URI"];
		$console->linkTo($loginUrl);
	}
}
unset($_SESSION[FRAME_NAME]["BACK_URI"]);

/**
 * 帳號資訊
 */
$memberInfo = $_SESSION[FRAME_NAME]["member"][$memberSessionName];


/**
 * 群組
 */
$memberGroup = new MTsung\memberGroup($console,PREFIX.'admin_group');
$memberInfo["group"] = $memberGroup->getData(" where id='".$memberInfo["groupID"]."'")[0];
if($memberInfo["group"]["status"]==0){
	//群組已被停用
	$console->alert($console->getMessage("GROUP_DISABLED"),$loginUrl);
}

/**
 * 資料庫容量限制
 */
if($_POST && $console->setting->getValue("sizeSwitch") && ($console->setting->getValue("sqlMaxSize")<$console->getSqlSize())){
	$console->alert($console->getMessage("SQL_SIZE_MAX_MSG"),-1);
	exit;
}

/**
 * 管理語系
 */
if(isset($_GET['setSettingLang'])){
	$_SESSION[FRAME_NAME]["SETTING_LANG"] = $_GET['setSettingLang'];
}
if(isset($_SESSION[FRAME_NAME]["SETTING_LANG"]) && $_SESSION[FRAME_NAME]["SETTING_LANG"] && isset($console->languageArray[$_SESSION[FRAME_NAME]["SETTING_LANG"]])){
	$settingLang = $_SESSION[FRAME_NAME]["SETTING_LANG"];
}else{
	$settingLang = $_SESSION[FRAME_NAME]["SETTING_LANG"] = LANG;
}

/**
 * 選單
 */
$menu = new MTsung\menu($console,PREFIX."menu");
$temp = $menu->getData("where status='1' and id in ('".str_replace(",","','", $memberInfo["group"]["auth"])."') order by sort asc");
$menuUrl = array();
if($temp){
	foreach ($temp as $key => $value) {
		if($value["url"]){
			array_push($menuUrl, $value["url"]);
		}
	}
}

/**
 * 是否有權限進入此網址
 */
$url = str_replace($console->getLanguage()."/","",$_SERVER["REQUEST_URI"]);//刪除網址的語言部份
if(0 === strpos($url,$console->MT_web["serback_path"]) && isset($console->path[0]) && $console->path[0] != "index" && $console->path[0] != "" ){
	$_OK = false;
	$url = substr($url,strlen($console->MT_web["serback_path"]."/"));
	foreach ($menuUrl as $key => $value) {
		if((0 === strpos($url,$value)) && (!isset($url[strlen($value)]) || (isset($url[strlen($value)]) && ($url[strlen($value)]=="/" || $url[strlen($value)]=="?")))){
			$_OK = true;
			/**
			 * 麵包屑
			 */
			$breadcru[0]['name'] = $menu->getData("where url='".$value."'")[0]["name"];
			$breadcru[0]['url'] = $value;
			break;
		}
	}
	if(!$_OK){
		$console->alert($console->getMessage("NOT_AUTHORITY"),$console->MT_web["serback_path"]);
	}
}

/**
 * 左側選單
 */
$menuArray = array();
if($temp){
	foreach ($temp as $key => $value) {
		if(!isset($menuArray[$value["floor"]])){
			$menuArray[$value["floor"]] = array();
		}
		array_push($menuArray[$value["floor"]], $value);
	}
}
unset($temp);
$console->design->setData("menu", @$menuArray);

$web_set['lang'] = count($console->getLanguageArray("array"))==1?'':$console->getLanguage();
$web_set['main_url'] = WEB_PATH.($web_set['lang']?'/'.$web_set['lang']:'');
$web_set['serback_url'] = $web_set['main_url'].'/serback';


/**
 * 開關
 */
$switch["searchBox"] = 0;
$switch["buttonBox"] = 0;

$switch["addButton"] = 0;
$switch["editButton"] = 0;
$switch["saveButton"] = 0;
$switch["deleteButton"] = 0;
$switch["backButton"] = 0;
$switch["redoButton"] = 0;

$switch["listList"] = 0;
$switch["addList"] = 0;
$switch["editList"] = 0;
$switch["detailList"] = 0;
$switch["instructionsList"] = 0;

/**
 * 案鈕事件
 */
$data["addOnClick"] = '';		//javascript
$data["listUrl"] = '';			//url

	
?>