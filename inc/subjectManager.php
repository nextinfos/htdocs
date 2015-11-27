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
	#subjectList_info, #subjectList_length {
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
	  .studentListContainer {
		padding-left: 0px;
		padding-right: 0px;
		display: none;
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
	input[name=submit]{
		display: none;
	}
  </style>
<script>
function getRegSubList(){
	console.log('Function getRegSubList Called.');
	$( "#subject" ).load( "dataCenter.php", { "action": "get", "type":"regSubList", "term":$( "input[type='radio']:checked","#term" ).val(), "year":$( "#year" ).val() } , function( response, status, xhr ) {
		$( "#subject" ).selectmenu( "refresh" );
		console.log('Select menu "subject" was refreshed.');
	});
}
	$(function(){
		var term = $( "input[type='radio']:checked","#term" ).val();
		var year = $('#year').val();
		var subjectID = $( "#subject" ).val();
		studentListVar = $('#studentList').DataTable( {
			paging: false,
		    scrollY: 370,
		    "order": [0,'asc'],
//			    "orderFixed": [2,'desc'],
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
		$( "input:button" ).button().click(function(event, ui){
			window.location = "?action=subjectManager&tag=studentRegistrarPackage";
		});
		$( "button","#searchRegSub" ).button({
			icons: {
				primary: "ui-icon-search"
			}
		}).click(function( event, ui){
			event.preventDefault();
			//getRegSubList();
			if($('#subject').val()!=0){
				studentListVar.ajax.reload();
				$('.studentListContainer').show("blind", 500);
				$('input[name=submit]').show("fade", 500);
				$('html,body,.mainContainer').animate({
			          scrollTop: $( '#studentList').offset().top
		        }, 1000, 'swing');
			} else {
				alert('กรุณาเลือกวิชา');
			}
		});
		$( '#studentRegistrar' ).submit(function(event){
			event.preventDefault();
			if($( "input[type='radio']:checked","#term" ).val()&&$('#year').val()&&$('#subject').val()!=0){
				if($('table#studentList>tbody>tr>td').html()>0){
					$('#loading' ).show('fade', '', '100');
					$.post( "dataCenter.php", {
						action: "set",
						type: "regStudent",
						term: $( "input[type='radio']:checked","#term" ).val(),
						year: $('#year').val(),
						subjectID: $('#subject').val()
					}, function(data){
						var text;
						if(data=='OK'){
							text = 'ลงทะเบียนนักเรียนเข้าสู่รายวิชาเรียบร้อยแล้ว';
							$('.studentListContainer').hide("blind", 500);
							$('input[name=submit]').hide("fade", 500);
						} else if(data=='UNVALID'){
							text = 'กรุณาเลือก ภาคการศึกษา, ปีการศึกษา และ วิชา ให้ครบถ้วน';
						} else if(data=='ERROR'){
							text = 'ข้อผิดพลาด : ไม่สามารถบันทึกข้อมูลได้ กรุณาติดต่อผู้พัฒนา';
						} else {
							text = 'ข้อผิดพลาด : ไม่ทราบปัญหา\n['+data+']';
						}
						setTimeout(function(){
							alert(text);
							$('#loading' ).hide('fade', '', '1000');
						},800);
					});
				} else {
					alert('ไม่พบรายชื่อนักเรียนที่ตรงกับเงื่อนไขวิชา');
				}
			} else {
				alert('กรุณาเลือกภาคการศึกษา, ปีการศึกษา และ วิชา');
			}
		});
		$('fieldset').addClass("ui-corner-all");
		$('.studentListContainer').addClass("fg-toolbar ui-toolbar ui-widget-header ui-helper-clearfix");
	});
</script>
<h1>ลงทะเบียนนักเรียนเข้าวิชาเรียน</h1>
<?php if($result) if($error) echo '<span class="error">'.$result.'</span>'; else echo '<span class="pass">'.$result.'</span>';?>
	<form method="POST" id="studentRegistrar">
	<div id="formHolder">
		<div><input type="button" value="ลงทะเบียนแพกเกจ"></div>
		<div class="spacer"></div>
		<div class="leftCell">ภาคการศึกษา : </div>
		<div id="term"><input type="radio" name="term" id="t1" value="1"<?php echo radioTerm(1);?>/><label for="t1">1</label><input type="radio" name="term" id="t2" value="2"/><label for="t2">2</label><input type="radio" name="term" id="t3" value="3"/><label for="t3">3</label></div>
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
				<option value="0">กรุณาเลือกภาคการศึกษาและปีการศึกษา</option>
			</select>
		</div>
		<div class="spacer"></div>
	</div>
	<div id="searchRegSub" style="margin: auto;"><button>ค้นหา</button></div>
	<div class="spacer"></div>
	<div>
		<fieldset class="studentListContainer">
  			<legend style="margin-left:12px;">รายชื่อวิชาในระบบ</legend>
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
	<div class="spacer"></div>
	<div style="margin: auto;"><input type="submit" name="submit" value="บันทึก" /></div>
	</form>
<?php
	} elseif($tag=="studentRegistrarPackage"){
?>
<script>
	function getGradeYearList(){
		$( "#gradeYear" ).load( "dataCenter.php", { 
			"action": "get", 
			"type":"regGradeYearList", 
			"term":$( "input[type='radio']:checked","#term" ).val(), 
			"year":$( "#year" ).val() 
		} , function( response, status, xhr ) {
			$( "#gradeYear" ).selectmenu( "refresh" );
		});
	}
	function genDataCheck(obj){
		var data = new Array;
		var obj = $( 'input[name='+obj+']' );
		var objl = obj.length;
		for(var i =0; i<objl; i++){
			data[i] = obj.eq(i).is(":checked");
		}
		return data;
	}
	function genStudentID(){
		var studentID = new Array;
		var obj = $( 'input[name=studentID]' );
		for(var i =0; i<obj.length; i++){
			studentID[i] = obj.eq(i).val();
		}
		return studentID;
	}
	var subjectReg = new Array;
	$(function(){
// 		studentListVar = $('#studentList').DataTable( {
// 			paging: false,
// 		    scrollY: 370,
// //		    "order": [0,'asc'],
// //			    "orderFixed": [2,'desc'],
// 		    "columns": [{ data: "studentID","orderable": true },{ data: "firstName","orderable": true },{ data: "lastName","orderable": true },{ data: "gradeYear","orderable": false}],
// 		    ajax:  {
// 	            url: "dataCenter.php",
// 	            type: 'POST',
// 	            data: function ( d ) {
// 	                d.action = "get";
// 	                d.type = "stuCanRegPackage";
// 	                d.term = $( "input[type='radio']:checked","#term" ).val();
// 	                d.year = $( "#year" ).val();
// 	                d.gradeYear = $( "#gradeYear" ).val();
// 	            }
// 	        }
// 		});
		var term = $( "input[type='radio']:checked","#term" ).val();
		var year = $('#year').val();
		var subjectID = $( "#subject" ).val();
		$( "#gradeYear" ).selectmenu();
		$( "#term" ).buttonset();
		$( "#year" ).selectmenu({
			change: function( event, ui ) {
				getGradeYearList();
			}
		});
		$( "input[type='radio']","#term" )
			.click(function(){
				getGradeYearList();
			});
		getGradeYearList();
		$( "button","#searchRegSub" ).button({
			icons: {
				primary: "ui-icon-search"
			}
		}).click(function( event, ui){
			event.preventDefault();
			getGradeYearList();
			if($('#gradeYear').val()!=0){
				//studentListVar.ajax.reload();
				$.post('dataCenter.php',{
					action: 'get',
					type: 'stuCanRegPackage',
					term: $( "input[type='radio']:checked","#term" ).val(),
	                year: $( "#year" ).val(),
	                gradeYear: $( "#gradeYear" ).val()
				},function(data){
					subjectReg = new Array;
					data = JSON.parse(data);
					var render;
					render = render+'<thead class="ui-state-default"><tr><th style="width:50px;">รหัส</th><th style="min-width:100px;">ชื่อจริง</th><th style="min-width:100px;">นามสกุล<div class="checkAllSub">Check All : <input type="checkbox" id="checkall" checked></div></th>';
					$.each(data.subject,function(mkey,mval){
						var curSubjectID = null;
						$.each(mval,function(key,val){
							if(key=='subjectID'){
								subjectReg[subjectReg.length] = val;
								curSubjectID = val;
							}
							if(key=='name') render = render+'<th class="subjectName"><div class="subjectName"><input type="checkbox" name="checkAll" data-subjectID="'+curSubjectID+'" checked>'+val+'</div></th>';
						});
					});
					render = render+'</tr></thead><tbody>';
					var oi =0;
					$.each(data.data,function(mkey,mval){
						if(oi%2==0){ var trcl = 'odd'; }else{ var trcl = 'even';}
						oi++;
						render = render+'<tr class="'+trcl+'">';
						render = render+'<td><input type="hidden" name="studentID" value="'+mval.studentID+'">'+mval.studentID+'</td>'+'<td>'+mval.firstName+'</td>'+'<td>'+mval.lastName+'</td>';
						$.each(data.subject,function(skey,sval){
							$.each(sval,function(key,val){
								if(key=='subjectID'){
									$.each(data.isStuReg,function(ikey,ival){
										if(ikey==mval.studentID){
											$.each(ival,function(iskey,isval){
												if(iskey==val){
// 													console.log(isval);
													if(isval){
					 									render = render+'<td><input type="checkbox" class="dis" name="'+iskey+'" disabled/></td>';
					 								} else {
					 									render = render+'<td><input type="checkbox" name="'+iskey+'" checked/></td>';
													}
												}
											});
										}
									});
								}
							});
						});
						render = render+'</tr>';
					});
					render = render+'</tbody>';
					$('#studentList').html(render);
					$( 'input[name=checkAll' ).change(function(){
// 						console.log($(this).attr("data-subjectid"));
						$('input[name='+$(this).attr("data-subjectid")+']:not([disabled])').prop("checked",$(this).is(':checked'));
						if(!$(this).is(':checked')) $( '#checkall' ).prop("checked",false);
// 						console.log($('input[name='+$(this).attr("data-subjectid")+']:not([disabled])'));
					});
					$( '#checkall' ).change(function(){
// 						$('input[name=checkAll]').prop("checked",$(this).is(':checked'));
						$('input:not([disabled]):not([name=term])').prop("checked",$(this).is(':checked'));
					});
					$( 'input[type=checkbox]').change(function(){
// 						console.log($('input[data-subjectid='+$(this).attr("name")+']'));
// 						if(!$(this).is(':checked')) $( '#checkall' ).prop("checked",false);
						if($('input[data-subjectid='+$(this).attr("name")+']')){
							if ($('input[name='+$(this).attr("name")+']:checked').length == $('input[name='+$(this).attr("name")+']:not([disabled])').length)
								$('input[data-subjectid='+$(this).attr("name")+']').prop("checked",true);
							else
								$('input[data-subjectid='+$(this).attr("name")+']').prop("checked",false);
						}
						if ($('input[name=checkAll]:checked').length == $('input[name=checkAll]').length) 
							$( '#checkall' ).prop("checked",true);
						else
							$( '#checkall' ).prop("checked",false);
					});
				});
				$('.studentListContainer').show("blind", 500);
				$('input[name=submit]').show("fade", 500);
				$('html,body,.mainContainer').animate({
			          scrollTop: $( '#studentList').offset().top
		        }, 1000, 'swing');
			} else {
				alert('กรุณาเลือกชั้นปี');
			}
		});
		$( '#studentRegistrar' ).submit(function(event){
			event.preventDefault();
			if($( "input[type='radio']:checked","#term" ).val()&&$('#year').val()&&$('#subject').val()!=0&&$( 'input[type=checkbox]:not([name=checkAll]):not([id=checkall]):checked' ).size()>0){
				if($('table#studentList>tbody>tr').size()>0){
					var sendData=new Array;
					$.each(subjectReg,function(key,val){
						console.log(val);
						var preData = new Array;
// 						sendData[eval("'val'")] = genDataCheck(val);
// 						preData['subjectID'] = val; 
// 						preData['data'] = JSON.stringify(genDataCheck(val));
// 						preData['data'] = genDataCheck(val);
						sendData[key] = '{"subjectID":"'+val+'","data":'+JSON.stringify(genDataCheck(val))+'}';
						console.log(sendData[key]);
					});
// 					var rsendData = JSON.stringify(sendData);
					console.log(JSON.stringify(sendData));
					$('#loading' ).show('fade', '', '100');
					$.post( "dataCenter.php", {
						'action': "set",
						'type': "regStudentPackage",
						'term': $( "input[type='radio']:checked","#term" ).val(),
						'year': $('#year').val(),
						'studentID': JSON.stringify(genStudentID()),
						'data': sendData
					}, function(data){
						data = $.parseJSON(data);
						var text;
						if(data.status=='SUCCESS'){
							text = 'ลงทะเบียนนักเรียนเข้าสู่รายวิชาเรียบร้อยแล้ว';
							$('.studentListContainer').hide("blind", 500);
							$('input[name=submit]').hide("fade", 500);
						} else if(data.status=='UNVALID'){
							text = 'กรุณาเลือก ภาคการศึกษา, ปีการศึกษา และ วิชา ให้ครบถ้วน';
						} else if(data.status=='OK'){
							text = 'มีนักเรียนบางคนไม่สามารถลงทะเบียนได้ กรุณาติดต่อผู้พัฒนา\n['+data.fail+']';
						} else if(data.status=='ERROR'){
							text = 'ข้อผิดพลาด : ไม่สามารถบันทึกข้อมูลได้ กรุณาติดต่อผู้พัฒนา';
						} else {
							text = 'ข้อผิดพลาด : ไม่ทราบปัญหา\n['+data+']';
						}
						setTimeout(function(){
							alert(text);
							$('#loading' ).hide('fade', '', '1000');
						},800);
					});
				} else {
					alert('ไม่พบรายชื่อนักเรียนที่ตรงกับเงื่อนไขวิชา');
				}
			} else {
				if($( 'input[type=checkbox]:not([name=checkAll]):not([id=checkall]):checked' ).size()>0){
					alert('กรุณาเลือกภาคการศึกษา, ปีการศึกษา และ ชั้นปี');
				} else {
					alert('ไม่มีข้อมูลให้บันทึก');
				}
			}
		});
		$('fieldset').addClass("ui-corner-all");
		$('.studentListContainer').addClass("fg-toolbar ui-toolbar ui-widget-header ui-helper-clearfix");
	});
</script>
<style>
	#gradeYear {
	  	width: 320px;
	  }
	  .studentListContainer, input[name=submit] {
	  	display: none;
	  }
	th {
		text-align: center;
	}
	th.subjectName {
		padding-top: 150px !important;
		width: 18px;
	}
	div.subjectName {
		font-size: 0.7em;
		 -webkit-transform: rotate(-90deg);
	 	width: 150px;
	 	height: 0px;
	 	margin: -75px;
	 	position: relative;
	 	text-align: left;
	}
	div.subjectName>input[type=checkbox] {
		 -webkit-transform: rotate(90deg);
	     margin-left: -5px;
	    margin-right: 5px;
	    margin-bottom: -2.5px;
	    margin-top: 2.5px;
	}
	input[type=checkbox].dis:hover {
		cursor: not-allowed;
	}
	div.studentListContainer {
		max-width: 850px;
		margin: auto;
	}
	div.checkAllSub {
	    font-size: 0.7em;
	    position: relative;
	    height: 0px;
	    margin-top: 53px;
	    margin-bottom: -53px;
	    width: 100%;
	    text-align: right;
	}
</style>
<h1>ลงทะเบียนนักเรียนเข้าวิชาเรียน</h1>
<?php if($result) if($error) echo '<span class="error">'.$result.'</span>'; else echo '<span class="pass">'.$result.'</span>';?>
<form method="POST" id="studentRegistrar">
	<div id="formHolder">
		<div class="leftCell">ภาคการศึกษา : </div>
		<div id="term"><input type="radio" name="term" id="t1" value="1"<?php echo radioTerm(1);?>/><label for="t1">1</label><input type="radio" name="term" id="t2" value="2"/><label for="t2">2</label><input type="radio" name="term" id="t3" value="3"/><label for="t3">3</label></div>
		<div class="spacer"></div>
		<div class="leftCell">ปีการศึกษา : </div>
		<div>
			<select name="year" id="year">
				<?php $year = date("Y"); for($i=$year;$i<=($year+3);$i++){ echo '<option value="'.$i.'"';if($year==$i) echo ' selected';echo '>'.($i+543).'</option>';}?>
			</select>
		</div>
		<div class="spacer"></div>
		<div class="leftCell">ชั้นปี :</div> 
		<div>
			<select name="gradeYear" id="gradeYear">
				<option value="0">กรุณาเลือกภาคการศึกษาและปีการศึกษา</option>
			</select>
		</div>
		<div class="spacer"></div>
	</div>
	<div id="searchRegSub" style="margin: auto;"><button>ค้นหา</button></div>
	<div class="spacer"></div>
	<div class="studentListContainer">
		<fieldset class="studentListContainer">
  			<legend style="margin-left:12px;">รายชื่อวิชาในระบบ</legend>
  			<div>
	 			<table id="studentList" class="dataTable display" style="width:100%">
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
	<div class="spacer"></div>
	<div style="margin: auto;"><input type="submit" name="submit" value="บันทึก" /></div>
</form>
<?php
	} else {
		echo "<script>window.location='?';</script>";
	}
?>
</div>