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
for ($i=0; $i <24 ; $i++) { 
	$s = DATE_Y."-".DATE_M."-".DATE_D." ".$i.":00:00";
	$e = DATE_Y."-".DATE_M."-".DATE_D." ".($i+1).":00:00";
	$data["analytics"]["count24H"][] = $analytics->getTotalCount(false,$s,$e);
	$data["analytics"]["repeatCount24H"][] = $analytics->getTotalCount(true,$s,$e);
}
for ($i=0; $i <date("t",strtotime(date("Y-m")."-1")) ; $i++) { 
	$s = DATE_Y."-".DATE_M."-".($i+1)." 00:00:00";
	$e = DATE_Y."-".DATE_M."-".($i+2)." 00:00:00";
	$data["analytics"]["countMonth"][] = $analytics->getTotalCount(false,$s,$e);
	$data["analytics"]["repeatCountMonth"][] = $analytics->getTotalCount(true,$s,$e);
}
?>