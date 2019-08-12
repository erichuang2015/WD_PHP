<?php

	$lang = $console->getLanguage();
	//狀態sql
	$statusSql = " and status='1' and (release_date<='".DATE."' or release_date is null or release_date='') and (expire_date>='".DATE."' or expire_date is null or expire_date='') ";

	$template = new MTsung\fileTemplate($console);
	$templateList = $template->getListData("web");

	$output = array(HTTP_PATH);

	if($templateList){
		foreach ($templateList as $keyTemplate => $valueTemplate) {
			$pageName = explode(".", $valueTemplate["name"])[0];
			if($console->conn->GetArray("desc ".PREFIX.$pageName."__".str_replace("-","_",$lang))){

				$menu = new MTsung\menu($console,PREFIX."menu");

				if($temp = $menu->getData("where alias=? and status=1 and features!='_other_form'",array($pageName))){
					foreach ($temp as $key => $value) {

						$basic = new MTsung\dataList($console,PREFIX.$pageName,$lang);
						switch ($value["features"]) {
							case '_other_basicOne':
								$output[] = HTTP_PATH.$pageName;
								break;
							case 'basic/product':
							case '_other_basic':
								if($data["list"] = $console->urlKey($basic->getListData($statusSql." order by sort",array("name"),$value["count"]))){
									foreach ($data["list"] as $listKey => $listValue) {
										if(isset($listValue["class"]) && $listValue["class"]){
											$output[] = HTTP_PATH.$pageName."/".explode("|__|",$listValue["class"])[0]."/".$listValue["urlKey"];
										}else{
											$output[] = HTTP_PATH.$pageName."/".$listValue["urlKey"];
										}
									}
								}
								if(!isset($data["list"][0]["class"]) || !$data["list"][0]["class"]){
									$output[] = HTTP_PATH.$pageName;
									for ($i=2; $i <= $basic->pageNumber->getPageTotal(); $i++) { 
										$output[] = HTTP_PATH.$pageName."?page=".$i;
									}
								}
								break;
							case 'class/product':
							case '_other_class':
								$class = new MTsung\dataClass($console,PREFIX.$pageName."_class",$lang);
								if($data["class"] = $console->urlKey($class->getData("where 1=1 ".$statusSql." order by step"))){
									foreach ($data["class"] as $keyClass => $valueClass) {
										$classArray = array();
										$classArray[] = $valueClass["id"];
										if($tempC = $class->findChildren1($valueClass["id"])){
											foreach ($tempC as $valueC) {
												$classArray[] = $valueC;
											}
										}
										$findClassSql = $basic->findArrayString("class",$classArray);
										$count = $menu->getData("where alias=? and status=1 and (features='_other_basic' or features='basic/product')",array($pageName))[0]["count"];
										if($data["list"] = $console->urlKey($basic->getListData($statusSql.$findClassSql." order by sort",array("name"),$count))){
											foreach ($data["list"] as $listKey => $listValue) {
												$output[] = HTTP_PATH.$pageName."/".$valueClass["urlKey"]."/".$listValue["urlKey"];
											}
										}
										$output[] = HTTP_PATH.$pageName."/".$valueClass["urlKey"];
										for ($i=2; $i <= $basic->pageNumber->getPageTotal(); $i++) { 
											$output[] = HTTP_PATH.$pageName."/".$valueClass["urlKey"]."?page=".$i;
										}
									}
								}

								break;
							default:
								# code...
								break;
						}
					}
				}
			}
		}
	}

	//輸出
	header('Content-Type: text/xml');
	$dom = new DOMDocument('1.0');
	$dom->encoding = 'UTF-8';

	$pre = 5000;

	$output = array_unique($output);

	if(count($output)<$pre){
		$urlset = $dom->appendChild($dom->createElement('urlset'));
		$urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
		foreach ($output as $key => $value) {
			$url = $urlset->appendChild($dom->createElement('url'));
			$loc = $url->appendChild($dom->createElement('loc'));
			$loc->appendChild($dom->createTextNode($value));
		}
	}else{
		if($_GET["page"]){
			$pageNumber = new MTsung\pageNumber($console,$output,$pre);
		    $temp = array();
		    $end = $pageNumber->getDataStart()+$pageNumber->getPer();
		    $end = ($end>$pageNumber->getDataCount())?$pageNumber->getDataCount():$end;
		    for ($i = $pageNumber->getDataStart(); $i < $end ; $i++) { 
		    	$temp[] = $output[$i];
		    }
		    $output = $temp;
			$urlset = $dom->appendChild($dom->createElement('urlset'));
			$urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
			foreach ($output as $key => $value) {
				$url = $urlset->appendChild($dom->createElement('url'));
				$loc = $url->appendChild($dom->createElement('loc'));
				$loc->appendChild($dom->createTextNode($value));
			}
		}else{
			$sitemapindex = $dom->appendChild($dom->createElement('sitemapindex'));
			for ($i=1; $i <= ceil(count($output)/$pre); $i++) { 
				$sitemap = $sitemapindex->appendChild($dom->createElement('sitemap'));
				$loc = $sitemap->appendChild($dom->createElement('loc'));
				$loc->appendChild($dom->createTextNode(HTTP_PATH."sitemap.xml?page=".$i));
			}
		}
	}

	  
	$xmlStr = $dom->saveXML();
	echo $xmlStr;

	exit;