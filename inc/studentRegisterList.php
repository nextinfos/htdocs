<style>
	#formHolder {
		width: 100%;
	}
	.studentList {
		display: none;
	}
	.studentListContainer {
		padding-left: 0px;
		padding-right: 0px;
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
</style>
<script>
	$(function() {
		$( "#subject" ).selectmenu({
			change: function(){
				$( '#loading' ).show( 'fade', 500 );
				if($( "#subject" ).val()!=0){
					studentListVar.ajax.reload();
					$('.studentList').show('blind', 500);
				} else {
					$('.studentList').hide('blind', 500);
					$( "#subject" ).selectmenu("disable");
					$( '#loading' ).hide( 'fade', 500 );
				}
			}
		});
		$( "#subject" ).load("dataCenter.php", {
			action: "get",
			type: "regInsSubList"
		}, function(){
			$( "#subject" ).selectmenu("refresh");
			if($( "#subject" ).val()!=0){
				studentListVar.ajax.reload();
				$('.studentList').show('blind', 500);
			} else {
				$( "#subject" ).selectmenu("disable");
			}
		});
		studentListVar = $('#studentList').on( 'xhr.dt', function(){
			$( '#loading' ).hide( 'fade', 500 );
		}).DataTable( {
			paging: false,
			scrollY: 370,
			"order": [0,'asc'],
//			"orderFixed": [2,'desc'],
			"columns": [{ data: "studentID","orderable": true },{ data: "firstName","orderable": true },{ data: "lastName","orderable": true },{ data: "cardID","orderable": false },{ data: "grade","orderable": false}],
			ajax:  {
				url: "dataCenter.php",
				type: 'POST',
				data: function ( d ) {
					d.action = "get";
					d.type = "stuRegList";
					d.subjectID = $( "#subject" ).val();
				}
			}
		});
		$( "#term" ).buttonset();
		$( "#year" ).selectmenu();
		$('fieldset').addClass("ui-corner-all");
		$('.studentListContainer').addClass("fg-toolbar ui-toolbar ui-widget-header ui-helper-clearfix");
	});
</script>
<?php
	$year = getYear();
	$term = getTerm();
?>
<div id="formHolder">
	<div><h1>รายชื่อนักเรียนที่ลงทะเบียนในรายวิชา</h1></div>
	<div class="leftCell">ภาคการศึกษา : </div>
		<div id="term"><input type="radio" name="term" id="t1" value="1"<?php echo radioTerm(1);?> disabled/><label for="t1">1</label><input type="radio" name="term" id="t2" value="2"<?php echo radioTerm(2);?> disabled/><label for="t2">2</label><input type="radio" name="term" id="t3" value="3"<?php echo radioTerm(3);?> disabled/><label for="t3">3</label></div>
		<div class="spacer"></div>
		<div class="leftCell">ปีการศึกษา : </div>
		<div>
			<select name="year" id="year" disabled>
				<?php echo "<option value='$year' selected>".($year+543).'</option>';?>
			</select>
		</div>
		<div class="spacer"></div>
	<div>
		<select name="subject" style="width:300px;" id="subject">
	  		<option value="0">กำลังประมวลผล</option>
		</select>
	</div>
	<div class="studentList">
		<fieldset class="studentListContainer">
			<legend style="margin-left:12px;">รายชื่อนักเรียนที่ลงทะเบียนและเกรด</legend>
			<div>
				<table id="studentList" class="display" style="width:100%">
					<thead>
						<tr class="ui-state-default">
							<th style="width: 100px;">รหัสนักเรียน</th>
							<th>ชื่อ</th>
							<th>นามสกุล</th>
							<th style="width: 0px;"></th>
							<th style="width: 120px;">เกรด</th>
						</tr>
					</thead>
				</table>
			</div>
		</fieldset>
	</div>
</div>
<?php
