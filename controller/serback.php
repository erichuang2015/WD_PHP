<?php
/**
 * 後台轉換
 */
$console->design->setSerbackDir();
unset($console->path[0]);
$console->path = array_values($console->path);
if(isset($console->path[0]) && $console->path[0]){
	$console->controller = $console->path[0];
}else{
	$console->controller = 'index';
}

$noHeaderPage = array('login','forget');
if (is_file(CONTROLLER_PATH.'serback/'.$console->controller.'.php')){

	if(!in_array($console->controller, $noHeaderPage)){
		include_once(CONTROLLER_PATH.'serback/header.php'); 
	}
	include_once(CONTROLLER_PATH.'serback/'.$console->controller.'.php'); 
}else{
	$console->to404();
	// echo $console->getMessage('CONTROLLER_NULL',array($console->controller));
	exit;
}

?>