<script>
	$(function(){
		$('#pageName').text('จัดการรายวิชา');
		$('input:text, input:password').button().css({
			'font' : 'inherit',
			'color' : 'inherit',
			'text-align' : 'left',
			'outline' : 'none',
			'cursor' : 'text'
		});
		$('input:submit').button();
	});
</script>
<style>
	#contentContainer {
		text-align:center;
		padding: 10px;
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
<div id="contentContainer">
<?php
	$tag = $_REQUEST['tag'];
	if($tag=='add'){
		$subjectID=$_POST['subjectID'];
		$subjectName=$_POST['subjectName'];
		$subjectType=$_POST['subjectType'];
		if($subjectID&&$subjectName&&$subjectType){
			$error=0;
			if(!preg_match('#[A-Za-zก-ฮ]+[0-9]{5}$#', $subjectID)){
				$error+=1;
				$reason .= 'กรุณากรอกรหัสวิชา โดยขึ้นต้นด้วยตัวอักษร 1 ตัว แล้วตามด้วยตัวเลข 5 หลัก เช่น "ท21101".';
			}
			if($error==0){
				$strSQL = 'INSERT INTO subject VALUES ("'.$subjectID.'","'.$subjectName.'","'.$subjectType.'")';
				$objQuery = mysql_query($strSQL);
				if($objQuery) $reason = '<h3 style="color:green;margin-bottom: 2px;">เพิ่มวิชาเรียนเรียบร้อย</h3><span>'.$subjectID.' '.$subjectName.'</span>'; else {
					$strSQL = 'SELECT * FROM `subject` WHERE `subjectID` = "'.$subjectID.'"';
					$objQuery = mysql_query($strSQL);
					if(mysql_num_rows($objQuery)>=1){
						$reason = '<span style="color:red;">ไม่สามารถทำการบันทึกได้ เนื่องจากรหัสวิชานี้มีอยู่ในระบบแล้ว.</span>';
					} else {
						$reason = '<h3 style="color:red;">ไม่สามารถทำการบันทึกได้ในตอนนี้ กรุณาติดต่อผู้ดูแลระบบ.</h3>';
					}
				}
			}
			echo $reason;
		}
?>
<script>
	$(function(){
		$( "#subjectType" ).buttonset();
	});
</script>
	<h1>เพิ่มวิชาเรียน</h1>
	<form action="?action=subjectManager&tag=add" method="POST">
	<div id="formHolder">
			<div class="leftCell">รหัสวิชา : </div>
			<div><input type="text" name="subjectID" placeholder="ท21101" required autofocus pattern="[A-Za-zก-ฮ]+[0-9]{5}$" maxlength="6" title='โปรดกรอกรหัสวิชาเป็นตัวหนังสือ 1 ตัว แล้วตามด้วยตัวเลข 5 หลัก เช่น "ท21101"' /></div>
			<div class="spacer"></div>
			<div class="leftCell">ชื่อวิชา : </div>
			<div><input type="text" name="subjectName" placeholder="ภาษาไทย 1" required /></div>
			<div class="spacer"></div>
			<div class="leftCell">ประเภท :</div> 
			<div>
				<div id="subjectType"><input type="radio" name="subjectType" id="BASIC" value="BASIC" checked /><label for="BASIC">พื้นฐาน</label><input type="radio" name="subjectType" id="EXTRA" value="EXTRA" /><label for="EXTRA">เพิ่มเติม</label></div>
			</div>
	</div>
	<div style="margin: auto;"><input type="submit" value="บันทึก" /></div>
	</form>
<?php
	} elseif($tag=='edit'){
?>
<script>
$(function(){
	$('.subjectListContainer').addClass("fg-toolbar ui-toolbar ui-widget-header ui-helper-clearfix");
	studentListVar = $('#subjectList').DataTable( {
		paging: false,
	    scrollY: 370,
	    "order": [1,'asc'],
//		    "orderFixed": [2,'desc'],
	    "columns": [{ data: "ch","orderable": false},{ data: "subjectID","orderable": true },{ data: "subjectName","orderable": true },{ data: "subjectType","orderable": false},{ data: "edit","orderable": false},{ data: "delete","orderable": false}],
	    ajax:  {
            url: "dataCenter.php",
            type: 'POST',
            data: {"action": "get","type":"subList"}
        }
	});
	$('fieldset').addClass("ui-corner-all");
	$('.subjectListContainer').addClass("fg-toolbar ui-toolbar ui-widget-header ui-helper-clearfix");
	$('input[type=search]').button();
});
</script>
<style>
	.subjectListContainer {
		padding-left: 0px;
		padding-right: 0px;
	}
	#subjectList_filter {
		padding-right: 20px;
	}
	#subjectList_filter input[type="search"] {
		font-size: 8pt;
	}
	#ssubjectList_info, #subjectList_length {
		padding-left: 20px;
	}
	#subjectList_paginate {
		padding-right: 20px;
	}
