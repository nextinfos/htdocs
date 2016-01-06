<?php
	$studentID = $_REQUEST['studentID'];
	$term = $_REQUEST['term'];
	$year = $_REQUEST['year'];
// 	$confDurningScore = 60;
	$confMidScore = 50;
	$confFinalScore = 50;
	$countRegBasic=0;
	$countRegExtra=0;
	$countBasic=0;
	$countExtra=0;
	$GPAStatus = true;
	$GPACal = 0;
// Pre Set Var
	$preName = $gradeYear = $firstName = $lastName = $insFirstName = $insLastName = $durningScore = $maxFoundMid = $maxBeforeMidScore = $maxMidScore = $maxAfterMidScore = $maxFinalScore = $foundMid = $beforeMidScore = $midScore = $afterMidScore = $finalScore = NULL;
	$strSQL = sprintf(
			"
				SELECT
					sub.subjectID,
					sub.name,
					sub.type,
					sub.weight,
					stu.firstName,
					stu.lastName,
					stu.gender,
					stu.gradeYear,
					ins.firstName AS insFirstName,
					ins.lastName AS insLastName,
					regstu.grade
				FROM
					`register-subject` regsub,
					`register-student` regstu,
					`subject` sub,
					`student` stu,
					`instructor` ins
				WHERE
					sub.type = 'BASIC' AND
					stu.instructorID = ins.instructorID AND
					regstu.subjectID = regsub.subjectID AND
					regstu.subjectID = sub.subjectID AND
					regstu.studentID = '%s' AND
					regstu.studentID = stu.studentID AND
					regstu.registerID = regsub.registerID AND
					regstu.registerID = (
						SELECT
							registerID
						FROM
							`registerinfo`
						WHERE
							term = '%s' AND
							year = '%s'
					)
				ORDER BY
					regstu.subjectID ASC
				",
			mysql_real_escape_string($studentID),
			mysql_real_escape_string($term),
			mysql_real_escape_string($year)
	);
	$objQuery = mysql_query($strSQL);
	if($objQuery&&mysql_num_rows($objQuery)){
		while($row=mysql_fetch_assoc($objQuery)){
			$preName = $row['gender'];
			$gradeYear = $row['gradeYear'];
			$firstName = $row['firstName'];
			$lastName = $row['lastName'];
			$insFirstName = $row['insFirstName'];
			$insLastName = $row['insLastName'];
			$durningScore = NULL;
			$maxFoundMid = false;
			$maxBeforeMidScore = 0;
			$maxMidScore = 0;
			$maxAfterMidScore = 0;
			$maxFinalScore = 0;
			$foundMid = false;
			$beforeMidScore = 0;
			$midScore = 0;
			$afterMidScore = 0;
			$finalScore = 0;
			if(isset($row['grade'])||$row['grade']!=''){
				$strSQL2 = sprintf(
						"
						SELECT
							maxScore,
							type
						FROM
							`scoreinfo`
						WHERE
							subjectID = '%s' AND
							registerID = (
								SELECT
									registerID
								FROM
									`registerinfo`
								WHERE
									term = '%s' AND
									year = '%s'
							)
						",
						mysql_real_escape_string($row['subjectID']),
						mysql_real_escape_string($term),
						mysql_real_escape_string($year)
				);
				$objQuery2 = mysql_query($strSQL2);
				if($objQuery2&&mysql_num_rows($objQuery2)){
					while($row2=mysql_fetch_assoc($objQuery2)){
						if($row2['type']!='EXAM'){
							if(!$maxFoundMid){
								$maxBeforeMidScore += $row2['maxScore'];
							} else {
								$maxAfterMidScore += $row2['maxScore'];
							}
						} else {
							if(!$maxFoundMid){
								$maxMidScore += $row2['maxScore'];
								$maxFoundMid = true;
							} else {
								$maxFinalScore += $row2['maxScore'];
							}
						}
					}
					$maxScore = ($maxBeforeMidScore+$maxMidScore+$maxAfterMidScore+$maxFinalScore);
					$strSQL3 = sprintf(
							"
							SELECT
								stusco.score,
								sco.type
							FROM
								`studentscore` stusco,
								`student` stu,
								`scoreinfo` sco
							WHERE
								stusco.studentID = '%s' AND
								stusco.studentID = stu.studentID AND
								stusco.scoreID = sco.scoreID AND
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
										)
							",
							mysql_real_escape_string($studentID),
							mysql_real_escape_string($row['subjectID']),
							mysql_real_escape_string($term),
							mysql_real_escape_string($year)
					);
					$objQuery3 = mysql_query($strSQL3);
					if($objQuery3&&mysql_num_rows($objQuery3)){
						while($row3=mysql_fetch_assoc($objQuery3)){
							if($row3['type']!='EXAM'){
								if(!$foundMid){
									$beforeMidScore += $row3['score'];
								} else {
									$afterMidScore += $row3['score'];
								}
							} else {
								if(!$foundMid){
									$midScore += $row3['score'];
									$foundMid = true;
								} else {
									$finalScore += $row3['score'];
								}
							}
						}
					}
				}
				$strSQL2 = sprintf(
						"
						SELECT
							grade
						FROM
							`register-student`
						WHERE
							studentID = '%s' AND
							subjectID = '%s' AND
							registerID = (
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
						mysql_real_escape_string($row['subjectID']),
						mysql_real_escape_string($term),
						mysql_real_escape_string($year)
				);
				$objQuery2 = mysql_query($strSQL2);
				if($objQuery2){
					$row2 = mysql_fetch_assoc($objQuery2);
					$grade = $row2['grade'];
				}
				if(!isset($grade)||$grade=='') $grade = '--';
				$predata = NULL;
				$predata['subjectID'] = $row['subjectID'];
				$predata['subjectName'] = $row['name'];
				$predata['hour'] = $row['type']=='BASIC'?'60':'40';
				$predata['weight'] = $row['weight'];
				$countRegBasic += $predata['weight'];
				if($grade!='W'&&$grade!='0')
				$countBasic += $predata['weight'];
				$predata['durningScore'] = $durningScore;
				$predata['midScore'] = normalization(($beforeMidScore+$midScore), ($maxBeforeMidScore+$maxMidScore), $confMidScore,0);
	// 			$predata['midScore'] =  ($beforeMidScore+$midScore).'/'.($maxBeforeMidScore+$maxMidScore);
				$predata['totalDurningScore'] = $predata['durningScore']+$predata['midScore'];
				$predata['finalScore'] =  normalization(($afterMidScore+$finalScore), ($maxAfterMidScore+$maxFinalScore), $confFinalScore,0);
	// 			$predata['finalScore'] =  ($afterMidScore+$finalScore).'/'.($maxAfterMidScore+$maxFinalScore);
				$predata['totalScore'] = $predata['totalDurningScore']+$predata['finalScore'];
				$predata['grade'] = $grade;
				$GPACal += ($row['weight']*$grade);
				if($_REQUEST['pdf']=='1'){
					$predata['beg'] = $predata['grade']=='0'?'':'';
				} else {
					$predata['beg'] = $predata['grade']=='0'?'<input type="text" value="" />':'';
				}
				$dataInfoBasic[] = $predata;
			} else {
				$GPAStatus = false;
				$predata['subjectID'] = $row['subjectID'];
				$predata['subjectName'] = $row['name'];
				$predata['hour'] = $row['type']=='BASIC'?'60':'40';
				$predata['weight'] = $row['weight'];
				$countRegBasic += $predata['weight'];
				$predata['durningScore'] = '--';
				$predata['midScore'] = '--';
				$predata['totalDurningScore'] = '--';
				$predata['finalScore'] = '--';
				$predata['totalScore'] = '--';
				$predata['grade'] = '--';
				$predata['beg'] = '';
				$dataInfoBasic[] = $predata;
			}
		}
	}
	$strSQL = sprintf(
			"
				SELECT
					sub.subjectID,
					sub.name,
					sub.type,
					sub.weight,
					stu.firstName,
					stu.lastName,
					stu.gender,
					stu.gradeYear,
					ins.firstName AS insFirstName,
					ins.lastName AS insLastName,
					regstu.grade
				FROM
					`register-subject` regsub,
					`register-student` regstu,
					`subject` sub,
					`student` stu,
					`instructor` ins
				WHERE
					sub.type = 'EXTRA' AND
					stu.instructorID = ins.instructorID AND
					regstu.subjectID = regsub.subjectID AND
					regstu.subjectID = sub.subjectID AND
					regstu.studentID = '%s' AND
					regstu.studentID = stu.studentID AND
					regstu.registerID = regsub.registerID AND
					regstu.registerID = (
						SELECT
							registerID
						FROM
							`registerinfo`
						WHERE
							term = '%s' AND
							year = '%s'
					)
				ORDER BY
					regstu.subjectID ASC
				",
			mysql_real_escape_string($studentID),
			mysql_real_escape_string($term),
			mysql_real_escape_string($year)
	);
	$objQuery = mysql_query($strSQL);
	if($objQuery&&mysql_num_rows($objQuery)){
		while($row=mysql_fetch_assoc($objQuery)){
			if(isset($row['grade'])||$row['grade']!=''){
				$preName = $row['gender'];
				$gradeYear = $row['gradeYear'];
				$firstName = $row['firstName'];
				$lastName = $row['lastName'];
				$insFirstName = $row['insFirstName'];
				$insLastName = $row['insLastName'];
				$durningScore = NULL;
				$maxFoundMid = false;
				$maxBeforeMidScore = 0;
				$maxMidScore = 0;
				$maxAfterMidScore = 0;
				$maxFinalScore = 0;
				$foundMid = false;
				$beforeMidScore = 0;
				$midScore = 0;
				$afterMidScore = 0;
				$finalScore = 0;
				$strSQL2 = sprintf(
						"
						SELECT
							maxScore,
							type
						FROM
							`scoreinfo`
						WHERE
							subjectID = '%s' AND
							registerID = (
								SELECT
									registerID
								FROM
									`registerinfo`
								WHERE
									term = '%s' AND
									year = '%s'
							)
						",
						mysql_real_escape_string($row['subjectID']),
						mysql_real_escape_string($term),
						mysql_real_escape_string($year)
				);
				$objQuery2 = mysql_query($strSQL2);
				if($objQuery2&&mysql_num_rows($objQuery2)){
					while($row2=mysql_fetch_assoc($objQuery2)){
						if($row2['type']!='EXAM'){
							if(!$maxFoundMid){
								$maxBeforeMidScore += $row2['maxScore'];
							} else {
								$maxAfterMidScore += $row2['maxScore'];
							}
						} else {
							if(!$maxFoundMid){
								$maxMidScore += $row2['maxScore'];
								$maxFoundMid = true;
							} else {
								$maxFinalScore += $row2['maxScore'];
							}
						}
					}
					$maxScore = ($maxBeforeMidScore+$maxMidScore+$maxAfterMidScore+$maxFinalScore);
					$strSQL3 = sprintf(
							"
							SELECT
								stusco.score,
								sco.type
							FROM
								`studentscore` stusco,
								`student` stu,
								`scoreinfo` sco
							WHERE
								stusco.studentID = '%s' AND
								stusco.studentID = stu.studentID AND
								stusco.scoreID = sco.scoreID AND
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
										)
							",
							mysql_real_escape_string($studentID),
							mysql_real_escape_string($row['subjectID']),
							mysql_real_escape_string($term),
							mysql_real_escape_string($year)
					);
					$objQuery3 = mysql_query($strSQL3);
					if($objQuery3&&mysql_num_rows($objQuery3)){
						while($row3=mysql_fetch_assoc($objQuery3)){
							if($row3['type']!='EXAM'){
								if(!$foundMid){
									$beforeMidScore += $row3['score'];
								} else {
									$afterMidScore += $row3['score'];
								}
							} else {
								if(!$foundMid){
									$midScore += $row3['score'];
									$foundMid = true;
								} else {
									$finalScore += $row3['score'];
								}
							}
						}
					}
				}
				$grade = $row['grade'];
				if(!isset($grade)||$grade=='') $grade = '--';
				$predata = NULL;
				$predata['subjectID'] = $row['subjectID'];
				$predata['subjectName'] = $row['name'];
				$predata['hour'] = $row['type']=='BASIC'?'60':'40';
				$predata['weight'] = $row['weight'];
				$countRegExtra += $predata['weight'];
				if($grade!='W'&&$grade!='0')
				$countExtra += $predata['weight'];
				$predata['durningScore'] = $durningScore;
				$predata['midScore'] = normalization(($beforeMidScore+$midScore), ($maxBeforeMidScore+$maxMidScore), $confMidScore,0);
				// 			$predata['midScore'] =  ($beforeMidScore+$midScore).'/'.($maxBeforeMidScore+$maxMidScore);
				$predata['totalDurningScore'] = $predata['durningScore']+$predata['midScore'];
				$predata['finalScore'] =  normalization(($afterMidScore+$finalScore), ($maxAfterMidScore+$maxFinalScore), $confFinalScore,0);
				// 			$predata['finalScore'] =  ($afterMidScore+$finalScore).'/'.($maxAfterMidScore+$maxFinalScore);
				$predata['totalScore'] = $predata['totalDurningScore']+$predata['finalScore'];
				$predata['grade'] = $grade;
				$GPACal += ($row['weight']*$grade);
				if($_REQUEST['pdf']=='1'){
					$predata['beg'] = $predata['grade']=='0'?'':'';
				} else {
					$predata['beg'] = $predata['grade']=='0'?'<input type="text" value="" />':'';
				}
				$dataInfoExtra[] = $predata;
			} else {
				$GPAStatus = false;
				$predata['subjectID'] = $row['subjectID'];
				$predata['subjectName'] = $row['name'];
				$predata['hour'] = $row['type']=='BASIC'?'60':'40';
				$predata['weight'] = $row['weight'];
				$countRegExtra += $predata['weight'];
				$predata['durningScore'] = '--';
				$predata['midScore'] = '--';
				$predata['totalDurningScore'] = '--';
				$predata['finalScore'] = '--';
				$predata['totalScore'] = '--';
				$predata['grade'] = '--';
				$predata['beg'] = '';
				$dataInfoExtra[] = $predata;
			}
		}
	}
	$year = $year+543;
	$preName = $preName=='M'?'ด.ช.':'ด.ญ.';
	$gradeYearName = getGradeYearName($gradeYear);
	$level = getLevelName($gradeYear);
	$instructor = $insFirstName.'&nbsp;&nbsp;&nbsp;'.$insLastName;
	$data = array(
			logo						=>	'images/logo.png',
			schoolName			=>	'โรงเรียนชุมชนบ้านถ้ำสิงห์',
			title						=>	'แบบรายงานผลการเรียนรายบุคคล',
			director				=>	'นายประมวล&nbsp;&nbsp;&nbsp;พรหมศร',
			instructor				=>	$instructor,
			termInfo				=>	"ระดับ$level&nbsp;&nbsp;&nbsp;ภาคเรียนที่ $term&nbsp;&nbsp;&nbsp;ปีการศึกษา $year",
			studentInfo			=>	"$preName$firstName $lastName&nbsp;&nbsp;&nbsp;เลขประจำตัว $studentID&nbsp;&nbsp;&nbsp;ชั้น$gradeYearName",
			durningScore		=>	$confDurningScore,
			midScore				=>	$confMidScore,
			finalScore				=>	$confFinalScore
	);
	$totalReg = $countRegBasic+$countRegExtra;
	$totalGain = $countBasic+$countExtra;
	if($GPAStatus){
		$GPA = number_format(round(($GPACal/$totalReg),2),2);
	} else {
		$GPA = '--';
	}
	if($_REQUEST['pdf']=='1'){
		require 'PDFGradeReport.php';
		exit();
	} else {
	function genData($data){
		$res = '';
		if(sizeof($data)<1){
			$res.= "\n\t\t\t\t\t\t<tr>";
			$res.= "\n\t\t\t\t\t\t\t".'<td class="lefttext merge"></td>';
			$res.= "\n\t\t\t\t\t\t\t".'<td class="lefttext" colspan="8">--</td>';
			$res.= "\n\t\t\t\t\t\t"."</tr>";
		} else {
			foreach ($data as $val){
				$res.= "\n\t\t\t\t\t\t<tr>";
				$res.= "\n\t\t\t\t\t\t\t".'<td class="lefttext">'.$val['subjectID']."</td>";
				$res.= "\n\t\t\t\t\t\t\t".'<td class="lefttext">'.$val['subjectName']."</td>";
				$res.= "\n\t\t\t\t\t\t\t"."<td>".$val['hour']."</td>";
				$res.= "\n\t\t\t\t\t\t\t"."<td>".$val['weight']."</td>";
	// 			$res.= "<td>".$val['durningScore']."</td>";
				$res.= "\n\t\t\t\t\t\t\t"."<td>".$val['midScore']."</td>";
	// 			$res.= "<td>".$val['totalDurningScore']."</td>";
				$res.= "\n\t\t\t\t\t\t\t"."<td>".$val['finalScore']."</td>";
				$res.= "\n\t\t\t\t\t\t\t"."<td>".$val['totalScore']."</td>";
				$res.= "\n\t\t\t\t\t\t\t"."<td>".$val['grade']."</td>";
				$res.= "\n\t\t\t\t\t\t\t".'<td class="beg">'.$val['beg']."</td>";
				$res.= "\n\t\t\t\t\t\t"."</tr>";
			}
		}
		return $res;
	}
?>
<html>
	<head>
		<meta charset="UTF-8">
		<title>ระบบจัดการเวลาเรียนและคะแนน - ดูตัวอย่าง</title>
		<script src="scripts/jquery-ui/external/jquery/jquery.js"></script>
 		<script src="scripts/jquery-ui/jquery-ui.js"></script>
 		<link rel="stylesheet" type="text/css" href="scripts/gradeReport.css" />
 		<link rel="stylesheet" media="print" href="scripts/gradeReport.css" />
		<script>
		$(function(){
			$( '#pdfsave').click(function(){
				window.open(window.location+'&pdf=1');
			});
		});
		</script>
	</head>
	<body>
		<div id="paper">
			<div id="header">
				<div id="logo"><img src="<?php echo $data['logo'];?>" /></div>
				<div id="schoolName"><?php echo $data['schoolName'];?></div>
				<div id="title"><?php echo $data['title'];?></div>
				<div id="termInfo"><?php echo $data['termInfo'];?></div>
				<div id="studentInfo"><?php echo $data['studentInfo'];?></div>
			</div>
			<div id="data">
				<table cellspacing="0"<?php if($_REQUEST['pdf']=='1'){echo ' border="1"';}?>>
					<thead>
						<tr>
							<th rowspan="3" class="subjectId">รหัสวิชา</th>
							<th rowspan="3" class="subjectName">รายวิชา</th>
							<th rowspan="3">ชั่วโมง</th>
							<th rowspan="3">หน่วยกิต</th>
							<th colspan="6">ผลการประเมิน</th>
						</tr>
						<tr>
							<th class="midScore">คะแนนสอบกลางภาคเรียน</th>
							<th class="finalScore">คะแนนสอบปลายภาคเรียน</th>
							<th class="totalScore">รวมคะแนนทั้งหมด</th>
							<th class="grade">ระดับผลการเรียน</th>
							<th class="beg">ผลการแก้ตัว</th>
						</tr>
						<tr>
							<th><?php echo $data['midScore'];?></th>
							<th><?php echo $data['finalScore'];?></th>
							<th><?php echo $data['midScore']+$data['finalScore'];?></th>
							<th></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="merge"></td>
							<td colspan="10" class="group lefttext">สาระการเรียนรู้พื้นฐาน</td>
						</tr>
						<!-- Begin Basic Subject Data -->
<?php 				echo genData($dataInfoBasic);?>
						<!-- End Basic Subject Data -->
						<tr>
							<td class="merge"></td>
							<td colspan="10" class="group lefttext">สาระการเรียนรู้เพิ่มเติม</td>
						</tr>
						<!-- Begin Extra Subject Data -->
<?php 				echo genData($dataInfoExtra);?>
						<!-- End Extra Subject Data -->
					</tbody>
				</table>
			</div>
			<div id="footer">
				<div id="sum">
					<table cellspacing="0">
						<thead>
							<tr>
								<th rowspan="2">สรุปผลการประเมิน</th>
								<th colspan="2">ผลการเรียนที่ได้</th>
							</tr>
							<tr>
								<th>ที่เรียน</th>
								<th>ได้</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>จำนวนหน่วยกิต/น้ำหนักวิชาสาระการเรียนรู้พื้นฐาน</td>
								<td class="cent"><?php echo $countRegBasic;?></td>
								<td class="cent"><?php echo $countBasic;?></td>
							</tr>
							<tr>
								<td>จำนวนหน่วยกิต/น้ำหนักวิชาสาระการเรียนรู้เพิ่มเติม</td>
								<td class="cent"><?php echo $countRegExtra;?></td>
								<td class="cent"><?php echo $countExtra;?></td>
							</tr>
							<tr>
								<td>รวมจำนวนหน่วยกิต/น้ำหนัก</td>
								<td class="cent"><?php echo $totalReg;?></td>
								<td class="cent"><?php echo $totalGain;?></td>
							</tr>
							<tr>
								<td>ระดับผลการเรียนเฉลี่ย (GPA)</td>
								<td colspan="2" class="cent"><?php echo $GPA;?></td>
							</tr>
							<tr>
								<td>ตำแหน่งเปร์เซ็นไทล์ (Pr)</td>
								<td colspan="2" class="cent"></td>
							</tr>
						</tbody>
					</table>
				</div>
				<div id="sign">
					<div class="signSpace">ลงชื่อ ................................................................</div>
					<div>(<?php echo $data['instructor'];?>)</div>
					<div>ครูประจำชั้น</div>
					<div class="signSpace">ลงชื่อ ................................................................</div>
					<div>(<?php echo $data['director'];?>)</div>
					<div>ผู้อำนวยการโรงเรียน</div>
<!-- 					<div class="signSpace">ลงชื่อ ................................................................</div> -->
<!-- 					<div>(<span class="signName" style="color:white;"><?php //for($i=0;$i<55;$i++){echo '.';}?></span>)</div> -->
<!-- 					<div>ผู้ปกครอง</div> -->
				</div>
			</div>
		</div>
		<div class="noprint" id="printOption">
			<button onclick="window.print();" title="พิมพ์"><img src="images/print.png"></button>
			<button id="pdfsave" title="บันทึกเป็น PDF"><img src="images/pdf-dl.png"></button>
		</div>
	</body>
</html>
<?php }?>