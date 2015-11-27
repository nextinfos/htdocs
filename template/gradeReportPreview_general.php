<?php
	$studentID = $_REQUEST['studentID'];
	$term = $_REQUEST['term'];
	$year = $_REQUEST['year'];
	$confDurningScore = 60;
	$confMidScore = 20;
	$confFinalScore = 20;
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
			$predata['midScore'] = rand(0,20);
			$predata['totalDurningScore'] = $predata['durningScore']+$predata['midScore'];
			$predata['finalScore'] =  rand(0,20);
			$predata['totalScore'] = $predata['totalDurningScore']+$predata['finalScore'];
			$predata['grade'] = gradeCal(NULL, array(50,55,60,65,70,75,80,100), '0', $predata['totalScore']);
			$predata['beg'] = $predata['grade']=='0'?'<input type="text" value="1" />':'';
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
		$predata['beg'] = $predata['grade']=='0'?'<input type="text" value="1" />':'';
		$dataInfoExtra[] = $predata;
	}
//	Sample Data Generator End
	function genData($data){
		$res = '';
		foreach ($data as $val){
			$res.= "<tr>";
			$res.= '<td class="lefttext">'.$val['subjectID']."</td>";
			$res.= '<td class="lefttext">'.$val['subjectName']."</td>";
			$res.= "<td>".$val['hour']."</td>";
			$res.= "<td>".$val['weight']."</td>";
			$res.= "<td>".$val['durningScore']."</td>";
			$res.= "<td>".$val['midScore']."</td>";
			$res.= "<td>".$val['totalDurningScore']."</td>";
			$res.= "<td>".$val['finalScore']."</td>";
			$res.= "<td>".$val['totalScore']."</td>";
			$res.= "<td>".$val['grade']."</td>";
			$res.= '<td class="beg">'.$val['beg']."</td>";
			$res.= "</tr>";
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
 		<script src="scripts/jspdf.min.js"></script>
		<style>
			@font-face {
			    font-family: THSarabunNew;
			    src: url('fonts/THSarabunNew Bold.ttf');
			}
			body, html {
				margin: 0px;
				font-size: 1em;
			}
			#paper {
				max-width: 21.0cm;
				max-height: 29.7cm;
				min-width: 21.0cm;
				min-height: 29.7cm;
 				border: 1px solid black;
 				padding: 0.5cm 1cm;
				margin: auto;
			}
			#header {
				width: 100%;
				height: 150px;
			}
			#logo {
				width: 70px;
				height: 70px;
				margin: auto;
				text-align: center;
			}
			#logo>img {
				max-width: 70px;
				max-height: 70px;
			}
			#schoolName,#title,#termInfo,#studentInfo {
				text-align: center;
				font-weight: bold;
			}
			#data, #data>table {
				width: 100%;
			}
			table td, table th{
				border-right: 1px solid black;
				border-bottom: 1px solid black;
				padding: 4px 5px;
			}
			table {
				border-top: 1px solid black;
				border-left: 1px solid black;
				font-size: 0.8em;
			}
			th {
				font-weight: normal;
			}
			.group {
				text-weight: bold;
			}
			td.merge {
				border-right: none;
			}
			#data>table>tbody>tr>td {
				text-align: center;
			}
			td.lefttext {
				text-align: left !important;
			}
			.subjectID{ width: 50px; }
			.subjectName { width: 180px; }
			.durningScore { width: 65px; }
			.midScore, .totalDurningScore { width: 76px; }
			.finalScore { width: 55px; }
			.grade { width: 35px; }
			.beg {
				word-break: break-word;
    			width: 20px;
    		}
    		.beg>input[type=text] {
    			width: 100%;
    			margin: 0px;
    			padding: 0px;
    			text-align: center;
    		}
    		td.beg {
				padding: 4px 1px;
			}
    		#footer {
    			width: 100%;
    			vertical-align: middle;
    		}
    		#footer>div {
    			display: inline-block;
    			width: 49%;
    			margin: auto;
    		}
     		#sum {
				padding-top: 20px;
				vertical-align: top;
     		}
    		#sum>table {
    			width: 100%;
    		}
    		#sign {
    			font-size: 0.8em;
    			text-align: center;
    		}
    		.signSpace {
    			padding-top: 30px;
    		}
    		#sign>div {
    			text-align: center;
    			width: 100%;
    		}
    		#printOption {
    			width: 21.0cm;
    			text-align: center;
    			padding: 20px;
    			margin: auto;
    		}
    		@page {
				size: A4;
				margin: 0.5cm 1cm;
			}
			@media print {
				.noprint {
					display: none;
				}
				body {
					font-size: 0.8em;
				}
				table {
					font-size: 0.6em;
				}
				#paper {
					border: none;
					padding: 0cm 0cm;
				}
				.beg>input[type=text] {
					border: none;
					font-size: 0.6em;
					width: 100%;
				}
				td.beg {
					padding: 4px 1px;
				}
			}
		</style>
		<script>
		$(function(){
			$( '#pdfsave').click(function(){
				var doc = new jsPDF('p', 'pt', 'A4'),source = $('table')[0];
				doc.fromHTML(
					source
				);
				doc.save('<?php echo $studentID;?>.pdf');
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
				<table cellspacing="0">
					<thead>
						<tr>
							<th rowspan="4" class="subjectId">รหัสวิชา</th>
							<th rowspan="4" class="subjectName">รายวิชา</th>
							<th rowspan="4">ชั่วโมง</th>
							<th rowspan="4">หน่วยกิต</th>
							<th colspan="7">ผลการประเมิน</th>
						</tr>
						<tr>
							<th colspan="3">ระหว่างเรียน</th>
							<th rowspan="2" class="finalScore">คะแนนสอบปลายภาคเรียน</th>
							<th rowspan="2">รวมคะแนนทั้งหมด</th>
							<th rowspan="2" class="grade">ระดับผลการเรียน</th>
							<th rowspan="2" class="beg">ผลการแก้ตัว</th>
						</tr>
						<tr>
							<th class="durningScore">คะแนนสอบระหว่างเรียน</th>
							<th class="midScore">คะแนนสอบกลางภาคเรียน</th>
							<th class="totalDurningScore">รวมคะแนนเก็บระหว่างภาคเรียน</th>
						</tr>
						<tr>
							<th><?php echo $data['durningScore'];?></th>
							<th><?php echo $data['midScore'];?></th>
							<th><?php echo $data['durningScore']+$data['midScore'];?></th>
							<th><?php echo $data['finalScore'];?></th>
							<th><?php echo $data['durningScore']+$data['midScore']+$data['finalScore'];?></th>
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
						<?php echo genData($dataInfoBasic);?>
						<!-- End Basic Subject Data -->
						<tr>
							<td class="merge"></td>
							<td colspan="10" class="group lefttext">สาระการเรียนรู้เพิ่มเติม</td>
						</tr>
						<!-- Begin Extra Subject Data -->
						<?php echo genData($dataInfoExtra);?>
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
					<div>(<?php for($i=0;$i<55;$i++){echo '&nbsp';}?>)</div>
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