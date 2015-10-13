<?php require_once 'config.php';
$action = $_POST['action'];
$type = $_POST['type'];
$atdID = $_SESSION["atdID"];
$cardID = $_POST['cardID'];
$studentID = $_POST['studentID'];
$time = $_POST['time'];
if(!$time) $time = date('H:i:s',strtotime('Now +10 minute'));
function getCardInfo($studentID){
	$strSQL = sprintf(
		"
		SELECT
			*
		FROM
			student
		WHERE
			studentID = '%s'
		LIMIT 1
		",
		mysql_real_escape_string($studentID)
	);
	$objQuery = mysql_query($strSQL);
	if($objQuery){
		$row = mysql_fetch_array($objQuery);
		$photo = 'images/students/'.$row['studentID'].'.jpg';
		if(!file_exists($photo)){
			$photo = 'images/noPhoto.jpg';
		}
		$data['status'] = "SUCCESS";
		$data['data'][] = array(
				"id"=>$row['studentID'],
				"fname"=>$row['firstName'],
				"lname"=>$row['lastName'],
				"photo"=>$photo);
	} else {
		$data['status'] = "FAIL";
		$data['data'][] = array(
				"detail"=> "Error Query [$strSQL] ".mysql_error()
		);
		$result = '	{	"status": "fail",	"data": [	{	,	}	]	}';
	}
	return $data;
}
function getStdFromCard($cardID){
	if(ctype_digit ($cardID)){
		$thn = array('ๅ','/','-','ภ','ถ','ุ','ึ','ค','ต','จ');
		$thc = array('+','๑','๒','๓','๔','ู','฿','๕','๖','๗');
		$num = array('1','2','3','4','5','6','7','8','9','0');
		$cardID = str_replace($thn, $num, $cardID);
		$cardID = str_replace($thc, $num, $cardID);
	}
	$strSQL = sprintf(
		"
		SELECT
			studentID
		FROM
			student
		WHERE
			cardID = '%s' OR
			secondCardID = '%s'
		LIMIT 1
		",
		mysql_real_escape_string($cardID),
		mysql_real_escape_string($cardID)
	);
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
	$strSQL = sprintf(
		"
		SELECT
			regstu.studentID
		FROM
			`register-student` regstu,
			`attendanceinfo` atd
		WHERE
			atd.subjectID = regstu.subjectID AND
			atd.registerID = regstu.registerID AND
			regstu.studentID = '%s'  AND
			atd.attendanceID = '%s'
		LIMIT 1
		",
		mysql_real_escape_string($studentID),
		mysql_real_escape_string($atdID)
	);
	$objQuery = mysql_query($strSQL);
	if($objQuery&&mysql_num_rows($objQuery)==1){
		$row = true;
	} else {
		$row = false;
	}
	return $row;
}
function isCheckedIn($studentID,$atdID){
	$strSQL = sprintf(
		"
		SELECT
			stuatd.status
		FROM
			`studentattendance` stuatd
		WHERE
			stuatd.studentID = '%s' AND
			stuatd.attendanceID = '%s'
		LIMIT 1
		",
		mysql_real_escape_string($studentID),
		mysql_real_escape_string($atdID)
	);
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
			$strSQL = sprintf(
				"
				SELECT 
					stuatd.studentID,
					stuatd.status,
					stu.firstName,
					stu.lastName 
				FROM 
					`studentattendance` stuatd,
					`student` stu
				WHERE
					stuatd.attendanceID = '%s' AND
					stuatd.studentID = stu.studentID
				",
				mysql_real_escape_string($atdID)
			);
			$objQuery = mysql_query($strSQL);
			if($objQuery){
				if(mysql_num_rows($objQuery)>0){
					$status = array('ONTIME'=>'Ontime','LATE'=>'Late','Unknow');
					while($row=mysql_fetch_array($objQuery)){
						$data['data'][] = array(
								"id"=> $row['studentID'],	
								"name"=> $row['firstName'].'&nbsp;&nbsp;&nbsp;'.$row['lastName'],	
								"status"=> $status[$row['status']]);
					}
				} else {
					$data['data'][] = array(
							"id"=> '',
							"name"=> 'ไม่พบข้อมูล',
							"status"=> '');
				}
			}
		}
		echo json_encode($data);
	} elseif($type=="stuRegList"){
		$subjectID = $_REQUEST['subjectID'];
		$instructorID = $confUserID;
		$term = getTerm();
		$year = getYear();
		$strSQL = sprintf(
			"
			SELECT
				stu.cardID,
				stu.secondCardID,
				stu.studentID,
				stu.firstName,
				stu.lastName
			FROM
				`student` stu
			WHERE
				stu.studentID
				IN
				(
					SELECT
						studentID
					FROM
						`register-student`
					WHERE
						subjectID = '%s' AND
						registerID =
						(
							SELECT
								registerID
							FROM
								registerinfo
							WHERE
								term = '%s' AND
								year = '%s'
						)
				)
			",
			mysql_real_escape_string($subjectID),
			mysql_real_escape_string($term),
			mysql_real_escape_string($year)
		);
		$objQuery = mysql_query($strSQL);
		if($objQuery&&mysql_num_rows($objQuery)>0){
			while($row=mysql_fetch_array($objQuery)){
				$data['data'][] = array(
						'cardID'=>'<span style="display:none;">'.$row['cardID'].'</span>',
						'secondCardID'=>'<span style="display:none;">'.$row['secondCardID'].'</span>',
						'studentID'=>$row['studentID'],
						'firstName'=>$row['firstName'],
						'lastName'=>$row['lastName'],
						'score'=>'<input type="text" name="score" />'
				);
			}
		} else {
			$data['data'][] = array(
					'cardID'=>'',
					'secondCardID'=>'',
					'studentID'=>'',
					'firstName'=>'ไม่พบข้อมูล',
					'lastName'=>'',
					'score'=>''
			);
		}
		echo json_encode($data);
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
	} elseif($type=="insList"){
		$strSQL = 'SELECT * FROM `instructor`;';
		$objQuery = mysql_query($strSQL);
		$data = '<option value="0">ไม่ระบุ</option>';
		if(mysql_num_rows($objQuery)>=1){
			while($row = mysql_fetch_array($objQuery)){
				$data.='<option value="'.$row['instructorID'].'">'.$row['firstName'].' '.$row['lastName'].'</option>';
			}
		}
		echo $data;
	} elseif($type=="card"){
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
	} elseif($type=="getScoreList"){
		$term = getTerm();
		$year = getYear();
		$instructorID = $confUserID;
		$subjectID = $_POST['subjectID'];
		$strSQL = sprintf(
			"
			SELECT
				*
			FROM
				`scoreinfo`
			WHERE
				subjectID = '%s' AND
				registerID =
					(
					SELECT
						registerID
					FROM
						`registerinfo`
					WHERE
						term = '%s' AND
						year = '%s'
					)
				ORDER BY
					date
				ASC
			",
			mysql_real_escape_string($subjectID),
			mysql_real_escape_string($term),
			mysql_real_escape_string($year)
		);
		$objQuery = mysql_query($strSQL);
		if(mysql_num_rows($objQuery)>=1){
			while($row = mysql_fetch_array($objQuery)){
				$data.='<option value="'.$row['scoreID'].'">'.($row['type']=='TASK'?'ชิ้นงาน':($row['type']=='QUIZ'?'ตอบคำถาม':'สอบ')).' '.$row['maxScore'].' คะแนน ('.(date("d/m/Y H:i")).')</option>';
			}
		} else {
			$data = '<option value="0">ไม่พบข้อมูลลงคะแนน</option>';
		}
		echo $data;
	} elseif($type=="regInsSubList"){
		$term = getTerm();
		$year = getYear();
		$instructorID = $confUserID;
		$strSQL = 'SELECT sub.subjectID AS subjectID, sub.name AS name, sub.type AS type FROM `register-subject` regsub, `registerinfo` reg, `subject` sub WHERE reg.registerID = regsub.registerID AND reg.term="'.$term.'" AND reg.year="'.$year.'" AND regsub.subjectID = sub.subjectID AND regsub.instructorID="'.$instructorID.'";';
		$objQuery = mysql_query($strSQL);
		if(mysql_num_rows($objQuery)>=1){
			while($row = mysql_fetch_array($objQuery)){
				$data.='<option value="'.$row['subjectID'].'">'.$row['subjectID'].' '.$row['name'].' ('.($row['type']=='BASIC'?'พื้นฐาน':'เพิ่มเติม').')</option>';
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
			$strSQL = "SELECT stu.studentID AS studentID, stu.firstName AS firstName, stu.lastName AS lastName, stu.gradeYear AS gradeYear FROM `register-subject` regsub, `registerinfo` reg, `student` stu WHERE reg.registerID = regsub.registerID AND reg.term='$term' AND reg.year='$year' AND regsub.subjectID = '$subjectID' AND regsub.gradeYear = stu.gradeYear AND stu.status='NORMAL' AND stu.studentID NOT IN (SELECT regstu.studentID FROM `register-student` regstu WHERE regstu.subjectID=regsub.subjectID AND regstu.registerID=regsub.registerID );";
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
		if($atdID){
			$strSQL = "SELECT subjectID, registerID,date FROM `attendanceinfo` WHERE attendanceID = '$atdID';";
			$objQuery = mysql_query($strSQL);
			if($objQuery){
				$row = mysql_fetch_array($objQuery);
				$subjectID = $row['subjectID'];
				$registerID = $row['registerID'];
				$startDateTime = $row['date'];
				if($cardID||$studentID){
					if($cardID){
						$studentID = getStdFromCard($cardID);
					}
					if($studentID){
						$stdIsReg = isStdRegis($studentID,$atdID);
						if($stdIsReg){
							$late = $_SESSION['atdLate'];
							if(!isCheckedIn($studentID, $atdID)){
								if(date('Y-m-d H:i:s',strtotime($startDateTime.' +'.$late.' minute 2 second'))>date('Y-m-d H:i:s',strtotime('Today '.$time))) $status = 'ONTIME'; else $status = 'LATE';
								$strSQL = "INSERT INTO `studentattendance` VALUES('$atdID','$studentID','$subjectID','$registerID','$status')";
								$objQuery = mysql_query($strSQL);
								if($objQuery){
									$data['status'] = "SUCCESS";
									$data['data'][] = array(
											"responseText"=>"Added",
											"studentID"=>getCardInfo($studentID)
									);
								} else {
									$data['status'] = "FAIL";
									$data['data'][] = array(
											"reason"=>"SaveFail",
											"strSQL"=>$strSQL
									);
								}
							} else {
								$data['status'] = "FAIL";
								$data['data'][] = array(
										"reason"=>"CheckedIn",
										"strSQL"=>$strSQL
								);
							}
						} else {
							$data['status'] = "FAIL";
							$data['data'][] = array(
									"reason"=>"NotFoundReg",
									"strSQL"=>$strSQL
							);
						}
					} else {
						$data['status'] = "FAIL";
						$data['data'][] = array(
								"reason"=>"NotFoundCard",
								"cardID"=>$_POST['cardID']
						);
					}
				}
			} else {
				$data['status'] = "FAIL";
				$data['data'][] = array(
					"reason"=>"NotFoundAtd",
					"atdID"=>$atdID
				);
			}
		} else {
			$data['status'] = "FAIL";
			$data['data'][] = array("reason"=>"NotLogIn");
		}
		echo json_encode($data);
	} elseif($type=='createAtd'){
		$term = $_POST['term'];
		$year = $_POST['year'];
		$subjectID = $_POST['subjectID'];
		$strSQL = "SELECT attendanceID,date FROM attendanceinfo WHERE subjectID = '$subjectID' AND registerID = (SELECT registerID FROM registerinfo WHERE term='$term' AND year='$year') AND date LIKE '".date("Y-m-d")."%'";
		$objQuery = mysql_query($strSQL);
		if(mysql_num_rows($objQuery)==0){
			$strSQL = "SELECT regsub.registerID FROM `register-subject` regsub, `registerinfo` reg WHERE reg.term = '$term' AND reg.year = '$year' AND reg.registerID = regsub.registerID AND regsub.subjectID = '$subjectID'";
			$objQuery = mysql_query($strSQL);
			if(mysql_num_rows($objQuery)==1){
				$row = mysql_fetch_array($objQuery);
				$registerID = $row['registerID'];
				$late = $_POST['late'];
				$time = $_POST['time'];
				$strSQL = 'INSERT INTO attendanceinfo VALUES (NULL,"'.$subjectID.'","'.$registerID.'","'.date('Y-m-d H:i:s',strtotime('Today '.$time)).'")';
				$objQuery = mysql_query($strSQL);
				if($objQuery){
					$atdID = mysql_insert_id();
					$_SESSION['atdID'] = $atdID;
					$_SESSION['atdLate'] = $late;
					$_SESSION['atdStart'] = $time;
					$data['status'] = "SUCCESS";
					$data['atdID'] = $atdID;
				} else {
					$data['status']  = "FAIL";
					$data['strSQL'] = $strSQL;
				}
			} else {
				$data['status'] = "NOTFOUND";
				$data['strSQL'] = $strSQL;
			}
		} else {
			$row = mysql_fetch_array($objQuery);
			$late = $_POST['late'];
			$atdID = $row['attendanceID'];
			$time = $row['date'];
			$_SESSION['atdID'] = $atdID;
			$_SESSION['atdLate'] = $late;
			$_SESSION['atdStart'] = date("H:i:s",strtotime($time));
			$data['status'] = "SUCCESS";
			$data['atdID'] = $atdID;
		}
		echo json_encode($data);
	} elseif($type=='cardHolder'){
		$studentID = $_POST['studentID'];
		$cardID1 = $_POST['cardID1'];
		$cardID2 = $_POST['cardID2'];
		$strSQL = "INSERT INTO card VALUES('".$cardID1."','".$cardID2."','".$studentID."')";
		$objQuery = mysql_query($strSQL);
		if($objQuery){
			echo '{"status":"success"}';
		}
	} elseif($type=='addStudent'){
		$studentID = $_POST['studentID'];
		$personalID = $_POST['personalID'];
		$firstName = $_POST['firstName'];
		$lastName = $_POST['lastName'];
		$gender = $_POST['gender'];
		$cardID = $_POST['cardID'];
		if($cardID=='') $cardID = 'NULL'; else $cardID="'$cardID'";
		$secondCardID = $_POST['secondCardID'];
		if($secondCardID=='') $secondCardID = 'NULL'; else $secondCardID="'$secondCardID'";
		$gradeYear = $_POST['gradeYear'];
		$instructorID = $_POST['instructorID'];
		if($instructorID==0) $instructorID = 'NULL'; else $instructorID="'$instructorID'";
		$password = randomPassword(6);
		$status = 'NORMAL';
		$strSQL = "SELECT * FROM `student` WHERE studentID='$studentID' OR personalID='$personalID';";
		$objQuery = mysql_query($strSQL);
		if(mysql_num_rows($objQuery)<1){
			$strSQL = "INSERT INTO `student` VALUES ('$studentID','$personalID','$firstName','$lastName','$gender',$cardID,$secondCardID,'$gradeYear',$instructorID,'$password','$status');";
			$objQuery = mysql_query($strSQL);
			if($objQuery){
				$data['status'] = 'OK';
				$data['studentID'] = $studentID;
				$data['password'] = $password;
			} else {
				$data['status'] = 'ERROR';
				$data['strSQL'] = $strSQL;
			}
		} else {
			$data['status'] = 'EXIST';
			$data['studentID'] = $studentID;
		}
		echo json_encode($data);
	} elseif($type=='regStudent'){
		$term = $_POST['term'];
		$year = $_POST['year'];
		$subjectID = $_POST['subjectID'];
		if($term&&$year&&$subjectID){
			$strSQL = "INSERT INTO `register-student` ";
			$strSQL.= "SELECT stu.studentID AS studentID, regsub.subjectID, regsub.registerID, NULL FROM `register-subject` regsub, `registerinfo` reg, `student` stu WHERE reg.registerID = regsub.registerID AND reg.term='$term' AND reg.year='$year' AND regsub.subjectID = '$subjectID' AND regsub.gradeYear = stu.gradeYear AND stu.status='NORMAL' AND stu.studentID NOT IN (SELECT regstu.studentID FROM `register-student` regstu WHERE regstu.subjectID=regsub.subjectID AND regstu.registerID=regsub.registerID );";
			$objQuery = mysql_query($strSQL);
			if($objQuery){
				echo "OK";
			} else {
				echo "ERROR";
			}
		} else {
			echo "UNVALID";
		}
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
