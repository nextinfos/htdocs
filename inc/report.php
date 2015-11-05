<?php
	$type = $_GET['type']; 
	function getPercentile($array){
		if(isset($array)&&sizeof($array)>0){
			arsort($array);
			$i=0;
			$total = count($array);
			$percentiles = array();
			$previousValue = -1;
			$previousPercentile = -1;
			foreach ($array as $key => $value) {
				if ($previousValue == $value) {
					$percentile = $previousPercentile;
				} else {
					$percentile = 100 - $i*100/$total;
					$previousPercentile = $percentile;
				}
				$percentiles[$key] = $percentile;
				$previousValue = $value;
				$i++;
			}
			return $percentiles;
		} else {
			return false;
		}
	}
?>
<style>
	div.tablelHolder {
		margin: auto;
		width: 300px;
		font-size: 1em;
		text-align: center;
	}
	#scoreInfoConf {
		display: none;
	}
	.studentListContainer {
		padding-left: 0px;
		padding-right: 0px;
/* 		display: none; */
		max-width: 960px;
		margin: auto;
	}
	#studentList_filter {
		padding-right: 20px;
	}
	#studentList_filter input[type="search"] {
		font-size: 8pt;
	}
	#studentList_info, #studentList_length {
		padding-left: 20px;
	}
	#studentList_paginate {
		padding-right: 20px;
	}
	fieldset>legend {
		text-align: center;
	}
	.dataTableShow, .statusHolder {
		background-color: white;
		color: #333;
		margin: auto;
	}
		#formHolder {
		margin: auto;
		display: table;
	}
	#formHolder>div {
		margin: auto;
/* 		display: block; */
/* 		text-align: left; */
		padding: 2px;
		text-align: center;
	}
	#subjectType>label {
		width: 114px;
		height: 33px;
	}
	#subjectType>label>span,#term>label>span {
		padding-top: 5px;
	}
	#term>label {
		width: 50px;
	}
	#year {
		width: 150px;
	}
	.spacer {
		height: 5px;
	}
	.leftCell {
		padding-left: 5px !important;
	}
</style>
<script>
  $(function() {
    var score = $( "#score" ).spinner({min:0});
    $( "#subject" ).selectmenu({
        change: function(){
        	if($( "#subject" ).val()!=0){
            	$( '#scoreInfoConf' ).show('blind', 500);
        	}
    	}
   	});
    $(function() {
        $( "input[type=submit], button" )
          .button()
          .click(function( event ) {
          });
      });
    $( "#subject" ).load("dataCenter.php", {
        action: "get",
        type: "regInsSubList"
	}, function(){
        $( "#subject" ).selectmenu("refresh");
        if($( "#subject" ).val()!=0){
        	$( '#scoreInfoConf' ).show('blind', 500);
    	} else {
        	$( '#subject' ).selectmenu('disable');
    	}
    });
    studentListVar = $('#studentList').DataTable( {
		paging: false,
	    scrollY: 370,
	    "order": [0,'asc'],
//		    "orderFixed": [2,'desc'],
	    "columns": [{ data: "studentID","orderable": true },{ data: "firstName","orderable": true },{ data: "lastName","orderable": true },{ data: "gradeYear","orderable": false}],
	    ajax:  {
            url: "dataCenter.php",
            type: 'POST',
            data: function ( d ) {
                d.action = "get";
                d.type = "stuCanReg";
                d.term = $( "input[type='radio']:checked","#term" ).val();
                d.year = $( "#year" ).val();
                d.subjectID = $( "#subject" ).val();
            }
        }
	});
    $('fieldset').addClass("ui-corner-all");
	$('.studentListContainer').addClass("fg-toolbar ui-toolbar ui-widget-header ui-helper-clearfix");
  });
