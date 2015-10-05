<?php
function microtime_float()
{
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}
$lowPer = $_GET['lowPer']?$_GET['lowPer']:50;
$warnPer = $_GET['warnPer']?$_GET['warnPer']:65;
$objConnect = mysql_connect("localhost","utccictc_tss","1d6QHmik");
// $objConnect = mysql_connect("localhost","root","");
if($objConnect){
	$objDB = mysql_select_db("utccictc_tss");
// 	$objDB = mysql_select_db("tss_old");
	mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'", $objConnect);
} else {
	echo "ERROR Connect To Database.";
	exit();
}
$subjectData = "";
	$strSQL = "SELECT * FROM subjects";
	$objQuery = mysql_query($strSQL);
	if(@mysql_num_rows($objQuery)>=1){
		while($row = mysql_fetch_array($objQuery)){
			$subjectData .= '<option value="'.$row['subjectID'].'">'.$row['code'].' '.$row['name'].'</option>';
			if($_GET['subjectID']){
				if($_GET['subjectID']==$row['subjectID']) $subjectInfo = $row['code'].' '.$row['name'];
			}
		}
	} else {
		$subjectData = "<option>ไม่มีวิชาเรียน</option>";
	}
	if($_GET['subjectID']&&$subjectInfo){
		$subInfo = "<br/>ข้อมูลรายวิชา : $subjectInfo<br/>";
		$report = "<hr/>";
		$startTime = microtime_float();
		$strSQL = "SELECT *  FROM atdlist atd,reg_subject rsu WHERE rsu.subjectID = '".$_GET['subjectID']."' AND rsu.regSubjectID = atd.regSubjectID ORDER BY atd.datetime;";
		$objQuery = mysql_query($strSQL);
		if(mysql_num_rows($objQuery)>=1){
			$i = 0;
			while($row = mysql_fetch_array($objQuery)){
				$atdList[$i] = date("d/m/Y",strtotime($row['datetime'])).' ('.$row['atdID'].')';
				$i++;
			}
		} else {
			$atdList[0] = 'ไม่พบข้อมูล';
		}
		mysql_free_result($objQuery);
		$strSQL = "SELECT stu.studentID AS studentID,stu.firstname AS firstName,stu.lastname AS lastName  FROM students stu,reg_subject rsu, reg_student rst WHERE rsu.subjectID = '".$_GET['subjectID']."' AND rsu.regSubjectID = rst.regSubjectID AND rst.studentID = stu.studentID;";
		$objQuery = mysql_query($strSQL);
		if(mysql_num_rows($objQuery)>=1){
			$report.='<table border="1" cellspacing="0">';
			$report.='<thead><tr><th>รหัสนร.</th><th>ชื่อ - สกุล</th>';
			foreach($atdList as $key => $value) {
				$report.= "<th><div class=\"ah\"><span>$value</span><div></th>";
			}
			$report.='<th>มา</th><th>สาย</th><th>ขาด</th><th>%</th></tr></thead>';
			while($row = mysql_fetch_array($objQuery)){
				$studentID = $row['studentID'];
				$intime=0;
				$late=0;
				$reportr='';
				$rowClass='';
				$strSQLa = "SELECT *  FROM atdlist atd,reg_subject rsu WHERE rsu.subjectID = '".$_GET['subjectID']."' AND rsu.regSubjectID = atd.regSubjectID ORDER BY atd.datetime;";
				$objQuerya = mysql_query($strSQLa);
				if(mysql_num_rows($objQuerya)>=1){
					while($rowa = mysql_fetch_array($objQuerya)){
						$atdID = $rowa['atdID'];
						$strSQLi = "SELECT status  FROM atdinfo WHERE studentID = '".$studentID."' AND atdID = '".$atdID."';";
						$objQueryi = mysql_query($strSQLi);
						$numbRowInfo = mysql_num_rows($objQueryi);
						if($numbRowInfo==1){
							while($rowi = mysql_fetch_array($objQueryi)){
								$status = $rowi['status']=='1'?'<img class="icon" src="images/correct.png">':'<img class="icon" src="images/warning.png">';
								if($rowi['status'] == '1') $intime++;
								if($rowi['status'] == '2') $late++;
								$reportr.='<td>'.$status.'</td>';
							}
						} elseif($numbRowInfo==0) {
							$reportr.='<td><img class="icon" src="images/incorrect.png"></td>';
						} else {
							$reportr.='<td>--</td>';
						}
					}
				} else {
					$reportr.='<td colspan="'.sizeof($atdList).'">ERROR</td>';
				}
				$abs = sizeof($atdList)-($intime+$late);
				$per = round(($intime+$late)/sizeof($atdList)*100,2);
				if($per<=$warnPer&&$per>$lowPer){
					$per = '<span class="warnPer">'.$per.'</span>';
					$rowClass = 'warn';
				} elseif($per<=$lowPer){
					$per = '<span class="lowPer">'.$per.'</span>';
					$rowClass = 'low';
				} else {
					$rowClass = 'pass';
				}
				$report.='<tr class="'.$rowClass.'"><td>'.$row['studentID'].'</td><td>'.$row['firstName'].'&nbsp;&nbsp;&nbsp;'.$row['lastName'].'</td>'.$reportr;
				$report.="<td>$intime</td><td>$late</td><td>$abs</td><td>$per%</td>";
				$report.='</tr>';
			}
			$report.='</table>';
		} else {
			$report.="ไม่พบข้อมูล";
		}
		mysql_free_result($objQuery);
		mysql_free_result($objQuerya);
		mysql_free_result($objQueryi);
		$stopTime = microtime_float();
		$usedTime = $stopTime-$startTime;
		$report.='<div class="statusHolder"><div class="status low">ไม่มีสิทธิ์สอบ</div><div class="status warn">สุ่มเสี่ยง</div><div class="status pass">ผ่าน</div></div>';
		$report.='<hr/><span class="noPrint">ใช้เวลาประมวลผลทั้งหมด '.$usedTime.' วินาที</span>';
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>ระบบจัดการเวลาเรียนและคะแนน</title>
		<style>
			.lowPer {
				color: red;
				font-weight: bold;
			}
			.warnPer {
				color: chocolate;
			}
			.low { background-color: rgba(255,0,0,0.3);}
			.warn { background-color: rgba(255,255,0,0.3);}
			.pass { background-color: rgba(0,255,0,0.3);}
			input[type=number] {
				width: 30px;
			}
			img.icon {
				width: 15px;
				height: 15px;
			}
			div.ah>span {
				writing-mode: lr-tb;
				-webkit-transform: rotate(-90deg);
				transform: rotate(-90deg);
				display:block;
				width: 150px;
				height: 15px;
				position: relative;
				left: -68px;
			}
			div.ah {
				position: relative;
				width: 15px;
			}
			th {
				height: 150px;
			}
			.statusHolder {
				display: block;
				margin: auto;
				padding: 15px;
				width: 450px;
			}
			.status {
				width: 150px;
				text-align: center;
				border: 1px solid black;
				padding: 5px;
				margin: 15px;
				display: table-cell;
			}
			@media print {
				.noPrint {
					display: none;
				}
			}
		</style>
	</head>
	<body>
		<div class="noPrint">
		<form method="GET">
			<select name="subjectID">
				<?php echo $subjectData;?>
			</select><br/>
			หมดสิทธ์สอบเมื่อเข้าเรียนต่ำกว่า : <input type="number" value="50" step="5" name="lowPer">%<br/>
			แจ้งเตือนเมื่อเข้าเรียนต่ำกว่า : <input type="number" value="65" step="5" name="warnPer">%<br/>
			<input type="submit" value="ดูข้อมูล">
		</form>
		</div>
		<?php echo $subInfo.$report;?>
	</body>
</html>