<?php
	$subID = $_REQUEST['subID'];
	$termID = $_REQUEST['termID'];
?>
<style>	
	#subject {
		width: 320px;
	}
	#scoreContainer {
		text-align: center;
	}
	.subInfoText {
		width: 120px;
		display: inline-block;
		text-align: left;
		padding-bottom: 5px;
	}
	.subInfoCollumn {
		display: inline-block;
	}
	#subInfoContainer {
		background: rgba(255,255,255,.5);
	}
	.studentListContainer {
		padding-left: 0px;
		padding-right: 0px;
	}
	#studentList_filter {
		padding-right: 20px;
	}
	#studentList_filter input[type="search"] {
		font-size: 8pt;
	}
	.label {
		color: grey;
		font-size: .8em;
	}
	.error {
		color: red;
	}
	#studentList_info, #studentList_length {
		padding-left: 20px;
	}
	#studentList_paginate {
		padding-right: 20px;
	}
</style>
<script>
	$('#pageName').text('ระบบลงคะแนน');
	$(function(){
		$('input:text, input:password').button().css({
			'font' : 'inherit',
			'color' : 'inherit',
			'text-align' : 'left',
			'outline' : 'none',
			'cursor' : 'text'
		});
		$('input:submit,button').button();
		$( "#subject" )
			.selectmenu()
			.selectmenu( "menuWidget" )
			.addClass( "overflow" );
		studentListVar = $('#studentList').DataTable( {
			paging: true,
		    scrollY: 370,
		    "order": [0,'asc'],
// 		    "orderFixed": [2,'desc'],
		    "columns": [{ data: "num","orderable":true},{ data: "id","orderable": true },{ data: "name","orderable": true }],
		    ajax:  {
	            url: "dataCenter.php",
	            type: 'POST',
	            data: {"action": "get","type":"stdList","regSubjectID":"<?php echo $subID;?>"}
	        }
		});
		$('fieldset').addClass("ui-corner-all");
		$('.studentListContainer').addClass("fg-toolbar ui-toolbar ui-widget-header ui-helper-clearfix");
		$('input[type=search]').button();
	});
	$(document).ready(function(){
		$( "#subject" ).on( "selectmenuchange",function(){
			console.log($( "#subject" ).val()+'/<?php echo $subID;?>');
			if($( "#subject" ).val()!='<?php echo $subID;?>' && $( "#subject" ).val()!='') document.location = '<?php echo explode('&',$_SERVER["REQUEST_URI"])[0];?>&subID='+$( "#subject" ).val();
		});
	});
</script>
<div id="scoreContainer">
<?php
// 	$objDB = mysql_select_db("tss_old");
	$confUserID = '2';
	$strSQL = 'SELECT instructorID FROM owner WHERE memberID = "'.$confUserID.'"';
	$objQuery = mysql_query($strSQL);
	if($objQuery&&mysql_num_rows($objQuery)==1){
		$row = mysql_fetch_array($objQuery);
		$instructorID = $row['instructorID'];
	}
	$strSQL = 'SELECT * FROM terms t ,subjects s ,reg_subject r WHERE s.subjectID = r.subjectID AND r.instructorID = "'.$instructorID.'" AND r.termID = t.termID';
	$objQuery = mysql_query($strSQL);
	if($objQuery&&mysql_num_rows($objQuery)>=1){
		$data = '<select id="subject">';
		if($subID=='') $data .= '<option value="">กรุณาเลือกวิชา</option>';
		while($row = mysql_fetch_array($objQuery)){
			if($subID==$row['subjectID']&&$termID==$row['termID']) $selectSt = ' selected'; else $selectSt = '';
			$data.= '<option value="'.$row['subjectID'].'&termID='.$row['termID'].'"'.$selectSt.'>['.$row['term'].'-'.($row['year']+543).'] '.$row['code'].' '.$row['name'].'</option>';
		}
		echo $data.'</select>';
	} else {
		echo '<select disabled id="subject"><option>ไม่พบข้อมูล</option></select>';
	}
?>
<br /><span class="label">1 = ภาคเรียนที่ ๑ , 2 = ภาคเรียนที่ ๒ , 3 = ภาคฤดูร้อน</span><br />
<?php
if($subID!=''){
	$strSQL = 'SELECT s.name as name,s.nameEN as nameEN, l.name as lname, s.code as code, s.codeEN as codeEN, s.type as type, s.hour as hour,s.levelID as level FROM levels l,terms t ,subjects s RIGHT JOIN reg_subject r ON s.subjectID = r.subjectID WHERE r.instructorID = "'.$instructorID.'" AND r.termID = t.termID AND l.levelID = s.levelID AND r.subjectID = "'.$subID.'" AND r.termID = "'.$termID.'"';
	$objQuery = mysql_query($strSQL);
	if($objQuery&&mysql_num_rows($objQuery)>=1){
		$row = mysql_fetch_array($objQuery);
// 		print_r($row);
?>
<div style="position:relative;width: 944px;height: 230px;left:40px;top:40px;">
	<fieldset id="subInfoContainer">
		<legend>รายละเอียดวิชา</legend>
		<div style="width:300px;text-align:right;" class="subInfoCollumn">
			รหัสวิชา : <span class="subInfoText"><?php echo $row['code'];?></span><br/>
			ชื่อวิชา : <span class="subInfoText"><?php echo $row['name'];?></span><br/>
		</div>
		<div style="width:300px;text-align:right;" class="subInfoCollumn">
			ประเภทวิชา : <span class="subInfoText"><?php switch ($row['type']){ case 0 : echo 'พื้นฐาน'; break; case 1 : echo 'เพิ่มเติม'; break; default: echo 'ไม่ระบุ'; }?></span><br/>
			ครูผู้สอน : <span class="subInfoText"><?php echo $row['lname']?></span><br/>
		</div>
	</fieldset>
</div>
<div>
	<button>เพิ่มการลงคะแนน</button>
</div>
	<div style="position:relative;width: 944px;left:40px;height:575px;top:20px;">
			<fieldset class="studentListContainer">
  					<legend style="margin-left:12px;">รายชื่อนักเรียนที่ลงทะเบียนเรียน</legend>
  						<div style="width:940px;height:460px;">
	 						<table id="studentList" class="display" style="width:100%">
	 							<thead>
		 							<tr class="ui-state-default">
		 								<th style="width: 20px;">No.</th>
		 								<th style="width: 100px;">เลขประจำตัว</th>
		 								<th>ชื่อ - สกุล</th>
		 							</tr>
	 							</thead>
	 						</table>
 						</div>
			</fieldset>
		</div>
<?php		
// 		$strSQL = 'SELECT * FROM reg_student r, students s WHERE r.studentID = s.studentID AND r.status = "1" AND s.status = "1" AND r.regSubjectId = "'.$subID.'"';
// 		echo '<br/>'.$strSQL.'<br/>';
// 		$objQuery = mysql_query($strSQL);
// 		if($objQuery&&mysql_num_rows($objQuery)>=1){
// 			$data = '<table>\n<thead>';
// 			while($row = mysql_fetch_array($objQuery)){
// 				$data.= $row['firstname'].' '.$row['lastname'].'<br/>';
// 			}
// 			echo $data.'';
// 		} else {
// 			echo '<span class="error">ไม่พบข้อมูล</span>';
// 		}
	} else {
		echo '<span class="error">เกิดข้อผิดพลาด กรุณาติดต่อผู้ดูแลระบบ</span>';
	}
} 
?>
</div>