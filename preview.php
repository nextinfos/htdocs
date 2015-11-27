<?php
	require_once 'config.php';
	$type = $_REQUEST['type'];
	switch($type){
		case "gradeReport":	$file = 'template/gradeReportPreview.php';	break;
		default:	$file = 'index.php?action=404';	$error = true;	break;
	}
	if($error!=true){
		require_once $file;
	 } else header("Location: ".$file)?>