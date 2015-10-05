<?php
	header('Content-Type: text/html; charset=utf-8');
	$rawData = $_POST['imgBase64'];
	$filteredData = explode(',', $rawData);
	$unencoded = base64_decode($filteredData[1]);
	
	$datime = date("Y-m-d-H.i.s", time() ) ; # - 3600*7
	if(is_numeric($userid)) $er='N_';
	$userid  = $_POST['userid'] ;
	if(!$userid) $er='NoID_'.$datime;
	//$userid = urlencode($userid);
	
	// name & save the image file
	$filename = iconv("utf-8", "tis-620", 'images/students/'.$er.$userid.'.jpg');
	$fp = fopen($filename, 'w');
	fwrite($fp, $unencoded);
	fclose($fp);
?>