</script>
<?php if($type=='scoreOld'){?>
	<div class="tablelHolder">
		<select name="subjectID" style="width:300px;" id="subject">
			<option value="0">กำลังประมวลผล</option>
		</select>
	</div><br>
	<div id="scoreInfoConf">
		<div class="tablelHolder">
			แจ้งเตือนเมื่อคะแนนต่ำกว่า : <input type="number" style="width:50px;" value="50" step="5" name="warnPer" id="score">%
		</div><br/>
		<div class="tablelHolder">
			<input type="submit" value="ดูข้อมูล">
		</div><br>
	</div>
	<div id="scoreEdit">
		<div>
			<fieldset class="studentListContainer">
	  			<legend style="margin-left:12px;">รายชื่อนักเรียนที่ลงทะเบียน</legend>
	  			<div>
		 			<table id="studentList" class="display" style="width:100%">
		 				<thead>
							<tr class="ui-state-default">
								<th style="width: 100px;">รหัสนักเรียน</th>
								<th>ชื่อ</th>
								<th>นามสกุล</th>
								<th style="width: 120px;">ระดับชั้น</th>
							</tr>
		 				</thead>
		 			</table>
	 			</div>
			</fieldset>
		</div>
	</div>
<?php } elseif($type=="score"){
	function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
	$lowPer = $_GET['lowPer']?$_GET['lowPer']:50;
	$warnPer = $_GET['warnPer']?$_GET['warnPer']:65;
	$term = $_GET['term'];
	if(!$term) $term = getTerm();
	$year = $_GET['year'];
	if(!$year) $year = getYear();
	$subjectData = "";
	$strSQL = "SELECT sub.subjectID AS subjectID, sub.name AS name, reg.term AS term, reg.year AS year FROM `subject` sub, `register-subject` regsub, `registerinfo` reg WHERE regsub.subjectID = sub.subjectID AND regsub.instructorID = '$confUserID' AND regsub.registerID = reg.registerID AND reg.term='$term' AND reg.year = '$year';";
	$objQuery = mysql_query($strSQL);
	if(@mysql_num_rows($objQuery)>=1){
		while($row = mysql_fetch_array($objQuery)){
			$subjectData .= '<option value="'.$row['subjectID'].'"';
			if($_GET['subjectID']){
				if($_GET['subjectID']==$row['subjectID']){
					$subjectInfo = $row['subjectID'].' '.$row['name'].' เทอม '.$row['term'].' ปีการศึกษา '.($row['year']+543);
					$subjectData.=' selected';
				}
			}
			$subjectData.='>'.$row['subjectID'].' '.$row['name'].'</option>';
		}
	} else {
		$subjectData = "<option>ไม่มีวิชาเรียน</option>";
	}
	if($_GET['subjectID']&&$subjectInfo){
		$subInfo = "<div class='subInfo'>ข้อมูลรายวิชา : $subjectInfo</div>";
		$report = "<hr/>";
		$startTime = microtime_float();
		$strSQL = "SELECT *  FROM `scoreinfo` atd, `register-subject` regsub, `registerinfo` reg WHERE regsub.subjectID = '".$_GET['subjectID']."' AND regsub.subjectID = atd.subjectID AND regsub.registerID = atd.registerID AND atd.registerID = reg.registerID AND reg.term='$term' AND reg.year = '$year' ORDER BY atd.date;";
		$objQuery = mysql_query($strSQL);
		if(mysql_num_rows($objQuery)>=1){
			$i = 0;
			while($row = mysql_fetch_array($objQuery)){
				$scoreList[$i] = date("d/m/Y",strtotime($row['date'])).' ('.$row['scoreID'].')';
				$scoreMax[$i] = $row['maxScore'];
				$sumMaxScore+=$row['maxScore'];
				$i++;
			}
		} else {
			$sumMaxScore = 0;
			$scoreMax[0] = '--';
			$score = 0;
			$scoreList[0] = 'ไม่พบข้อมูล';
		}
		mysql_free_result($objQuery);
		$strSQL = sprintf(
			"
			SELECT
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
			mysql_real_escape_string($_GET['subjectID']),
			mysql_real_escape_string($term),
			mysql_real_escape_string($year)
		);
		$objQuery = mysql_query($strSQL);
		if(mysql_num_rows($objQuery)>=1){
			$report.='<table border="1" cellspacing="0" class="dataTableShow">'."\n";
			$report.='<thead><tr><th>รหัสนร.</th><th>ชื่อ - สกุล</th>'."\n";
			foreach($scoreList as $key => $value) {
				$report.= "<th><div class=\"ah\"><span>$value</span><div></th>\n";
			}
			$report.='<th>รวม</th><th>เต็ม</th><th>%</th><th>Per</th></tr>';
			$report.='<tr><th colspan="2" class="maxScore">คะแนนเต็ม</th>';
			foreach($scoreMax as $key => $value) {
				$report.= "<th class=\"maxScore\">$value</th>\n";
			}
			$report.='<th colspan="4" class="maxScore"></th></tr>';
			$report.='</thead>'."\n";
			while($row = mysql_fetch_array($objQuery)){
				$studentID = $row['studentID'];
				$intime=0;
				$late=0;
				$reportr='';
				$rowClass='';
				// 				$strSQLa = "SELECT *  FROM `scoreinfo` atd, `register-subject` regsub WHERE regsub.subjectID = '".$_GET['subjectID']."' AND regsub.registerID = atd.registerID ORDER BY atd.date;";
				$strSQLa = sprintf(
					"
					SELECT
						*
					FROM
						`scoreinfo` sco,
						`register-subject` regsub,
						`registerinfo` reg
					WHERE
						regsub.subjectID = '%s' AND
						regsub.subjectID = sco.subjectID AND
						regsub.registerID = sco.registerID AND
						sco.registerID = reg.registerID AND
						reg.term='%s' AND
						reg.year = '%s'
					ORDER BY
						sco.date ASC;
				",
					mysql_real_escape_string($_GET['subjectID']),
					mysql_real_escape_string($term),
					mysql_real_escape_string($year)
				);
				$objQuerya = mysql_query($strSQLa);
				$score = 0;
				$sumScore = 0;
				if(mysql_num_rows($objQuerya)>=1){
					while($rowa = mysql_fetch_array($objQuerya)){
						$atdID = $rowa['scoreID'];
						$strSQLi = "SELECT score  FROM `studentscore` stusco WHERE stusco.studentID = '".$studentID."' AND stusco.scoreID = '".$atdID."';";
						$objQueryi = mysql_query($strSQLi);
						$numbRowInfo = mysql_num_rows($objQueryi);
						if($numbRowInfo==1){
							while($rowi = mysql_fetch_array($objQueryi)){
								$score = $rowi['score'];
								$sumScore+=$score;
								$reportr.='<td>'.$score.'</td>';
							}
						} elseif($numbRowInfo==0) {
							$reportr.='<td><img class="icon" src="images/incorrect.png"></td>'."\n";
						} else {
							$reportr.='<td>--</td>'."\n";
						}
						mysql_free_result($objQueryi);
					}
				} else {
					$reportr.='<td colspan="'.sizeof($scoreList).'">ERROR</td>'."\n";
				}
				mysql_free_result($objQuerya);
				$per = @round($sumScore/$sumMaxScore*100,2);
				$scoreArray[$studentID] = $sumScore;
				if($per<$warnPer&&$per>=$lowPer){
					$per = '<span class="warnPer">'.$per.'</span>';
					$rowClass = 'warn';
				} elseif($per<$lowPer){
					$per = '<span class="lowPer">'.$per.'</span>';
					$rowClass = 'low';
				} else {
					$rowClass = 'pass';
				}
				$report.='<tr class="'.$rowClass.'"><td>'.$row['studentID'].'</td><td>'.$row['firstName'].'&nbsp;&nbsp;&nbsp;'.$row['lastName'].'</td>'."\n".$reportr;
				$report.="<td>$sumScore</td><td>$sumMaxScore</td><td id='$studentID'>$per%</td>\n";
				$report.='</tr>'."\n";
			}
			$report.='</table>'."\n";
		} else {
			$report.="ไม่พบข้อมูล";
		}
		mysql_free_result($objQuery);
		$stopTime = microtime_float();
		$usedTime = $stopTime-$startTime;
		$report.='<div class="statusHolder"><div class="status low">ไม่ผ่าน</div><div class="status warn">สุ่มเสี่ยง</div><div class="status pass">ผ่าน</div></div>';
		$report.='<hr/><span class="noPrint">ใช้เวลาประมวลผลทั้งหมด '.$usedTime.' วินาที</span>';
	}
	?>
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
				input.spinnerBox {
					width: 35px;
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
				.ah>span {
					font-weight: normal;
				}
				#subjectID {
					width: 320px;
				}
				.subInfo {
					text-align: center;
				}
				th.maxScore {
					height: 25px;
					font-weight: normal;
				}
				@media print {
					.noPrint {
						display: none;
					}
				}
			</style>
			<script>
				$(function(){
					$( '#term' ).buttonset();
					$( "#year" ).selectmenu();
					$( "#subjectID" ).selectmenu();
					$( ".spinnerBox" ).spinner();
					console.log('<?php echo json_encode(getPercentile($scoreArray));?>');
					var data = $.parseJSON('<?php echo json_encode(getPercentile($scoreArray));?>');
					$.each(data, function( index, value ) {
						 	$( "<td>"+value+"</td>" ).insertAfter( "td[id="+index+"]" );
						});
				});
			</script>
		<div class="noPrint">
			<form method="GET">
				<input type="hidden" name="action" value="report" />
				<input type="hidden" name="type" value="score" />
				<div id="formHolder">
					<div class="leftCell">ภาคการศึกษา : </div>
					<div id="term">
						<input type="radio" name="term" id="t1" value="1"<?php echo $term==1?' checked':radioTerm(1);?>><label for="t1">1</label>
						<input type="radio" name="term" id="t2" value="2"<?php echo $term==2?' checked':radioTerm(2);?>><label for="t2">2</label>
						<input type="radio" name="term" id="t3" value="3"<?php echo $term==3?' checked':radioTerm(3);?>><label for="t3">3</label>
					</div>
					<div class="spacer"></div>
					<div class="leftCell">ปีการศึกษา : </div>
					<div>
						<select name="year" id="year">
							<?php $year = date("Y"); for($i=$year;$i<=($year+3);$i++){ echo '<option value="'.$i.'"';if($year==$i) echo ' selected';echo '>'.($i+543).'</option>';}?>
						</select>
					</div>
					<div class="spacer"></div>
					<div class="leftCell">วิชา : </div>
					<div>
						<select name="subjectID" id="subjectID">
							<?php echo $subjectData;?>
						</select>
					</div>
					<div class="spacer"></div>
					<div>
						หมดสิทธ์สอบเมื่อคะแนนต่ำกว่า : <input type="text" class="spinnerBox" value="<?php echo $_GET['lowPer']?$_GET['lowPer']:'50';?>" step="5" name="lowPer" min="0" max="100">%<br/>
					</div>
					<div class="spacer"></div>
					<div>
						แจ้งเตือนเมื่อคะแนนต่ำกว่า : <input type="text" class="spinnerBox" value="<?php echo $_GET['warnPer']?$_GET['warnPer']:'65';?>" step="5" name="warnPer" min="0" max="100">%<br/>
					</div>
					<div class="spacer"></div>
					<div>
						<input type="submit" value="ดูข้อมูล">
					</div>
				</div>
			</form>
		</div>
		<?php echo $subInfo.$report;
} elseif($type=="attendance"){
	function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
	$lowPer = $_GET['lowPer']?$_GET['lowPer']:50;
	$warnPer = $_GET['warnPer']?$_GET['warnPer']:65;
	$term = $_GET['term'];
	if(!$term) $term = getTerm();
	$year = $_GET['year'];
	if(!$year) $year = getYear();
	$subjectData = "";
	$strSQL = "SELECT sub.subjectID AS subjectID, sub.name AS name, reg.term AS term, reg.year AS year FROM `subject` sub, `register-subject` regsub, `registerinfo` reg WHERE regsub.subjectID = sub.subjectID AND regsub.instructorID = '$confUserID' AND regsub.registerID = reg.registerID AND reg.term='$term' AND reg.year = '$year';";
	$objQuery = mysql_query($strSQL);
	if(@mysql_num_rows($objQuery)>=1){
		while($row = mysql_fetch_array($objQuery)){
			$subjectData .= '<option value="'.$row['subjectID'].'"';
			if($_GET['subjectID']){
				if($_GET['subjectID']==$row['subjectID']){
					$subjectInfo = $row['subjectID'].' '.$row['name'].' เทอม '.$row['term'].' ปีการศึกษา '.($row['year']+543);
					$subjectData.=' selected';
				}
			}
			$subjectData.='>'.$row['subjectID'].' '.$row['name'].'</option>';
		}
	} else {
		$subjectData = "<option>ไม่มีวิชาเรียน</option>";
	}
	if($_GET['subjectID']&&$subjectInfo){
		$subInfo = "<div class='subInfo'>ข้อมูลรายวิชา : $subjectInfo</div>";
		$report = "<hr/>";
		$startTime = microtime_float();
		$strSQL = "SELECT *  FROM `attendanceinfo` atd, `register-subject` regsub, `registerinfo` reg WHERE regsub.subjectID = '".$_GET['subjectID']."' AND regsub.subjectID = atd.subjectID AND regsub.registerID = atd.registerID AND atd.registerID = reg.registerID AND reg.term='$term' AND reg.year = '$year' ORDER BY atd.date;";
		$objQuery = mysql_query($strSQL);
		if(mysql_num_rows($objQuery)>=1){
			$i = 0;
			while($row = mysql_fetch_array($objQuery)){
				$atdList[$i] = date("d/m/Y",strtotime($row['date'])).' ('.$row['attendanceID'].')';
				$i++;
			}
			$countAtdList = $i;
		} else {
			$countAtdList = 0;
			$intime = 0;
			$late = 0;
			$atdList[0] = 'ไม่พบข้อมูล';
		}
		mysql_free_result($objQuery);
// 		$strSQL = "SELECT stu.studentID AS studentID,stu.firstname AS firstName,stu.lastname AS lastName  FROM `student` stu, `register-subject` regsub WHERE regsub.subjectID = '".$_GET['subjectID']."' AND stu.studentID IN (SELECT regstu.studentID FROM `register-student` regstu WHERE regstu.subjectID = regsub.subjectID AND regstu.registerID = regsub.registerID);";
		$strSQL = sprintf(
				"
			SELECT
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
				mysql_real_escape_string($_GET['subjectID']),
				mysql_real_escape_string($term),
				mysql_real_escape_string($year)
		);
		$objQuery = mysql_query($strSQL);
		if(mysql_num_rows($objQuery)>=1){
			$report.='<table border="1" cellspacing="0" class="dataTableShow">'."\n";
			$report.='<thead><tr><th>รหัสนร.</th><th>ชื่อ - สกุล</th>'."\n";
			foreach($atdList as $key => $value) {
				$report.= "<th><div class=\"ah\"><span>$value</span><div></th>\n";
			}
			$report.='<th>มา</th><th>สาย</th><th>ขาด</th><th>%</th></tr></thead>'."\n";
			while($row = mysql_fetch_array($objQuery)){
				$studentID = $row['studentID'];
				$intime=0;
				$late=0;
				$reportr='';
				$rowClass='';
// 				$strSQLa = "SELECT *  FROM `attendanceinfo` atd, `register-subject` regsub WHERE regsub.subjectID = '".$_GET['subjectID']."' AND regsub.registerID = atd.registerID ORDER BY atd.date;";
				$strSQLa = "SELECT *  FROM `attendanceinfo` atd, `register-subject` regsub, `registerinfo` reg WHERE regsub.subjectID = '".$_GET['subjectID']."' AND regsub.subjectID = atd.subjectID AND regsub.registerID = atd.registerID AND atd.registerID = reg.registerID AND reg.term='$term' AND reg.year = '$year' ORDER BY atd.date;";
				$objQuerya = mysql_query($strSQLa);
				if(mysql_num_rows($objQuerya)>=1){
					while($rowa = mysql_fetch_array($objQuerya)){
						$atdID = $rowa['attendanceID'];
						$strSQLi = "SELECT status  FROM `studentattendance` stuatd WHERE stuatd.studentID = '".$studentID."' AND stuatd.attendanceID = '".$atdID."';";
						$objQueryi = mysql_query($strSQLi);
						$numbRowInfo = mysql_num_rows($objQueryi);
						if($numbRowInfo==1){
							while($rowi = mysql_fetch_array($objQueryi)){
								$status = $rowi['status']=='ONTIME'?'<img class="icon" src="images/correct.png">':'<img class="icon" src="images/stopwatch.png">';
								if($rowi['status'] == 'ONTIME') $intime++;
								if($rowi['status'] == 'LATE') $late++;
								$reportr.='<td>'.$status.'</td>';
							}
						} elseif($numbRowInfo==0) {
							$reportr.='<td><img class="icon" src="images/incorrect.png"></td>'."\n";
						} else {
							$reportr.='<td>--</td>'."\n";
						}
						mysql_free_result($objQueryi);
					}
				} else {
					$reportr.='<td colspan="'.sizeof($atdList).'">ERROR</td>'."\n";
				}
				mysql_free_result($objQuerya);
				$abs = $countAtdList-($intime+$late);
				$per = @round(($intime+$late)/$countAtdList*100,2);
				if($per<=$warnPer&&$per>$lowPer){
					$per = '<span class="warnPer">'.$per.'</span>';
					$rowClass = 'warn';
				} elseif($per<=$lowPer){
					$per = '<span class="lowPer">'.$per.'</span>';
					$rowClass = 'low';
				} else {
					$rowClass = 'pass';
				}
				$report.='<tr class="'.$rowClass.'"><td>'.$row['studentID'].'</td><td>'.$row['firstName'].'&nbsp;&nbsp;&nbsp;'.$row['lastName'].'</td>'."\n".$reportr;
				$report.="<td>$intime</td><td>$late</td><td>$abs</td><td>$per%</td>\n";
				$report.='</tr>'."\n";
			}
			$report.='</table>'."\n";
		} else {
			$report.="ไม่พบข้อมูล";
		}
		mysql_free_result($objQuery);
		$stopTime = microtime_float();
		$usedTime = $stopTime-$startTime;
		$report.='<div class="statusHolder"><div class="status low">ไม่มีสิทธิ์สอบ</div><div class="status warn">สุ่มเสี่ยง</div><div class="status pass">ผ่าน</div></div>';
		$report.='<hr/><span class="noPrint">ใช้เวลาประมวลผลทั้งหมด '.$usedTime.' วินาที</span>';
	}
	?>
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
			input.spinnerBox {
				width: 35px;
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
			.ah>span {
				font-weight: normal;
			}
			#subjectID {
				width: 320px;
			}
			.subInfo {
				text-align: center;
			}
			@media print {
				.noPrint {
					display: none;
				}
			}
		</style>
		<script>
			$(function(){
				$( '#term' ).buttonset();
				$( "#year" ).selectmenu();
				$( "#subjectID" ).selectmenu();
				$( ".spinnerBox" ).spinner();
			});
		</script>
	<div class="noPrint">
		<form method="GET">
			<input type="hidden" name="action" value="report" />
			<input type="hidden" name="type" value="attendance" />
			<div id="formHolder">
				<div class="leftCell">ภาคการศึกษา : </div>
				<div id="term">
					<input type="radio" name="term" id="t1" value="1"<?php echo $term==1?' checked':radioTerm(1);?>><label for="t1">1</label>
					<input type="radio" name="term" id="t2" value="2"<?php echo $term==2?' checked':radioTerm(2);?>><label for="t2">2</label>
					<input type="radio" name="term" id="t3" value="3"<?php echo $term==3?' checked':radioTerm(3);?>><label for="t3">3</label>
				</div>
				<div class="spacer"></div>
				<div class="leftCell">ปีการศึกษา : </div>
				<div>
					<select name="year" id="year">
						<?php $year = date("Y"); for($i=$year;$i<=($year+3);$i++){ echo '<option value="'.$i.'"';if($year==$i) echo ' selected';echo '>'.($i+543).'</option>';}?>
					</select>
				</div>
				<div class="spacer"></div>
				<div class="leftCell">วิชา : </div>
				<div>
					<select name="subjectID" id="subjectID">
						<?php echo $subjectData;?>
					</select>
				</div>
				<div class="spacer"></div>
				<div>
					หมดสิทธ์สอบเมื่อเข้าเรียนต่ำกว่า : <input type="text" class="spinnerBox" value="<?php echo $_GET['lowPer']?$_GET['lowPer']:'50';?>" step="5" name="lowPer" min="0" max="100">%<br/>
				</div>
				<div class="spacer"></div>
				<div>
					แจ้งเตือนเมื่อเข้าเรียนต่ำกว่า : <input type="text" class="spinnerBox" value="<?php echo $_GET['warnPer']?$_GET['warnPer']:'65';?>" step="5" name="warnPer" min="0" max="100">%<br/>
				</div>
				<div class="spacer"></div>
				<div>
					<input type="submit" value="ดูข้อมูล">
				</div>
			</div>
		</form>
	</div>
		<?php echo $subInfo.$report;?>
<?php }?>