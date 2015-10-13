<?php
	session_start();
	date_default_timezone_set("Asia/Bangkok");
	$noPhoto = 'images/noPhoto.jpg';
	$objConnect = mysql_connect("localhost","utccictc_tss","1d6QHmik");
// 	$objConnect = mysql_connect("localhost","root","");
	if($objConnect){
		$objDB = mysql_select_db("utccictc_tss");
// 		$objDB = mysql_select_db("tss_old");
		mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'", $objConnect);
	}
	if($_SESSION['userID']&&$_SESSION['userType']){
		if($_POST['logout']){
			require_once 'inc/logout.php';
		} else {
// 			$rowc = mysql_fetch_array($objQueryc);
// 			$confUserID = $rowc['memberID'];
			$confUserID = $_SESSION['userID'];
			$confUserType = $_SESSION['userType'];
			$confSuperUser = $_SESSION['userSuper'];
		}
	} else {
		$username = $_POST['username'];
		$password = $_POST['password'];
		$type = $_POST['type'];
		if($username&&$password&&$type){
			$strSQLc = sprintf(
				"
				SELECT
					* 
				FROM
					`%s` 
				WHERE
					`%sID` = '%s' AND
					`password` = '%s'
				",
				mysql_real_escape_string($type),
				mysql_real_escape_string($type),
				mysql_real_escape_string($username),
				mysql_real_escape_string($password)
			);
			$objQueryc = mysql_query($strSQLc);
			if(mysql_num_rows($objQueryc)==1){
				$rowc = mysql_fetch_array($objQueryc);
//				$strSQLc = 'INSERT INTO authentication VALUES ("'.session_id().'","'.$rowc['memberID'].'","'.date('Y-m-d H:i:s').'")';
//				$objQueryc = mysql_query($strSQLc);
//				if($objQueryc) $confUserID = $rowc['memberID']; else $reason = 'ไม่สามารถทำการยืนยันตัวตนได้ในตอนนี้ กรุณาติดต่อผู้ดูแลระบบ.';
				if($type=='instructor'){
					$super = $rowc['superUser'];
				} else $super = '0';
				$_SESSION['userID'] = $rowc[$type.'ID'];
				$_SESSION['userType'] = $type;
				$_SESSION['userSuper'] = $super;
				$confUserID = $rowc[$type.'ID'];
				$confUserType = $type;
				$confSuperUser = $super;
			} else {
				$password = '';
				$reason = 'ชื่อผู้ใช้ หรือ รหัสผ่าน ผิด กรุณาลองอีกครั้ง.';
			}
		} else {
			$reason = 'กรุณากรอก ชื่อผู้ใช้, รหัสผ่าน และ ประเภท.';
		}
	}
//-------------FUNCTION
	function getGradeYearName($gradeYear){
		switch ($gradeYear){
			case '1':	$return = 'ประถมศึกษาปีที่ 1'; break;
			case '2':	$return = 'ประถมศึกษาปีที่ 2'; break;
			case '3':	$return = 'ประถมศึกษาปีที่ 3'; break;
			case '4':	$return = 'ประถมศึกษาปีที่ 4'; break;
			case '5':	$return = 'ประถมศึกษาปีที่ 5'; break;
			case '6':	$return = 'ประถมศึกษาปีที่ 6'; break;
			case '7':	$return = 'มัธยมศึกษาปีที่ 1'; break;
			case '8':	$return = 'มัธยมศึกษาปีที่ 2'; break;
			case '9':	$return = 'มัธยมศึกษาปีที่ 3'; break;
		}
		return $return;
	}
	function getTerm(){
		$date = date('Y-m-d');
// 		$date=date('Y-m-d', strtotime("10/10/".getYear()));	//--(m/d/Y)
		$term1Begin = date('Y-m-d', strtotime("05/18/".getYear()));
// 		$term1End = date('Y-m-d', strtotime("10/10/".getYear()));
		$term1End = date('Y-m-d', strtotime("10/31/".getYear()));
// 		$term2Begin = date('m-d', strtotime("11/02"));
		$term2Begin = date('Y-m-d', strtotime("11/01/".getYear()));
		$term2End = date('Y-m-d', strtotime("03/25".getYear()." +1 year"));
		if (($date >= $term1Begin) && ($date <= $term1End)) {
			$term = 1;
		} elseif(($date >= $term2Begin) && ($date <= $term2End)) {
			$term = 2;
		} else {
			$term = 3;
		}
		return $term;
	}
	function getYear(){
		$year = date("Y");
		$month = date("m");
		if($month<=5) $year--;
		return $year;
	}
	function radioTerm($t){
		$check = '';
		if(getTerm()==$t) $check = ' checked';
		return $check;
	}
	function randomPassword($l=8) {
		$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		$pass = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < $l; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass); //turn the array into a string
	}
?>
