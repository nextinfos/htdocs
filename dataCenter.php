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
function isStuReg($studentID,$subjectID,$term,$year){
	$strSQL = sprintf(
			"
		SELECT
			regstu.studentID
		FROM
			`register-student` regstu
		WHERE
			regstu.studentID = '%s'  AND
			regstu.subjectID = '%s' AND
			regstu.registerID = (
					SELECT
						registerID
					FROM
						`registerinfo`
					WHERE
						`term` = '%s' AND
						`year` = '%s'
				)
		LIMIT 1
		",
			mysql_real_escape_string($studentID),
			mysql_real_escape_string($subjectID),
			mysql_real_escape_string($term),
			mysql_real_escape_string($year)
	);
	$objQuery = mysql_query($strSQL);
	if($objQuery&&mysql_num_rows($objQuery)==1){
		$row = true;
	} else {
		$row = false;
	}
	return $row;
}
function regStudent($studentID,$subjectID,$term,$year){
	$strSQL = sprintf(
			"
			INSERT INTO
				`register-student`
				(studentID,subjectID,registerID,grade)
				(
					SELECT
						'%s','%s',registerID,NULL
					FROM
						`registerinfo`
					WHERE
						term = '%s' AND
						year = '%s'
				)		
			",
			mysql_real_escape_string($studentID),
			mysql_real_escape_string($subjectID),
			mysql_real_escape_string($term),
			mysql_real_escape_string($year)
	);
	$objQuery = mysql_query($strSQL);
	if($objQuery)
		return true;
	else{
		return $strSQL;
	}
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
function getStuRegList($subjectID,$instructorID,$year,$term){
	$strSQL = sprintf(
			"
			SELECT
				stu.cardID,
				stu.secondCardID,
				stu.studentID,
				stu.firstName,
				stu.lastName,
				regstu.grade
			FROM
				`student` stu INNER JOIN `register-student` regstu
				ON stu.studentID = regstu.studentID
			WHERE
				regstu.subjectID = '%s' AND
				regstu.registerID =
				(
					SELECT
						registerID
					FROM
						registerinfo
					WHERE
						term = '%s' AND
						year = '%s'
				)
			",
			mysql_real_escape_string($subjectID),
			mysql_real_escape_string($term),
			mysql_real_escape_string($year)
	);
	return mysql_query($strSQL);
}
function checkScoreExist($scoreID,$studentID){
	$strSQL = sprintf(
			"
			SELECT
				*
			FROM
				`studentscore` stusco 
			WHERE
				stusco.scoreID = '%s' AND
				stusco.studentID = '%s'
			",
			mysql_real_escape_string($scoreID),
			mysql_real_escape_string($studentID)
	);
	$objQuery = mysql_query($strSQL);
	if($objQuery&&mysql_num_rows($objQuery)>=1)
		return true;
	else
		return false;
}
function scoreModify($scoreID,$studentID,$score){
	$strSQL = sprintf(
			"
			UPDATE
				`studentscore` stusco
			SET
				stusco.score = '%s'
			WHERE
				stusco.scoreID = '%s' AND
				stusco.studentID = '%s'
			",
			mysql_real_escape_string($score),
			mysql_real_escape_string($scoreID),
			mysql_real_escape_string($studentID)
	);
	$objQuery = mysql_query($strSQL);
	if($objQuery)
		return true;
	else
		return false;
}
function scoreSet($scoreID,$studentID,$score){
	if(checkScoreExist($scoreID,$studentID)){
		$result = scoreModify($scoreID,$studentID,$score);
	} else {
		$strSQL = sprintf(
				"
				INSERT INTO 
					`studentscore`
				(
					SELECT
						scoreID,'%s',subjectID,registerID,'%s'
					FROM
						`scoreinfo`
					WHERE
						scoreID = '%s'
				)
				",
				mysql_real_escape_string($studentID),
				mysql_real_escape_string($score),
				mysql_real_escape_string($scoreID)
		);
		$objQuery = mysql_query($strSQL);
		if($objQuery)
			$result = true;
		else
			$result = false;
	}
	return $result;
}
function gradeSet($subjectID, $term, $year, $studentID, $grade){
	$strSQL = sprintf(
			"
			UPDATE
				`register-student`
			SET
				grade = '%s'
			WHERE
				subjectID = '%s' AND
				studentID = '%s' AND
				registerID = 
				(
				SELECT
					registerID
				FROM
					`registerinfo`
				WHERE
					`term` = '%s' AND
					`year` = '%s'
				)
			",
			mysql_real_escape_string($grade),
			mysql_real_escape_string($subjectID),
			mysql_real_escape_string($studentID),
			mysql_real_escape_string($term),
			mysql_real_escape_string($year)
	);
	$objQuery = mysql_query($strSQL);
	if($objQuery)
		return true;
	else
		return false;
}
function getScore($scoreID,$studentID){
	$strSQL = sprintf(
			"
				SELECT
					score
				FROM
					`studentscore`
				WHERE
					scoreID = '%s' AND
					studentID = '%s'
				",
			mysql_real_escape_string($scoreID),
			mysql_real_escape_string($studentID)
	);
	$objQuery = mysql_query($strSQL);
	if($objQuery&&mysql_num_rows($objQuery)>0){
		$row = mysql_fetch_array($objQuery);
		$result = $row['score'];
	} else {
		$result = '';
	}
	return $result;
}
function getGrade($studentID,$subjectID,$term,$year){
	$strSQL = sprintf(
			"
				SELECT
					grade
				FROM
					`register-student`
				WHERE
					studentID = '%s' AND
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
				",
			mysql_real_escape_string($studentID),
			mysql_real_escape_string($subjectID),
			mysql_real_escape_string($term),
			mysql_real_escape_string($year)
	);
	$objQuery = mysql_query($strSQL);
	if($objQuery){
		$row = mysql_fetch_array($objQuery);
		return $row['grade'];
	} else return false;
}
function checked($v1,$v2){
	if($v1==$v2) return ' checked'; else return '';
}
function selected($v1,$v2){
	if($v1==$v2) return ' selected'; else return '';
}
function getAvgGrade($studentID,$term,$year){
	return '--';
}
//----------------Start Condition
if($action=="get"){
//----------------Action = GET	
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
// 					$data['data'][] = array(
// 							"id"=> '',
// 							"name"=> 'ไม่พบข้อมูล',
// 							"status"=> '');
					$data['data'] = NULL;
				}
			}
		}
		echo json_encode($data);
	} elseif($type=="stuScoList"){
		$subjectID = $_REQUEST['subjectID'];
		$scoreType = $_REQUEST['scoreType'];
		$addStatus = $_REQUEST['addStatus'];
		$instructorID = $confUserID;
		$term = getTerm();
		$year = getYear();
		$scoreID = $_REQUEST['scoreID'];
		$objQuery = getStuRegList($subjectID, $instructorID, $year, $term);
		if($objQuery&&mysql_num_rows($objQuery)>0){
			while($row=mysql_fetch_array($objQuery)){
				if($scoreType=='GRADE'){
					$grade = getGrade($row['studentID'],$subjectID,$term,$year);
					$select = '<select name="score" style="width: 80px;">';
					$select.= '<option value="">--</option>';
					$select.= '<option value="4"'.selected($grade, '4').'>4.0</option>';
					$select.= '<option value="3.5"'.selected($grade, '3.5').'>3.5</option>';
					$select.= '<option value="3"'.selected($grade, '3').'>3.0</option>';
					$select.= '<option value="2.5"'.selected($grade, '2.5').'>2.5</option>';
					$select.= '<option value="2"'.selected($grade, '2').'>2.0</option>';
					$select.= '<option value="1.5"'.selected($grade, '1.5').'>1.5</option>';
					$select.= '<option value="1"'.selected($grade, '1').'>1.0</option>';
					$select.= '<option value="0"'.selected($grade, '0').'>0</option>';
					$select.= '<option value="W"'.selected($grade, 'W').'>W</option>';
					$select.= '</select>';
					$data['data'][] = array(
							'cardID'=>'<span style="display:none;">'.$row['cardID'].'</span>',
							'secondCardID'=>'<span style="display:none;">'.$row['secondCardID'].'</span>',
							'studentID'=>$row['studentID'],
							'firstName'=>$row['firstName'],
							'lastName'=>$row['lastName'],
							'score'=>'<input type="hidden" name="studentID" value="'.$row['studentID'].'">'.$select
					);
				} else {
					$dataScore = $addStatus=='1'?'':getScore($scoreID,$row['studentID']);
					$data['data'][] = array(
							'cardID'=>'<span style="display:none;">'.$row['cardID'].'</span>',
							'secondCardID'=>'<span style="display:none;">'.$row['secondCardID'].'</span>',
							'studentID'=>$row['studentID'],
							'firstName'=>$row['firstName'],
							'lastName'=>$row['lastName'],
							'score'=>'<input type="hidden" name="studentID" value="'.$row['studentID'].'"><input type="text" name="score" value="'.$dataScore.'" />'
					);
				}
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
	} elseif($type=="stuRegList"){
		$subjectID = $_REQUEST['subjectID'];
		$instructorID = $confUserID;
		$term = getTerm();
		$year = getYear();
		$objQuery = getStuRegList($subjectID, $instructorID, $year, $term);
		if($objQuery&&mysql_num_rows($objQuery)>0){
			while($row=mysql_fetch_array($objQuery)){
				$data['data'][] = array(
						'cardID'=>'<span style="display:none;">'.$row['cardID'].'&nbsp'.$row['secondCardID'].'</span>',
//						'secondCardID'=>'<span style="display:none;">'.$row['secondCardID'].'</span>',
						'studentID'=>$row['studentID'],
						'firstName'=>$row['firstName'],
						'lastName'=>$row['lastName'],
						'grade'=>$row['grade']==NULL?'--':$row['grade']
				);
			}
		} else {
// 			$data['data'][] = array(
// 					'cardID'=>'',
// 					'studentID'=>'',
// 					'firstName'=>'ไม่พบข้อมูล',
// 					'lastName'=>'',
// 					'grade'=>''
// 			);
			$data['data']='';
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
	}elseif($type=="insStuList"){
		$strSQL = sprintf(
		"
		SELECT
			*
		FROM
			student
		WHERE
			instructorID = '%s'
		",
		mysql_real_escape_string($confUserID)
	);
		if($confUserType=='instructor'){
			$objQuery = mysql_query($strSQL);
			if($objQuery&&mysql_num_rows($objQuery)>0){
				while($row=mysql_fetch_array($objQuery)){
					$gendata[] = array(
							'studentID' => $row['studentID'],
							'firstName' => $row['firstName'],
							'lastName' => $row['lastName'],
							'cardID' => "<span style='display:none;'>".$row['cardID']."</span>",
							'secondCardID' => "<span style='display:none;'>".$row['secondCardID']."</span>",
							'atd' => "<button name='viewAtd' data-studentID='".$row['studentID']."'>ดูเวลาเรียน</button>",
							'sco' => "<button name='viewSco' data-studentID='".$row['studentID']."'>ดูคะแนน</button>",
							'grade' => getAvgGrade($row['studentID'], getTerm(), getYear())
					);
				}
				$data['data'] = $gendata;
				echo json_encode($data);
			}
		}
	}elseif($type=="subList"){
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
				DESC
			",
			mysql_real_escape_string($subjectID),
			mysql_real_escape_string($term),
			mysql_real_escape_string($year)
		);
		$objQuery = mysql_query($strSQL);
		if(mysql_num_rows($objQuery)>=1){
			while($row = mysql_fetch_array($objQuery)){
				$data.='<option value="'.$row['scoreID'].'">'.($row['type']=='TASK'?'ชิ้นงาน':($row['type']=='QUIZ'?'ตอบคำถาม':'สอบ')).' '.$row['maxScore'].' คะแนน ('.(date("d/m/Y H:i",strtotime($row['date']))).')</option>';
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
		$strSQL = 'SELECT * FROM `register-subject` regsub, `registerinfo` reg, `subject` sub WHERE regsub.subjectID = sub.subjectID AND reg.registerID = regsub.registerID AND reg.term="'.$term.'" AND reg.year="'.$year.'";';
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
	} elseif($type=="scoreInfo"){
		$scoreID = $_REQUEST['scoreID'];
		$strSQL = sprintf(
			"
			SELECT
				type,maxScore
			FROM
				`scoreinfo`
			WHERE
				scoreID = '%s'
			LIMIT 1
			",
				mysql_real_escape_string($scoreID)
		);
		$objQuery = mysql_query($strSQL);
		if($objQuery){
			$row = mysql_fetch_array($objQuery);
			$data['type'] = $row['type'];
			$data['maxScore'] = $row['maxScore'];
		} else {
			$data = NULL;
		}
		echo json_encode($data);
	} elseif($type=="stuReport"){
		$report = $_REQUEST['report'];
		$studentID = $_REQUEST['studentID'];
		if($report=="atd"){
			$strSQL = sprintf(
			"
			SELECT
				regstu.subjectID,sub.name
			FROM
				`register-student` regstu,
				`subject` sub
			WHERE
				regstu.subjectID = sub.subjectID AND
				regstu.studentID = '%s' AND
				regstu.registerID =
				(
					SELECT
						registerID
					FROM
						`registerinfo`
					WHERE
						`term` = '%s' AND
						`year` = '%s'
				)
			",
				mysql_real_escape_string($studentID),
				mysql_real_escape_string(getTerm()),
				mysql_real_escape_string(getYear())
			);
			$objQuery = mysql_query($strSQL);
			if($objQuery&&mysql_num_rows($objQuery)>0){
				while($row=mysql_fetch_array($objQuery)){
					$preData = NULL;
					$preData['subjectID'] = $row['subjectID'];
					$preData['subjectName'] = $row['name'];
					$strSQL = sprintf(
						"
						SELECT
							attendanceID
						FROM
							`attendanceinfo`
						WHERE
							subjectID = '%s' AND
							registerID =
							(
								SELECT
									registerID
								FROM
									`registerinfo`
								WHERE
									`term` = '%s' AND
									`year` = '%s'
							)
						",
						mysql_real_escape_string($row['subjectID']),
						mysql_real_escape_string(getTerm()),
						mysql_real_escape_string(getYear())
					);
					$objQuery2 = mysql_query($strSQL);
					if($objQuery2&&mysql_num_rows($objQuery2)>0){
						$preData['total'] = mysql_num_rows($objQuery2);
						$check = 0;
						$late = 0;
						while($row2=mysql_fetch_array($objQuery2)){
							$strSQL = sprintf(
									"
									SELECT
										status
									FROM
										`studentattendance`
									WHERE
										attendanceID = '%s' AND
										subjectID = '%s' AND
										studentID = '%s' AND
										registerID =
										(
											SELECT
												registerID
											FROM
												`registerinfo`
											WHERE
												`term` = '%s' AND
												`year` = '%s'
										)
									",
									mysql_real_escape_string($row2['attendanceID']),
									mysql_real_escape_string($row['subjectID']),
									mysql_real_escape_string($studentID),
									mysql_real_escape_string(getTerm()),
									mysql_real_escape_string(getYear())
							);
							$objQuery3 = mysql_query($strSQL);
							if($objQuery3&&mysql_num_rows($objQuery3)>0){
								while($row3=mysql_fetch_array($objQuery3)){
									if($row3['status']=="ONTIME")
										$check++;
									elseif($row3['status']=="LATE")
										$late++;
								}
							}
						}
						$preData['check'] = $check;
						$preData['late'] = $late;
						$preData['abs'] = ($preData['total']-($preData['check']+$preData['late']));
						$preData['percent'] = @round(($preData['check']+$preData['late'])/$preData['total']*100,2).'%';
						$data['data'][] = $preData;
					} else {
						$preData['check'] = '--';
						$preData['late'] = '--';
						$preData['abs'] = '--';
						$preData['total'] = '--';
						$preData['percent'] = '--';
						$data['data'][] = $preData;
					}
				}
			} else {
				$data['data'] == NULL;
			}
		} elseif($report=="score"){
			$strSQL = sprintf(
					"
			SELECT
				regstu.subjectID,sub.name,regstu.grade
			FROM
				`register-student` regstu,
				`subject` sub
			WHERE
				regstu.subjectID = sub.subjectID AND
				regstu.studentID = '%s' AND
				regstu.registerID =
				(
					SELECT
						registerID
					FROM
						`registerinfo`
					WHERE
						`term` = '%s' AND
						`year` = '%s'
				)
			",
					mysql_real_escape_string($studentID),
					mysql_real_escape_string(getTerm()),
					mysql_real_escape_string(getYear())
			);
			$objQuery = mysql_query($strSQL);
			if($objQuery&&mysql_num_rows($objQuery)>0){
				while($row=mysql_fetch_array($objQuery)){
					$preData['grade'] = $row['grade']!=''?$row['grade']:'--';
					$preData['subjectID'] = $row['subjectID'];
					$preData['subjectName'] = $row['name'];
					$strSQL = sprintf(
							"
						SELECT
							scoreID,maxScore
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
									`term` = '%s' AND
									`year` = '%s'
							)
						",
							mysql_real_escape_string($row['subjectID']),
							mysql_real_escape_string(getTerm()),
							mysql_real_escape_string(getYear())
					);
					$objQuery2 = mysql_query($strSQL);
					if($objQuery2&&mysql_num_rows($objQuery2)>0){
						$maxScore = 0;
						$sumScore = 0;
						while($row2=mysql_fetch_array($objQuery2)){
							$maxScore += $row2['maxScore'];
							$strSQL = sprintf(
									"
									SELECT
										score
									FROM
										`studentscore`
									WHERE
										scoreID = '%s' AND
										subjectID = '%s' AND
										studentID = '%s' AND
										registerID =
										(
											SELECT
												registerID
											FROM
												`registerinfo`
											WHERE
												`term` = '%s' AND
												`year` = '%s'
										)
									",
									mysql_real_escape_string($row2['scoreID']),
									mysql_real_escape_string($row['subjectID']),
									mysql_real_escape_string($studentID),
									mysql_real_escape_string(getTerm()),
									mysql_real_escape_string(getYear())
							);
							$objQuery3 = mysql_query($strSQL);
							if($objQuery3&&mysql_num_rows($objQuery3)>0){
								while($row3=mysql_fetch_array($objQuery3)){
									$sumScore+=$row3['score'];
								}
							}
						}
						if($confUserType=='instructor'){
							$preData['score'] = $sumScore.'/'.$maxScore;
						}
						$data['data'][] = $preData;
					} else {
						$preData['score'] = '--';
						$data['data'][] = $preData;
					}
				}
			} else {
				$data['data'] == NULL;
			}
		}
		echo json_encode($data);
	} elseif($type=="regGradeYearList"){
		$term = $_REQUEST['term'];
		$year = $_REQUEST['year'];
		$strSQL = sprintf(
				"
				SELECT
					regsub.gradeYear
				FROM
					`register-subject` regsub
					INNER JOIN
					`subject` sub
					ON
					regsub.subjectID = sub.subjectID
				WHERE
					registerID = (
						SELECT
							registerID
						FROM
							`registerinfo`
						WHERE
							term = '%s' AND
							year = '%s'
					)
				GROUP BY
					regsub.gradeYear
				ORDER BY
					regsub.gradeYear ASC
				",
				mysql_real_escape_string($term),
				mysql_real_escape_string($year)
		);
		$objQuery = mysql_query($strSQL);
		if($objQuery&&mysql_num_rows($objQuery)>0){
			while($row = mysql_fetch_array($objQuery)){
				$result.= '<option value="'.$row['gradeYear'].'">'.getGradeYearName($row['gradeYear']).'</option>';
			}
		} else {
			$result = '<option value="0">ไม่พบชั้นปี</optino>';
		}
		echo $result;
	} elseif($type=="stuCanRegPackage"){
		$term = $_REQUEST['term'];
		$year = $_REQUEST['year'];
		$gradeYear = $_REQUEST['gradeYear'];
		$strSQL = sprintf(
				"
				SELECT
					sub.subjectID,
					sub.name
				FROM
					`register-subject` regsub,
					`subject` sub
				WHERE
					regsub.subjectID = sub.subjectID AND
					regsub.gradeYear = '%s' AND
					regsub.registerID = 
					(
						SELECT
							registerID
						FROM
							`registerinfo`
						WHERE
							term = '%s' AND
							year = '%s'
					)
				",
				mysql_real_escape_string($gradeYear),
				mysql_real_escape_string($term),
				mysql_real_escape_string($year)
		);
		$objQuery = mysql_query($strSQL);
		if($objQuery&&mysql_num_rows($objQuery)>0){
			while($row=mysql_fetch_assoc($objQuery)){
				$predata = NULL;
				$predata['subjectID'] = $row['subjectID'];
				$predata['name'] = $row['name'];
				$data['subject'][] = $predata;
// 				$data['subject'][$row['subjectID']] = $row['name'];
			}
		}
		$strSQL = sprintf(
				"
				SELECT
					stu.studentID,
					stu.firstName,
					stu.lastName,
					stu.gradeYear
				FROM
					`student` stu
				WHERE
					gradeYear = '%s'
				",
				mysql_real_escape_string($gradeYear)
		);
		$objQuery = mysql_query($strSQL);
		if($objQuery&&mysql_num_rows($objQuery)>0){
			while($row = mysql_fetch_array($objQuery)){
				$predata = NULL;
				$predata['studentID'] = $row['studentID'];
				$predata['firstName'] = $row['firstName'];
				$predata['lastName'] = $row['lastName'];
				$predata['gradeYear'] = getGradeYearName($row['gradeYear']);
				$data['data'][] = $predata;
				foreach ($data['subject'] as $mval){
					foreach ($mval as $key=>$val){
						if($key=='subjectID'){
							$predata = NULL;
							$data['isStuReg'][$row['studentID']][$val] = isStuReg($row['studentID'], $val, $term, $year);
	 					}
					}
				}
			}
		} else {
			$data['data'] = '';
			$data['srtSQL'] = $strSQL;
		}
		echo json_encode($data);
	} elseif($type=='gradeCal'){
		$tag = $_REQUEST['tag'];
		$subjectID = $_REQUEST['subjectID'];
		$normalize = $_REQUEST['normalize'];
		if($tag=='info'){
			$strSQL = sprintf(
					"
					SELECT
						maxScore,
						type
					FROM
						scoreinfo
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
					",
					mysql_real_escape_string($subjectID),
					mysql_real_escape_string(getTerm()),
					mysql_real_escape_string(getYear())
			);
			$objQuery = mysql_query($strSQL);
			if($objQuery&&mysql_num_rows($objQuery)>0){
				$foundMid = false;
				$beforeMidScore = 0;
				$midScore = 0;
				$afterMidScore = 0;
				$finalScore = 0;
				while($row=mysql_fetch_assoc($objQuery)){
					if($row['type']!='EXAM'){
						if(!$foundMid){
							$beforeMidScore += $row['maxScore'];
						} else {
							$afterMidScore += $row['maxScore'];
						}
					} else {
						if(!$foundMid){
							$midScore += $row['maxScore'];
							$foundMid = true;
						} else {
							$finalScore += $row['maxScore'];
						}
					}
				}
				$return['beforeMidScore'] = $beforeMidScore;
				$return['midScore'] = $midScore;
				$return['afterMidScore'] = $afterMidScore;
				$return['finalScore'] = $finalScore;
				if($beforeMidScore>0&&$midScore>0&&$afterMidScore>0&&$finalScore>0){
					$return['status'] = 'SUCCESS';
					$maxScore = ($beforeMidScore+$midScore+$afterMidScore+$finalScore);
					$return['maxScore'] = $maxScore;
				} else {
					$return['status'] = 'INVALID';
				}
			} else {
				if($objQuery){
					$return['status'] = 'INVALID';
				} else {
					$return['status'] = 'ERROR';
				}
			}
			$return['subjectID'] = $subjectID;
		} elseif($tag=='preview'){
			$max = $_REQUEST['max'];
			if($subjectID&&$max){
				$max = json_decode($max);
				$maxPass = true;
				for($i=0;$i<sizeof($max);$i++){
					if($max[$i]==''||$max[$i]==null||!isset($max[$i])){
						$maxPass = false;
						break;
					}
				}
				if($maxPass){
					$maxScore = $max[7];
					$strSQL = sprintf(
							"
							SELECT
								stu.studentID,stu.firstName,stu.lastName,SUM(stusco.score) AS score
							FROM
								`studentscore` stusco RIGHT JOIN `student` stu ON stusco.studentID = stu.studentID
							WHERE
								stusco.scoreID IN
								(
								SELECT
									scoreID
								FROM
									scoreinfo
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
								) OR (stu.studentID IN (
									SELECT
										studentID
									FROM
										`register-student` regstu
									WHERE
										regstu.subjectID = '%s' AND
										regstu.registerID =
										(
											SELECT
												registerID
											FROM
												registerinfo
											WHERE
												term = '%s' AND
												year = '%s'
										)
								) AND stusco.subjectID IS NULL
							)
							GROUP BY
								studentID
							",
							mysql_real_escape_string($subjectID),
							mysql_real_escape_string(getTerm()),
							mysql_real_escape_string(getYear()),
							mysql_real_escape_string($subjectID),
							mysql_real_escape_string(getTerm()),
							mysql_real_escape_string(getYear()),
							mysql_real_escape_string($subjectID)
					);
					$objQuery = mysql_query($strSQL);
					if($objQuery&&mysql_num_rows($objQuery)){
						while($row=mysql_fetch_assoc($objQuery)){
							$preData = $row;
							if($row['score']=='') $preData['score'] = 0;
							$grade = gradeCal(0,$max,0,$row['score']);
							$select = '<select name="grade" style="width: 80px;">';
// 							$select.= '<option value="">--</option>';
							$select.= '<option value="'.$grade.'" selected>'.$grade.'</option>';
							$select.= '<option value="W">W</option>';
							$select.= '</select>';
							$preData['grade'] = '<input type="hidden" name="studentID" value="'.$row['studentID'].'">'.$select;
							$preData['score'] = $preData['score'].'/'.$maxScore;
							$return['data'][] = $preData;
						}
						$return['status'] = 'SUCCESS';
					} else {
						$return['status'] = 'FAIL';
						$return['strSQL'] = $strSQL;
					}
				} else {
					$return['status'] = 'MAXP';
					$return['data'] = '';
				}
			} else {
				$return['data'] = '';
			}
		}
		echo json_encode($return);
	} elseif($type=="studentStatus"){
		$studentID = $_REQUEST['studentID'];
		$data = '';
		$strSQL = sprintf(
				"
				SELECT
					status
				FROM
					student
				WHERE
					studentID = '%s'
				",
				mysql_real_escape_string($studentID)
				);
		$objQuery = mysql_query($strSQL);
		if($objQuery&&mysql_num_rows($objQuery)>0){
			$row = mysql_fetch_assoc($objQuery);
			$data .= '<option value="NORMAL"'.selected($row['status'], 'NORMAL').'>ปกติ</option>';
			$data .= '<option value="GRADUATE" '.selected($row['status'], 'GRADUATE').'>สำเร็จการศึกษา</option>';
			$data .= '<option value="SUSPEND"'.selected($row['status'], 'SUSPEND').'>พ้นสภาพ</option>';
		}
		echo $data;
	}
} elseif($action=="set"){
	if($type=="atdList"){
		if($atdID){
			$strSQL = sprintf(
				"
				SELECT
					subjectID,
					registerID,
					date
				FROM
					`attendanceinfo`
				WHERE
					attendanceID = '%s'
				",
				mysql_real_escape_string($atdID)
			);
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
// 						$stdIsReg = isStdRegis($studentID,$atdID);
						if(isStdRegis($studentID,$atdID)){
							$late = $_SESSION['atdLate'];
							if(!isCheckedIn($studentID, $atdID)){
								if(date('Y-m-d H:i:s',strtotime($startDateTime.' +'.$late.' minute 2 second'))>date('Y-m-d H:i:s',strtotime('Today '.$time))) $status = 'ONTIME'; else $status = 'LATE';
								$strSQL = "INSERT INTO `studentattendance` VALUES('$atdID','$studentID','$subjectID','$registerID','$status')";
								$objQuery = mysql_query($strSQL);
								if($objQuery){
									$data['status'] = "SUCCESS";
									$cardInfo = getCardInfo($studentID);
									$data['data'][] = array(
											"responseText"=>"Added",
											"studentID"=>$cardInfo
									);
									$data['atdData'] = array(
										"id"=>$cardInfo['data'][0]['id'],
										"name"=>$cardInfo['data'][0]['fname']."   ".$cardInfo['data'][0]['lname'],
										"status"=>ucfirst(strtolower($status))
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
	} elseif($type=='addInstructor'){
		$instructorID = $_POST['instructorID'];
		$firstName = $_POST['firstName'];
		$lastName = $_POST['lastName'];
		$password = randomPassword(6);
		$strSQL = "SELECT * FROM `instructor` WHERE instructorID='$instructorID';";
		$objQuery = mysql_query($strSQL);
		if(mysql_num_rows($objQuery)<1){
			$strSQL = "INSERT INTO `instructor` VALUES ('$instructorID','$firstName','$lastName','$password',0);";
			$objQuery = mysql_query($strSQL);
			if($objQuery){
				$data['status'] = 'OK';
				$data['instructorID'] = $instructorID;
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
	} elseif($type=="setScore"){
		$scoreID = $_REQUEST['scoreID'];
		$score = json_decode($_REQUEST['score']);
		$studentID = json_decode($_REQUEST['studentID']);
		$scoreType = $_REQUEST['scoreType'];
		$subjectID = $_REQUEST['subjectID'];
		$result['sizeOf'] = sizeof($score);
		for($i=0;$i<sizeof($score);$i++){
			$result['data'][$studentID[$i]] = $score[$i];
			if($score[$i]!=''){
				if($scoreType == 'GRADE'){
					if(!gradeSet($subjectID,getTerm(),getYear(), $studentID[$i], $score[$i])){
						$result['error'][$studentID[$i]] = TRUE;
					}
				} else {
					if(!scoreSet($scoreID, $studentID[$i], $score[$i])){
						$result['error'][$studentID[$i]] = TRUE;
					}
				}
			}
		} 
		echo json_encode($result);
	} elseif($type=="setScoreInfo"){
		$subjectID = $_REQUEST['subjectID'];
		$scoreType = $_REQUEST['scoreType'];
		$scoreMax = $_REQUEST['scoreMax'];
		$scoreID = $_REQUEST['scoreID'];
		$addStatus = $_REQUEST['addStatus'];
		$date = date("Y-m-d H:i:s",time());
		if($addStatus=='1'){
			if($scoreType=='GRADE'){
				$data['status'] = 'SUCCESS';
				echo json_encode($data);
				exit();
			}
			$strSQL = sprintf(
				"
				INSERT INTO
					scoreinfo
					(
					SELECT
						NULL,
						'%s',
						registerID,
						'%s',
						'%s',
						'%s'
					FROM
						registerinfo
					WHERE
						term = '%s' AND
						year = '%s'
					)
				",
				mysql_real_escape_string($subjectID),
				mysql_real_escape_string($date),
				mysql_real_escape_string($scoreType),
				mysql_real_escape_string($scoreMax),
				mysql_real_escape_string(getTerm()),
				mysql_real_escape_string(getYear())
			);
			$objQuery = mysql_query($strSQL);
			$scoreID = mysql_insert_id();
			$data['status'] = 'SUCCESS';
			$data['strSQL']=$strSQL;
			$data['scoreID'] = $scoreID;
		} elseif($addStatus=='2') {
			$strSQL = sprintf(
				"
				UPDATE
					`scoreinfo`
				SET
					type = '%s',
					maxScore = '%s'
				WHERE
					scoreID = '%s'
				",
					mysql_real_escape_string($scoreType),
					mysql_real_escape_string($scoreMax),
					mysql_real_escape_string($scoreID)
			);
			$objQuery = mysql_query($strSQL);
			$data['status'] = 'SUCCESS';
			$data['strSQL']=$strSQL;
			$data['scoreID'] = $scoreID;
		} else {
			$data['status']='FAIL';
		}
		echo json_encode($data);
	} elseif($type=='regStudentPackage'){
		$term = $_REQUEST['term'];
		$year = $_REQUEST['year'];
		$studentID = json_decode($_REQUEST['studentID']);
		$data = $_REQUEST['data'];
		if($term&&$year&&$studentID&&$data){
			$error = false;
			foreach ($data as $key => $val){
				$objSubject = json_decode($val);
				$subjectID = $objSubject->subjectID;
				$objData = $objSubject->data;
				foreach ($objData as $dkey => $dval){
					if($dval){
						if(!isStuReg($studentID[$dkey], $subjectID, $term, $year)){
							$res=regStudent($studentID[$dkey], $subjectID, $term, $year);
							if($res===true){
								$success[$subjectID][] = $studentID[$dkey];
							} else {
								$error = true;
								$fail[$subjectID][] = $studentID[$dkey];
								$fail[$subjectID][$studentID[$dkey]][] = $res;
							}
						}
					}
				}
			}
			$result['success'] = $success;
			$result['fail'] = $fail;
			if($error==false){
				$result['status'] = 'SUCCESS';
			} else {
				if(sizeof($success)>0)
					$result['status'] = 'OK';
				else 
					$result['status'] = 'ERROR';
			}
		} else {
			$result['status'] = 'UNVALID';
		}
 		echo json_encode($result);
	} elseif($type=="studentStatus"){
		$studentID = $_REQUEST['studentID'];
		$status = $_REQUEST['status'];
		$data = '';
		$strSQL = sprintf(
				"
				UPDATE
					`student`
				SET
					`status` = '%s'
				WHERE
					`studentID` = '%s'
				",
				mysql_real_escape_string($status),
				mysql_real_escape_string($studentID)
				);
		$objQuery = mysql_query($strSQL);
		if($objQuery){
			$data.='SUCCESS';
		} else {
			$data.="FAIL\n".$strSQL;
		}
		echo $data;
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
