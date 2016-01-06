<?php
	function genData($data){
		$res = '';
		if(sizeof($data)<1){
			$res.= "\n\t\t\t\t\t\t<tr>";
			$res.= "\n\t\t\t\t\t\t\t".'<td style="text-align: left !important;border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;"></td>';
			$res.= "\n\t\t\t\t\t\t\t".'<td style="text-align: left !important;border-bottom: 1px solid black;padding: 4px 5px;" colspan="8">--</td>';
			$res.= "\n\t\t\t\t\t\t"."</tr>";
		} else {
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
								<td style="border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;text-align: center;"><?php echo $countRegBasic;?></td>
								<td style="border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;text-align: center;"><?php echo $countBasic;?></td>
							</tr>
							<tr>
								<td style="border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">จำนวนหน่วยกิต/น้ำหนักวิชาสาระการเรียนรู้เพิ่มเติม</td>
								<td style="border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;text-align: center;"><?php echo $countRegExtra;?></td>
								<td style="border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;text-align: center;"><?php echo $countExtra;?></td>
							</tr>
							<tr>
								<td style="border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">รวมจำนวนหน่วยกิต/น้ำหนัก</td>
								<td style="border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;text-align: center;"><?php echo $totalReg;?></td>
								<td style="border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;text-align: center;"><?php echo $totalGain;?></td>
							</tr>
							<tr>
								<td style="border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">ระดับผลการเรียนเฉลี่ย (GPA)</td>
								<td colspan="2" style="border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;text-align: center;"><?php echo $GPA;?></td>
							</tr>
							<tr>
								<td style="border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;">ตำแหน่งเปร์เซ็นไทล์ (Pr)</td>
								<td colspan="2" style="border-right: 1px solid black;border-bottom: 1px solid black;padding: 4px 5px;text-align: center;"></td>
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
<!-- 					<div class="signSpace" style="padding-top: 30px;text-align: center;width: 100%;"><br/><br/>ลงชื่อ ...............................................................</div> -->
<!-- 					<div style="text-align: center;width: 100%;">(<span class="signName" style="color:white;"><?php //for($i=0;$i<55;$i++){echo '.';}?></span>)</div> -->
<!-- 					<div style="text-align: center;width: 100%;">ผู้ปกครอง</div> -->
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
	$pdf->Output("Grade Report $studentID($term-$year).pdf",'D');
?>