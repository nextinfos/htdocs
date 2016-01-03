<?php
	require_once 'config.php';
	require_once 'dc/function.php';
	$pass = false;
	$action = isset($_POST['action'])?$_POST['action']:false;
	$type = isset($_POST['type'])?$_POST['type']:false;
	$atdID = $_SESSION["atdID"];
	$cardID = $_POST['cardID'];
	$studentID = $_POST['studentID'];
	$time = $_POST['time'];
	if(!$time) $time = date('H:i:s',strtotime('Now +10 minute'));
	$file = "dc/$action/$type.php";
	$pass = file_exists($file)?true:false;
	if($action&&$type&&$pass){
		require_once $file;
	} else {
	?>
		<form method="POST">
			<input type="text" name="action">
			<input type="text" name="type">
			<input type="text" name="atdID">
			<input type="text" name="cardID">
			<input type="submit">
		</form>
	<?php
	}
?>
