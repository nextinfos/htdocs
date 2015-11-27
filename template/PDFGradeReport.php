<?php
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
			$predata['beg'] = $predata['grade']=='0'?'1':'';
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
		$predata['beg'] = $predata['grade']=='0'?'1':'';
		$dataInfoExtra[] = $predata;
	}
//	Sample Data Generator End
	function genData($data){
		$res = '';
		foreach ($data as $val){
			$res.= "\n\t\t\t\t\t\t<tr>";
			$res.= "\n\t\t\t\t\t\t\t".'<td style="text-align: left !important;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">'.$val['subjectID']."</td>";
			$res.= "\n\t\t\t\t\t\t\t".'<td style="text-align: left !important;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">'.$val['subjectName']."</td>";
			$res.= "\n\t\t\t\t\t\t\t".'<td style="text-align:center;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">'.$val['hour']."</td>";
			$res.= "\n\t\t\t\t\t\t\t".'<td style="text-align:center;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">'.$val['weight']."</td>";
			$res.= "\n\t\t\t\t\t\t\t".'<td style="text-align:center;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">'.$val['midScore']."</td>";
			$res.= "\n\t\t\t\t\t\t\t".'<td style="text-align:center;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">'.$val['finalScore']."</td>";
			$res.= "\n\t\t\t\t\t\t\t".'<td style="text-align:center;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">'.$val['totalScore']."</td>";
			$res.= "\n\t\t\t\t\t\t\t".'<td style="text-align:center;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">'.$val['grade']."</td>";
			$res.= "\n\t\t\t\t\t\t\t".'<td style="text-align:center;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">'.$val['beg']."</td>";
			$res.= "\n\t\t\t\t\t\t"."</tr>";
		}
		return $res;
	}
	require_once('scripts/mpdf60/mpdf.php'); //ที่อยู่ของไฟล์ mpdf.php ในเครื่องเรานะครับ
	ob_start(); // ทำการเก็บค่า html นะครับ
?>
<html>
	<head>
		<meta charset="UTF-8">
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
	<body style="margin: 0px;font-size: 1em;">
		<div id="paper" style="margin-top:-50px;max-width: 21.0cm;max-height: 29.7cm;min-width: 21.0cm;min-height: 29.7cm;border: none;padding: 0cm 1cm;margin: auto;">
			<div id="header" style="width: 100%;height: 150px;">
				<div id="logo" style="width: 70px;height: 70px;margin: auto;text-align: center;"><img src="<?php echo $data['logo'];?>" style="max-width: 70px;max-height: 70px;"/></div>
				<div id="schoolName" style="font-size: 1em;text-align: center;font-weight: bold;padding:-5px;"><?php echo $data['schoolName'];?></div>
				<div id="title" style="font-size: 1em;text-align: center;font-weight: bold;padding:-5px;"><?php echo $data['title'];?></div>
				<div id="termInfo" style="font-size: 1em;text-align: center;font-weight: bold;padding:-5px;"><?php echo $data['termInfo'];?></div>
				<div id="studentInfo" style="font-size: 1em;text-align: center;font-weight: bold;padding:-5px;"><?php echo $data['studentInfo'];?></div>
			</div>
			<div id="data" style="width: 100%;">
				<table cellspacing="0" style="width: 100%;border-top: 1px solid black;border-left: 1px solid black;font-size: 0.8em;">
					<thead>
						<tr>
							<th rowspan="3" class="subjectId" style="width: 60px;font-weight: normal;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">รหัสวิชา</th>
							<th rowspan="3" class="subjectName" style="width: 180px;font-weight: normal;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">รายวิชา</th>
							<th rowspan="3" style="font-weight: normal;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">ชั่วโมง</th>
							<th rowspan="3" style="font-weight: normal;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">หน่วยกิต</th>
							<th colspan="6" style="font-weight: normal;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">ผลการประเมิน</th>
						</tr>
						<tr>
							<th class="midScore" style="width: 90px;font-weight: normal;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">คะแนนสอบกลางภาคเรียน</th>
							<th class="finalScore" style="width: 90px;font-weight: normal;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">คะแนนสอบปลายภาคเรียน</th>
							<th class="totalScore" style="width: 80px;font-weight: normal;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">รวมคะแนนทั้งหมด</th>
							<th class="grade" style="width: 35px;font-weight: normal;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">ระดับผลการเรียน</th>
							<th class="beg" style="font-weight: normal;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">ผลการแก้ตัว</th>
						</tr>
						<tr>
							<th style="font-weight: normal;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;"><?php echo $data['midScore'];?></th>
							<th style="font-weight: normal;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;"><?php echo $data['finalScore'];?></th>
							<th style="font-weight: normal;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;"><?php echo $data['midScore']+$data['finalScore'];?></th>
							<th style="font-weight: normal;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;"></th>
							<th style="font-weight: normal;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;"></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="merge" style="border-right: none;border-bottom: 1px solid black;padding: 4px 5px;"></td>
							<td colspan="10" class="group lefttext" style="text-weight: bold;text-align:left;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">สาระการเรียนรู้พื้นฐาน</td>
						</tr>
						<!-- Begin Basic Subject Data -->
