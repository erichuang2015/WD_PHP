<?php 
	include_once('header.php'); 

	//EDM
	$edm = new MTsung\dataList($console,PREFIX."edm",$lang);
	$temp = $edm->getOne();
	$data["EDM"] = explode("|__|", $temp["picture"]);

	//熱門消息
	$index_news = new MTsung\dataList($console,PREFIX."index_news",$lang);
	$temp = $index_news->getOne();
	$dataID = explode("|__|", $temp["detail"]);
	$news = new MTsung\dataList($console,PREFIX."news",$lang);
	foreach ($dataID as $key => $value) {
		$data["news"][] = $news->getData("where id=?",array($value))[0];
	}
	$data["news"] = $console->urlKey($data["news"]);

	//熱門商品
	$index_product = new MTsung\dataList($console,PREFIX."index_product",$lang);
	$temp = $index_product->getOne();
	$dataID = explode("|__|", $temp["detail"]);
	$product = new MTsung\dataList($console,PREFIX."product",$lang);
	$class = new MTsung\dataClass($console,PREFIX."product_class",$lang);
	foreach ($dataID as $key => $value) {
		$data["product"][$key] = $product->getData("where id=?",array($value))[0];
		$data["product"][$key]["class"] = explode("|__|",$data["product"][$key]["class"])[0];

		$tempClass = $class->getOne("and id=?",array($data["product"][$key]["class"]));
		$data["product"][$key]["class"] = $console->urlKey($tempClass)["urlKey"];

	}
	$data["product"] = $console->urlKey($data["product"]);

?>