<?php 
	include_once('header.php');
	$web_set["titlePrefix"] = "最新消息";
	$temp = new MTsung\dataList($console,PREFIX.$console->path[0],$lang);

	if(isset($console->path[1])){
		$key = $console->path[1];
		if(!$data["one"] = $temp->getOne("and id=? or urlKey=?",array($key,$key))){
			$console->to404();
		}
	}else{
		$data["list"] = $temp->getListData("and status='1' order by sort",array(),10);
		$data["page"] = $temp->pageNumber->getHTML1();
	}
?>