</style>
	<div style="position:relative;width: 944px;left:40px;height:575px;top:20px;">
			<fieldset class="subjectListContainer">
  					<legend style="margin-left:12px;">รายชื่อวิชาในระบบ</legend>
  						<div style="width:940px;height:460px;">
	 						<table id="subjectList" class="display" style="width:100%">
	 							<thead>
		 							<tr class="ui-state-default">
		 								<th style="width: 20px;"></th>
		 								<th style="width: 100px;">รหัสวิชา</th>
		 								<th>ชื่อวิชา</th>
		 								<th style="width: 100px;">ประเภท</th>
		 								<th style="width: 20px;">แก้ไข</th>
		 								<th style="width: 20px;">ลบ</th>
		 							</tr>
	 							</thead>
	 						</table>
 						</div>
			</fieldset>
		</div>
<?php
	} elseif($tag=='subjectRegistrar'){
		$term = $_POST['term'];
		$year = $_POST['year'];
		$subjectID = $_POST['subject'];
		$instructorID = $_POST['instructor'];
		$gradeYear = $_POST['gradeYear'];
		if($term&&$year&&$subjectID&&$instructorID&&$gradeYear){
			$strSQL = 'SELECT registerID FROM `registerinfo` WHERE term = "'.$term.'" AND year = "'.$year.'";';
			$objQuery = mysql_query($strSQL);
			$numRow = mysql_num_rows($objQuery);
			$error = false;
			if($numRow==1){
				$row = mysql_fetch_array($objQuery);
				$registerID = $row['registerID'];
			} elseif($numRow==0){
				$strSQL = 'INSERT INTO `registerinfo` (term,year) VALUES ("'.$term.'","'.$year.'");';
				$objQuery = mysql_query($strSQL);
				if($objQuery)
					 $registerID = mysql_insert_id();
				else {
					$error = true;
					$result = 'ข้อผิดพลาด : ไม่สามารถเพิ่มข้อมูลภาคการศึกษาได้ โปรดติดต่อผู้พัฒนา';
				}
			} else {
				$error = true;
				$result = 'ข้อผิดพลาด : ข้อมูลภาคการศึกษาผิดปกติ โปรดติดต่อผู้พัฒนา';
			}
			if(!$error){
				$strSQL = 'SELECT * FROM `register-subject` WHERE subjectID = "'.$subjectID.'" AND registerID = "'.$registerID.'" AND instructorID = "'.$instructorID.'" AND gradeYear = "'.$gradeYear.'";';
				$objQuery = mysql_query($strSQL);
				if(mysql_num_rows($objQuery)==0){
					$strSQL = 'INSERT INTO `register-subject` (subjectID,registerID,gradeYear,instructorID) VALUES ("'.$subjectID.'","'.$registerID.'","'.$gradeYear.'","'.$instructorID.'");';
					$objQuery = mysql_query($strSQL);
					if($objQuery){
						$result = 'เปิดวิชารหัส '.$subjectID.' ในภาคการศึกษาที่ '.$term.' ปีการศึกษา '.($year+543).' สำหรับชั้น '.getGradeYearName($gradeYear).' เรียบร้อย';
					} else {
						$error = true;
						$result = 'ข้อผิดพลาด : ไม่สามารถเพิ่มข้อมูลการเปิดรายวิชาได้ โปรดติดต่อผู้พัฒนา ';
					}
				} else {
					$error = true;
					$result = 'ข้อผิดพลาด : มีการเปิดวิชารหัส '.$registerID.' ในภาคการศึกษาที่ '.$term.' ปีการศึกษา '.($year+543).' สำหรับชั้น '.getGradeYearName($gradeYear).' อยู่แล้ว';
				}
			}
		} elseif($_POST['submit']){
			$error = true;
			$result = "กรุณากรอกข้อมูลให้ครบทุกช่อง";
		}
?>
<style>
	  .custom-combobox-input {
	    margin: 0;
	    padding: 4.5px 5px;
	    width: 260px;
	  }
	  .ui-selectmenu-button {
		height: 30px;
	  }
	  .error {
	  	color: #900;
	  	text-decoration: underline;
	  	font-weight: bold;
	  }
	  .pass {
	  	color: #090;
	  	text-decoration: underline;
	  	font-weight: bold;
	  }
  </style>
<script>
	$(function(){
		$( "#term" ).buttonset();
		$( "#year" ).selectmenu();
		$( "#subject" ).combobox();
		$( "#instructor" ).combobox();
		$( "#gradeYear" ).combobox();
	});
</script>
<h1>เปิดวิชาเรียน</h1>
<?php if($result) if($error) echo '<span class="error">'.$result.'</span>'; else echo '<span class="pass">'.$result.'</span>';?>
	<form method="POST">
	<div id="formHolder">
		<div class="leftCell">ภาคการศึกษา : </div>
		<div id="term"><input type="radio" name="term" id="t1" value="1"/><label for="t1">1</label><input type="radio" name="term" id="t2" value="2"/><label for="t2">2</label><input type="radio" name="term" id="t3" value="3"/><label for="t3">3</label></div>
		<div class="spacer"></div>
		<div class="leftCell">ปีการศึกษา : </div>
		<div>
			<select name="year" id="year">
				<?php $year = date("Y"); for($i=$year;$i<=($year+3);$i++){ echo '<option value="'.$i.'"';if($year==$i) echo ' selected';echo '>'.($i+543).'</option>';}?>
			</select>
		</div>
		<div class="spacer"></div>
		<div class="leftCell">วิชา :</div> 
		<div>
			<select name="subject" id="subject">
					<?php
					$strSQL = 'SELECT * FROM `subject`';
					$objQuery = mysql_query($strSQL);
					if(mysql_num_rows($objQuery)>=1){
						while($row = mysql_fetch_array($objQuery)){
							$data.='<option value="'.$row['subjectID'].'">'.$row['subjectID'].' '.$row['name'].'('.($row['type']=='BASIC'?'พื้นฐาน':'เพิ่มเติม').')</option>';
						}
					} else {
						$data = '<option>ไม่พบวิชา</option>';
					}
					echo $data;
					?>
			</select>
		</div>
		<div class="spacer"></div>
		<div class="leftCell">ครูผู้สอน :</div> 
		<div>
			<select name="instructor" id="instructor">
					<?php
					$data='';
					$strSQL = 'SELECT * FROM `instructor`';
					$objQuery = mysql_query($strSQL);
					if(mysql_num_rows($objQuery)>=1){
						while($row = mysql_fetch_array($objQuery)){
							$data.='<option value="'.$row['instructorID'].'">'.$row['firstName'].' '.$row['lastName'].' ['.$row['instructorID'].']</option>';
						}
					} else {
						$data = '<option>ไม่พบครูผู้สอน</option>';
					}
					echo $data;
					?>
			</select>
		</div>
		<div class="spacer"></div>
		<div class="leftCell">สำหรับระดับชั้น :</div> 
		<div>
			<select name="gradeYear" id="gradeYear">
				<option value="7">มัธยมศึกษาปีที่ 1</option>
				<option value="8">มัธยมศึกษาปีที่ 2</option>
				<option value="9">มัธยมศึกษาปีที่ 3</option>
			</select>
		</div>
	</div>
	<div style="margin: auto;"><input type="submit" name="submit" value="บันทึก" /></div>
	</form>
<?php 
	} elseif($tag=='studentRegistrar') {
?>
<style>
	  .error {
	  	color: #900;
	  	text-decoration: underline;
	  	font-weight: bold;
	  }
	  .pass {
	  	color: #090;
	  	text-decoration: underline;
	  	font-weight: bold;
	  }
	  #subject {
	  	width: 320px;
	  }
	  #subject-button {
	  	font-weight: normal !important;
	  }
  </style>