<?php 				echo genData($dataInfoBasic);?>
						<!-- End Basic Subject Data -->
						<tr>
							<td class="merge" style="border-right: none;border-bottom: 1px solid black;padding: 4px 5px;"></td>
							<td colspan="10" class="group lefttext" style="text-weight: bold;text-align:left;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">สาระการเรียนรู้เพิ่มเติม</td>
						</tr>
						<!-- Begin Extra Subject Data -->
<?php 				echo genData($dataInfoExtra);?>
						<!-- End Extra Subject Data -->
					</tbody>
				</table>
			</div>
			<div id="footer" style="width: 100%;vertical-align: middle;">
				<table cellspacing="0" cellpadding="0" style="width:100%;padding-top:20px;">
					<tr>
						<td style="width: 50%;">
				<div id="sum" style="display:block;width: 100%;margin: auto;padding-top: 20px;vertical-align: top;">
					<table cellspacing="0" style="border-top: 1px solid black;border-left: 1px solid black;font-size: 0.8em;width: 100%;">
						<thead>
							<tr>
								<th rowspan="2" style="font-weight: normal;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">สรุปผลการประเมิน</th>
								<th colspan="2" style="font-weight: normal;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">ผลการเรียนที่ได้</th>
							</tr>
							<tr>
								<th style="font-weight: normal;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">ที่เรียน</th>
								<th style="font-weight: normal;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">ได้</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">จำนวนหน่วยกิต/น้ำหนักวิชาสาระการเรียนรู้พื้นฐาน</td>
								<td style="border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;"></td>
								<td style="border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;"></td>
							</tr>
							<tr>
								<td style="border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">จำนวนหน่วยกิต/น้ำหนักวิชาสาระการเรียนรู้เพิ่มเติม</td>
								<td style="border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;"></td>
								<td style="border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;"></td>
							</tr>
							<tr>
								<td style="border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">รวมจำนวนหน่วยกิต/น้ำหนัก</td>
								<td style="border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;"></td>
								<td style="border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;"></td>
							</tr>
							<tr>
								<td style="border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">ระดับผลการเรียนเฉลี่ย (GPA)</td>
								<td colspan="2" style="border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;"></td>
							</tr>
							<tr>
								<td style="border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">ตำแหน่งเปร์เซ็นไทล์ (Pr)</td>
								<td colspan="2" style="border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;"></td>
							</tr>
						</tbody>
					</table>
				</div>
						</td>
						<td style="width:10px;"></td>
						<td style="text-align: center;">
				<div id="sign" style="display:block;width: 100%;margin: auto;font-size: 0.8em;text-align: center;padding-left:10px;">
					<div class="signSpace" style="padding-top: 30px;text-align: center;width: 100%;"><br/><br/>ลงชื่อ ...............................................................</div>
					<div style="text-align: center;width: 100%;">(<?php echo $data['instructor'];?>)</div>
					<div style="text-align: center;width: 100%;">ครูประจำชั้น</div>
					<div class="signSpace" style="padding-top: 30px;text-align: center;width: 100%;"><br/><br/>ลงชื่อ ...............................................................</div>
					<div style="text-align: center;width: 100%;">(<?php echo $data['director'];?>)</div>
					<div style="text-align: center;width: 100%;">ผู้อำนวยการโรงเรียน</div>
					<div class="signSpace" style="padding-top: 30px;text-align: center;width: 100%;"><br/><br/>ลงชื่อ ...............................................................</div>
					<div style="text-align: center;width: 100%;">(<span class="signName" style="color:white;"><?php for($i=0;$i<55;$i++){echo '.';}?></span>)</div>
					<div style="text-align: center;width: 100%;">ผู้ปกครอง</div>
				</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</body>
</html>
<?php
	$html = ob_get_contents();
	ob_end_clean();
	$pdf = new mPDF('th', 'A4', '0', '','0','0','5','5'); //การตั้งค่ากระดาษถ้าต้องการแนวตั้ง ก็ A4 เฉยๆครับ ถ้าต้องการแนวนอนเท่ากับ A4-L
	$pdf->SetDisplayMode('fullpage');
	$pdf->WriteHTML($html, 2);
	$pdf->Output();
?>