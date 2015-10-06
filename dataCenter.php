<?php require_once 'config.php';
$action = $_POST['action'];
$type = $_POST['type'];
$atdID = $_SESSION["atdID"];
$cardID = $_POST['cardID'];
$studentID = $_POST['studentID'];
$time = $_POST['time'];
if(!$time) $time = date('H:i:s',strtotime('Now +10 minute'));
function getCardInfo($studentID){
	$strSQL = "SELECT * FROM students WHERE studentID = '".$studentID."' LIMIT 1";
	$objQuery = mysql_query($strSQL);
	if($objQuery){
		$row = mysql_fetch_array($objQuery);
		$photo = 'images/students/'.$row['studentID'].'.jpg';
		if(!file_exists($photo)){
			$photo = 'images/noPhoto.jpg';
		}
		$result = '	{	"status": "success",	"data": [	{	"id": "'.$row['studentID'].'",	"fname": "'.$row['firstname'].'",	"lname": "'.$row['lastname'].'",	"photo": "'.$photo.'"	}	]	}';
	} else {
		$result = '	{	"status": "fail",	"data": [	{	"detail": "Error Query ['.$strSQL.'] '.mysql_error().'",	}	]	}';
	}
	return $result;
}
function getStdFromCard($cardID){
	if(ctype_digit ($cardID)){
		$thn = array('ๅ','/','-','ภ','ถ','ุ','ึ','ค','ต','จ');
		$thc = array('+','๑','๒','๓','๔','ู','฿','๕','๖','๗');
		$num = array('1','2','3','4','5','6','7','8','9','0');
		$cardID = str_replace($thn, $num, $cardID);
		$cardID = str_replace($thc, $num, $cardID);
	}
	$strSQL = "SELECT studentID FROM card WHERE cardID = '".$cardID."' OR secondCardID = '".$cardID."' LIMIT 1";
	$objQuery = mysql_query($strSQL);
	if($objQuery&&mysql_num_rows($objQuery)==1){
		$row = mysql_fetch_array($objQuery);
		$studentID = $row['studentID'];
	} else {
		$studentID = false;
	}
	return $studentID;
}
function isStdRegis($studentID,$atdID){
	$strSQL = "SELECT a.datetime,a.late FROM reg_student r INNER JOIN atdlist a WHERE r.studentID = '".$studentID."'  AND a.atdID = '".$atdID."' AND r.regSubjectID = a.regSubjectID LIMIT 1";
	$objQuery = mysql_query($strSQL);
	if($objQuery&&mysql_num_rows($objQuery)==1){
		$row = mysql_fetch_array($objQuery);
	} else {
		$row = false;
	}
	return $row;
}
function isCheckedIn($studentID,$atdID){
	$strSQL = "SELECT time FROM atdinfo WHERE studentID = '".$studentID."' AND atdID = '".$atdID."' LIMIT 1";
	$objQuery = mysql_query($strSQL);
	if($objQuery&&mysql_num_rows($objQuery)<1){
		$result = false;
	} else {
		$result = true;
	}
	return $result;
}
if($action=="get"){
	if($type=="atdList"){
		if($atdID){
			$strSQL = "SELECT a.studentID,a.time,a.status,s.firstname,s.lastname FROM atdinfo a INNER JOIN students s WHERE a.studentID = s.studentID AND a.atdID = '".$atdID."' ORDER BY a.time DESC";
			$objQuery = mysql_query($strSQL);
			if($objQuery){
				$data = '{	"data": [';
				$i==0;
				$status = array('Cancel','Ontime','Late','Unknow');
				while($row=mysql_fetch_array($objQuery)){
					$i++;
					if($i>1) $data .= ',';
					$data .= '{	"id": "'.$row['studentID'].'",	"name": "'.$row['firstname'].'&nbsp;&nbsp;&nbsp;'.$row['lastname'].'",	"time": "'.$row['time'].'",	"status": "'.$status[$row['status']].'"}';
				}
				echo $data.']	}';
			}
		}
	}elseif($type=="stdList"){
		$subID = $_REQUEST['regSubjectID'];
		if($subID){
			$strSQL = 'SELECT * FROM reg_student r, students s WHERE r.studentID = s.studentID AND r.status = "1" AND s.status = "1" AND r.regSubjectId = "'.$subID.'"';
			$objQuery = mysql_query($strSQL);
			if($objQuery){
				$data = '{	"data": [';
				$i==0;
// 				$status = array('Cancel','Ontime','Late','Unknow');
				while($row=mysql_fetch_array($objQuery)){
					$i++;
					if($i>1) $data .= ',';
					$data .= '{	"num":"'.$i.'","id": "'.$row['studentID'].'",	"name": "'.$row['firstname'].'&nbsp;&nbsp;&nbsp;'.$row['lastname'].'"}';
				}
				echo $data.']	}';
			}
		}
	}elseif($type=="card"){
		if($studentID){
			echo getCardInfo($studentID);
		}
	} elseif($type=="subList"){
		$strSQL = 'SELECT * FROM `subject` WHERE 1';
		$objQuery = mysql_query($strSQL);
		if($objQuery){
			$data = '{	"data": [';
			$i==0;
			while($row=mysql_fetch_array($objQuery)){
				$i++;
				if($i>1) $data .= ',';
				$data .= '{	"ch":"<input type=\'checkbox\' name=\'subjectID\' value=\''.$row['subjectID'].'\' />","subjectID": "'.$row['subjectID'].'",	"subjectName": "'.$row['name'].'",	"subjectType": "'.($row['type']=='BASIC'?'พื้นฐาน':'เพิ่มเติม').'",	"edit": "e","delete":"d"}';
			}
			echo $data.']	}';
		}
	} elseif($type=="regInsSubList"){
		$term = getTerm();
		$year = getYear();
		$instructorID = $confUserID;
		$strSQL = 'SELECT * FROM `register-subject` regsub, `registerinfo` reg WHERE reg.registerID = regsub.registerID AND reg.term="'.$term.'" AND reg.year="'.$year.'" AND regsub.instructorID="'.$instructorID.'";';
		$objQuery = mysql_query($strSQL);
		if(mysql_num_rows($objQuery)>=1){
			while($row = mysql_fetch_array($objQuery)){
				$data.='<option value="'.$row['subjectID'].'">'.$row['subjectID'].' '.$row['name'].'('.($row['type']=='BASIC'?'พื้นฐาน':'เพิ่มเติม').')</option>';
			}
		} else {
			$data = '<option value="0">ไม่พบวิชา</option>';
		}
		echo $data;
	} elseif($type=="autoComplete"){
		$source=$_POST['source'];
		$data = $_POST['data'];
		if($source=="studentID"){
			$strSQL = 'SELECT studentID FROM students WHERE studentID LIKE "'.$data.'%"';
			$objQuery = mysql_query($strSQL);
			if($objQuery){
				$data = '[';
				while($row=mysql_fetch_array($objQuery)){
					$i++;
					if($i>1) $data .= ',';
					$data.= '{"label":"'.$row['studentID'].'"}';
				}
				echo $data.']';
			}
		}
	} elseif($type=='regSubList'){
		$term = $_POST['term'];
		$year = $_POST['year'];
		$strSQL = 'SELECT * FROM `register-subject` regsub, `registerinfo` reg WHERE reg.registerID = regsub.registerID AND reg.term="'.$term.'" AND reg.year="'.$year.'";';
		$objQuery = mysql_query($strSQL);
		if(mysql_num_rows($objQuery)>=1){
			while($row = mysql_fetch_array($objQuery)){
				$data.='<option value="'.$row['subjectID'].'">'.$row['subjectID'].' '.$row['name'].'('.($row['type']=='BASIC'?'พื้นฐาน':'เพิ่มเติม').')</option>';
			}
		} else {
			$data = '<option value="0">ไม่พบวิชา</option>';
		}
		echo $data;
	} elseif($type=='stuCanReg'){
		$subjectID = $_POST['subjectID'];
		$term = $_POST['term'];
		$year = $_POST['year'];
		if($subjectID&&$term&&$year){
			$strSQL = "SELECT stu.studentID AS studentID, stu.firstName AS firstName, stu.lastName AS lastName, stu.gradeYear AS gradeYear FROM `register-subject` regsub, `registerinfo` reg, `student` stu WHERE reg.registerID = regsub.registerID AND reg.term='$term' AND reg.year='$year' AND regsub.subjectID = '$subjectID' AND regsub.gradeYear = stu.gradeYear AND stu.status='NORMAL';";
			$objQuery = mysql_query($strSQL);
			if(mysql_num_rows($objQuery)>=1){
				$i=0;
				while($row = mysql_fetch_array($objQuery)){
					$data['data'][$i] = $row;
					$data['data'][$i]['gradeYear'] = getGradeYearName($row['gradeYear']);
					$i++;
				}
				echo json_encode($data);
			} else {
				$data['data'][] = array("studentID"=>"","firstName"=>"ไม่พบ","lastName"=>"","gradeYear"=>"");
				echo json_encode($data);
			}
		} else {
			$data['data'][] = array("studentID"=>"","firstName"=>"","lastName"=>"","gradeYear"=>"");
			echo json_encode($data);
		}
	}
} elseif($action=="set"){
	if($type=="atdList"){
		if($cardID||$studentID){
			if($cardID){
				$studentID = getStdFromCard($cardID);
			}
			if($studentID){
				$stdIsReg = isStdRegis($studentID,$atdID);
				if($stdIsReg){
					$startDateTime = $stdIsReg['datetime'];
					$late = $stdIsReg['late'];
					if(!isCheckedIn($studentID, $atdID)){
						if(date('Y-m-d H:i:s',strtotime($startDateTime.' +'.$late.' minute'))>date('Y-m-d H:i:s',strtotime('Today '.$time))) $status = 1; else $status = 2;
						$strSQL = "INSERT INTO atdinfo VALUES(NULL,'".$atdID."','".$studentID."','".date("Y-m-d")."','".$time."','".$status."')";
						$objQuery = mysql_query($strSQL);
						if($objQuery){
							echo '{	"status":"success",	"data": [{"responseText":"Added","studentID":'.getCardInfo($studentID).'}]	}';
						} else {
							echo '{"status":"fail","data": [{"reason":"SaveFail","strSQL":"'.$strSQL.'"}]}';
						}
					} else {
						echo '{"status":"fail","data": [{"reason":"CheckedIn","strSQL":"'.$strSQL.'"}]}';
					}
				} else {
					echo '{"status":"fail","data": [{"reason":"NotFoundReg","strSQL":"'.$strSQL.'"}]}';
				}
			} else {
					echo '{"status":"fail","data": [{"reason":"NotFoundCard","cardID":"'.$_POST['cardID'].'"}]}';
			}
		}
	} elseif($type=='createAtd'){
		$regSubjectID = $_POST['regSubjectID'];
		$late = $_POST['late'];
		$time = $_POST['time'];
		$strSQL = 'INSERT INTO atdlist VALUES (NULL,"'.$regSubjectID.'","'.date('Y-m-d H:i:s',strtotime('Today '.$time)).'","'.$late.'")';
		$objQuery = mysql_query($strSQL);
		if($objQuery){
			$_SESSION['atdID'] = mysql_insert_id();
			echo '{"status":"success"}';
		}
	} elseif($type=='cardHolder'){
		$studentID = $_POST['studentID'];
		$cardID1 = $_POST['cardID1'];
		$cardID2 = $_POST['cardID2'];
		$strSQL = "INSERT INTO card VALUES('".$cardID1."','".$cardID2."','".$studentID."')";
		$objQuery = mysql_query($strSQL);
		if($objQuery){
			echo '{"status":"success"}';
		}
	} elseif($type=='regStudent'){
		$term = $_POST['term'];
		$year = $_POST['year'];
		$subjectID = $_POST['subjectID'];
		echo "$term/\n/$year/\n/$subjectID";
	}
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