<script>
	$(function(){
		function getRegSubList(){
			$( "#subject" ).load( "dataCenter.php", { "action": "get", "type":"regSubList", "term":$( "input[type='radio']:checked","#term" ).val(), "year":$( "#year" ).val() } , function( response, status, xhr ) {
				$( "#subject" ).selectmenu( "refresh" );
			});
		}
		$( "#term" ).buttonset();
		$( "#year" ).selectmenu({
			change: function( event, ui ) {
				getRegSubList();
			}
		});
		$( "#subject" ).selectmenu();
		$( "input[type='radio']","#term" )
			.click(function(){
				getRegSubList();
			});
		getRegSubList();
		$( "button","#searchRegSub" ).button({
			icons: {
				primary: "ui-icon-locked"
			},
			click: function( event, ui){
				event.preventDefault();
				getRegSubList();
			}
		});
	});
</script>
<h1>ลงทะเบียนนักเรียนเข้าวิชาเรียน</h1>
<?php if($result) if($error) echo '<span class="error">'.$result.'</span>'; else echo '<span class="pass">'.$result.'</span>';?>
	<form method="POST">
	<div id="formHolder">
		<div class="leftCell">ภาคการศึกษา : </div>
		<div id="term"><input type="radio" name="term" id="t1" value="1"/><label for="t1">1</label><input type="radio" name="term" id="t2" value="2"/><label for="t2">2</label><input type="radio" name="term" id="t3" value="3"/><label for="t3">3</label></div>
		<div class="spacer"></div>
		<div class="leftCell">ปีการศึกษา : </div>
		<div>
			<select name="year" id="year">
				<?php $year = date("Y"); for($i=$year;$i<=($year+3);$i++){ echo '<option value="'.$i.'"';if($year==$i) echo ' selected';echo '>'.($i+543).'</option>';}?>
			</select>
		</div>
		<div class="spacer"></div>
		<div class="leftCell">วิชา :</div> 
		<div>
			<select name="subject" id="subject">
				<option>กรุณาเลือกภาคการศึกษาและปีการศึกษา</option>
			</select>
		</div>
		<div class="spacer"></div>
	</div>
	<div id="searchRegSub" style="margin: auto;"><button>ค้นหา</button></div>
	<div style="margin: auto;"><input type="submit" name="submit" value="บันทึก" /></div>
	</form>
<?php
	} else {
		echo "<script>window.location='?';</script>";
	}
?>
</div>