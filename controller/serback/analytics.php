<?php

$analytics = new MTsung\analytics($console);
// $analytics->addLog();

$monthArray = array("","Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
for ($i=(DATE_M-5); $i <=DATE_M ; $i++) {
	$s = DATE_Y."-".$i."-01 ";
	$e = DATE_Y."-".($i+1)."-01 ";
	$data["analytics"]["month"][] = $monthArray[$i];
	$data["analytics"]["count"][] = $analytics->getTotalCount(false,$s,$e);
	$data["analytics"]["repeatCount"][] = $analytics->getTotalCount(true,$s,$e);
}
$data["analytics"]["device"] = $analytics->getFieldCount("device",$s,$e);
$data["analytics"]["system"] = $analytics->getFieldCount("system",$s,$e);
$data["analytics"]["lang"] = $analytics->getFieldCount("lang",$s,$e);

?>