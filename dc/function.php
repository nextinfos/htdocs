<?php
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
?>
