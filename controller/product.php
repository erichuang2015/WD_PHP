<?php 
	include_once('header.php');
	$web_set["titlePrefix"] = "商品情報";
	$class = new MTsung\dataClass($console,PREFIX.$console->path[0]."_class",$lang);
	$data["class"] = $class->getData("where status='1' order by step");

	//class
	if(isset($console->path[1])){
		//網址轉換
		if($console->path[1] == "detail"){
			$key = $console->path[2];
			$console->linkTo(explode("|__|",WEB_PATH."/".$console->path[0]."/".$product->getOne("and (id=? or urlKey=?)",array($key,$key))["class"])[0]."/".$console->path[2]);
			exit;
		}

		$key = $console->path[1];
		if(!$data["one"] = $data["oneClass"] = $class->getOne("and (id=? or urlKey=?)",array($key,$key))){
			$console->to404();
		}
	}else{
		$data["oneClass"] = $data["class"][0];
	}
	$data["oneClass"] = $console->urlKey($data["oneClass"]);
	$_GET["class"] = $data["oneClass"]["id"];

	$findClassSql = $product->findArrayString("class",$data["oneClass"]["id"]);

	//商品
	if(isset($console->path[2])){
		$key = $console->path[2];
		if(!$data["one"] = $product->getOne("and (id=? or urlKey=?) ".$findClassSql,array($key,$key))){
			$console->to404();
		}
		$data["one"]["price"] = $product->getPrice($data["one"]["id"]);
		$data["one"]["specificationsID"] = explode("|__|",$data["one"]["specificationsID"])[0];
	}else{
		$data["list"] = $product->getListData("and status='1' ".$findClassSql." order by sort",array(),12);
		$data["page"] = $product->pageNumber->getHTML1();
	}

	//全部商品
	if($data["class"]){
		foreach ($data["class"] as $key => $value) {
			$findClassSql = $product->findArrayString("class",$value["id"]);
			$data["listAll"][$value["id"]] = $product->getData("where status='1' ".$findClassSql." order by sort");
			$data["listAll"][$value["id"]] = $console->urlKey($data["listAll"][$value["id"]]);
		}
	}


?>