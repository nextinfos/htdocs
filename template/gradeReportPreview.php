<?php
	if($_REQUEST['pdf']=='1'){
		require 'PDFGradeReport.php';
		exit();
	} else {
	$studentID = $_REQUEST['studentID'];
	$term = $_REQUEST['term'];
	$year = $_REQUEST['year'];
// 	$confDurningScore = 60;
	$confMidScore = 50;
	$confFinalScore = 50;
	$strSQL = sprintf(
			"
				SELECT
					sub.subjectID,
					sub.name,
					sub.type,
					stu.firstName,
					stu.lastName,
					stu.gender,
					stu.gradeYear,
					ins.firstName AS insFirstName,
					ins.lastName AS insLastName
				FROM
					`register-subject` regsub,
					`register-student` regstu,
					`subject` sub,
					`student` stu,
					`instructor` ins
				WHERE
					stu.instructorID = ins.instructorID AND
					regstu.subjectID = regsub.subjectID AND
					regstu.subjectID = sub.subjectID AND
					regstu.studentID = '%s' AND
					regstu.studentID = stu.studentID AND
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
			$strSQL2 = sprintf(
					"
				SELECT
					SUM(stusco.score) AS durningScore,
					(
					SELECT
						SUM(maxScore)
					FROM
						`scoreinfo`
					WHERE
						subjectID = '%s' AND
						type IN ('TASK','QUIZ') AND
						registerID = (
							SELECT
								registerID
							FROM
								`registerinfo`
							WHERE
								term = '%s' AND
								year = '%s'
						)
					) AS maxScore
				FROM
					`studentscore` stusco,
					`scoreinfo` scoinfo
				WHERE
					stusco.scoreID = scoinfo.scoreID AND
					stusco.studentID = '%s' AND
					stusco.subjectID = '%s' AND
					scoinfo.type IN ('TASK','QUIZ') AND
					stusco.registerID = (
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
					mysql_real_escape_string($year),
					mysql_real_escape_string($studentID),
					mysql_real_escape_string($row['subjectID']),
					mysql_real_escape_string($term),
					mysql_real_escape_string($year)
			);
			$objQuery2 = mysql_query($strSQL2);
			if($objQuery2&&mysql_num_rows($objQuery2)){
				$row2=mysql_fetch_assoc($objQuery2);
				$durningScore = $row2['durningScore']==NULL?'--':$row2['durningScore'];
 				if($durningScore!='--'&&$row2['maxScore']>=$confDurningScore){
 					$durningScore = round($durningScore/$row2['maxScore']*$confDurningScore,0).'*';
 				}
			} else {
				$durningScore = '--';
			}
// 			$strSQL2 = sprintf(
// 					"
// 				SELECT
// 					SUM(score) AS durningScore,
// 					SUM(scoinfo.maxScore) AS maxScore
// 				FROM
// 					`studentscore` stusco INNER JOIN `scoreinfo` scoinfo ON stusco.scoreID = scoinfo.scoreID
// 				WHERE
// 					stusco.studentID = '%s' AND
// 					stusco.subjectID = '%s' AND
// 					scoinfo.type = 'EXAM' AND
// 					stusco.registerID = (
// 						SELECT
// 							registerID
// 						FROM
// 							`registerinfo`
// 						WHERE
// 							term = '%s' AND
// 							year = '%s'
// 					)
// 				",
// 					mysql_real_escape_string($studentID),
// 					mysql_real_escape_string($row['subjectID']),
// 					mysql_real_escape_string($term),
// 					mysql_real_escape_string($year)
// 			);
// 			$objQuery2 = mysql_query($strSQL2);
// 			if($objQuery2&&mysql_num_rows($objQuery2)){
// 				$row2=mysql_fetch_assoc($objQuery2);
// 				$durningScore = $row2['durningScore']==NULL?'--':$row2['durningScore'];
// 			} else {
// 				$durningScore = '--';
// 			}
			$predata = NULL;
			$predata['subjectID'] = $row['subjectID'];
			$predata['subjectName'] = $row['name'];
			$predata['hour'] = $row['type']=='BASIC'?'60':'40';
			$predata['weight'] = $row['type']=='BASIC'?'1.5':'1';
			$predata['durningScore'] = $durningScore;
			$predata['midScore'] = rand(0,50);
			$predata['totalDurningScore'] = $predata['durningScore']+$predata['midScore'];
			$predata['finalScore'] =  rand(0,50);
			$predata['totalScore'] = $predata['totalDurningScore']+$predata['finalScore'];
			$predata['grade'] = gradeCal(NULL, array(50,55,60,65,70,75,80,100), '0', $predata['totalScore']);
			if($_REQUEST['pdf']=='1'){
				$predata['beg'] = $predata['grade']=='0'?'1':'';
			} else {
				$predata['beg'] = $predata['grade']=='0'?'<input type="text" value="1" />':'';
			}
			$dataInfoBasic[] = $predata;
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
//	Sample Data Generator Begin
	for($i=0;$i<7;$i++){
		$predata['subjectID'] = 'ท99999';
		$predata['subjectName'] = 'ทดสอบรายวิชา';
		$predata['hour'] = '99';
		$predata['weight'] = '9.9';
		$predata['durningScore'] = rand(0,60);
		$predata['midScore'] = rand(0,20);
		$predata['totalDurningScore'] = $predata['durningScore']+$predata['midScore'];
		$predata['finalScore'] =  rand(0,20);
		$predata['totalScore'] = $predata['totalDurningScore']+$predata['finalScore'];
		$predata['grade'] = gradeCal(NULL, array(50,55,60,65,70,75,80,100), '0', $predata['totalScore']);
		if($_REQUEST['pdf']=='1'){
			$predata['beg'] = $predata['grade']=='0'?'1':'';
		} else {
			$predata['beg'] = $predata['grade']=='0'?'<input type="text" value="1" />':'';
		}
		$dataInfoExtra[] = $predata;
	}
//	Sample Data Generator End
	function genData($data){
		$res = '';
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
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td>จำนวนหน่วยกิต/น้ำหนักวิชาสาระการเรียนรู้เพิ่มเติม</td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td>รวมจำนวนหน่วยกิต/น้ำหนัก</td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td>ระดับผลการเรียนเฉลี่ย (GPA)</td>
								<td colspan="2"></td>
							</tr>
							<tr>
								<td>ตำแหน่งเปร์เซ็นไทล์ (Pr)</td>
								<td colspan="2"></td>
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
					<div class="signSpace">ลงชื่อ ................................................................</div>
					<div>(<span class="signName" style="color:white;"><?php for($i=0;$i<55;$i++){echo '.';}?></span>)</div>
					<div>ผู้ปกครอง</div>
				</div>
			</div>
		</div>
		<div class="noprint" id="printOption">
			<button onclick="window.print();">พิมพ์</button>
			<button id="pdfsave">บันทึกเป็น PDF</button>
		</div>
	</body>
</html>
<?php }?>