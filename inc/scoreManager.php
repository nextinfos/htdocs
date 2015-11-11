<style>
	#scoreInfoConf, #scoreInfoSelectHolder {
		display: none;
	}
	#scoreInfoText {
		font-size: 1.5em;
	}
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
	#edit, #search, #cancel {
		display: none;
	}
	.scoreSpinner {
		width: 50px;
	}
</style>
<script>
	function genScore(){
		var score = new Array;
		if($('#scoreType').val()=='GRADE'){
			var obj = $( 'select[name=score]' );
		} else {
			var obj = $( 'input[name=score]' );
		}
		for(var i =0; i<obj.length; i++){
			score[i] = obj.eq(i).val();
		}
		return score;
	}
	function genStudentID(){
		var studentID = new Array;
		var obj = $( 'input[name=studentID]' );
		for(var i =0; i<obj.length; i++){
			studentID[i] = obj.eq(i).val();
		}
		return studentID;
	}
	$(function() {
		var score = $( "#scoreMax" ).spinner({min:0});
		$( "#subject" ).selectmenu({
			change: function(){
				$( '#loading' ).show( 'fade', 500 );
				$( '#scoreInfoConf' ).hide('blind', 500);
				$( "#scoreInfoSelectHolder" ).hide('blind', 500);
				$( "#search" ).hide('blind', 500);
				if($( "#subject" ).val()!=0){
					$( '#edit' ).hide('fade', 500);
					$( '.studentList' ).hide('blind', 500);
				} else {
					$( "#scoreInfoSelectHolder" ).hide('blind', 500);
					$( '#scoreInfoConf' ).hide('blind', 500);
				}
				$( "#scoreInfoSelector" ).load("dataCenter.php", {
					action: "get",
					type: "getScoreList",
					subjectID:$( "#subject" ).val()
				}, function(){
					$( "#scoreInfoSelector" ).selectmenu("refresh");
					$( '#loading' ).hide( 'fade', 500 );
					$( '#scoreInfoSelectHolder' ).show('blind', 500);
					if($( "#scoreInfoSelector" ).val()!=0){
						$( '#scoreInfoSelector' ).selectmenu('enable');
					} else {
						$( '#scoreInfoSelector' ).selectmenu('disable');
					}
				});
			}
		});
		$( "#scoreType" ).selectmenu({
			change: function(){
				if($(this).val()=='GRADE'){
					$( '#scoreMaxHolder' ).hide('blind', 500);
				} else {
					$( '#scoreMaxHolder' ).show('blind', 500);
				}
			}
			});
		$( "#scoreInfoSelector" ).selectmenu();
		$( "#subject" ).load("dataCenter.php", {
			action: "get",
			type: "regInsSubList"
		}, function(){
			$( "#subject" ).selectmenu("refresh");
			if($( "#subject" ).val()!=0){
				$( '#scoreInfoSelectHolder' ).show('blind', 500);
			} else {
				$( '#subject' ).selectmenu('disable');
			}
			$( "#scoreInfoSelector" ).load("dataCenter.php", {
				action: "get",
				type: "getScoreList",
				subjectID:$( "#subject" ).val()
			}, function(){
				$( "#scoreInfoSelector" ).selectmenu("refresh");
				if($( "#scoreInfoSelector" ).val()!=0){
				} else {
					$( '#scoreInfoSelector' ).selectmenu('disable');
				}
			});
		});
		$( "input[type=submit], button" )
			.button()
			.click(function( event ) {
		});
		studentListVar = $('#studentList').on( 'xhr.dt', function(){
			setTimeout(function(){
				$( 'input[name="score"]' ).spinner({
					min: 0,
					max: $( '#scoreMax' ).val(),
					change: function(){
						if($.isNumeric($(this).val())){
							if(parseInt($(this).val())>parseInt($( '#scoreMax' ).val())){
								console.log($(this).val()+' > '+$( '#scoreMax' ).val()+' = ');
								alert('คุณกรอกคะแนนเกินคะแนนเต็ม\nคะแนนเต็มสำหรับครั้งนี้คือ '+$( '#scoreMax' ).val()+' คะแนน\nคะแนนที่คุณกรอกมาคือ '+$(this).val()+' คะแนน');
								$(this).val($( '#scoreMax' ).val());
							} else if(parseInt($(this).val())<0){
								alert('คะแนนไม่สามารถมีค่าติดลบได้\nคะแนนที่คุณกรอกมาคือ '+$(this).val()+' คะแนน');
								$(this).val('0');
							}
						} else {
							alert('กรุณากรอกคะแนนเป็นตัวเลข');
							$(this).val('0');
						}
					}
				}).addClass('scoreSpinner');
				$( 'select[name=score]' ).selectmenu();
//				$( 'input[name=score]' ).attr('max',$( '#scoreMax' ).val());
			}, 100);
		}).DataTable( {
			paging: false,
			scrollY: 370,
			"order": [2,'asc'],
//			"orderFixed": [2,'desc'],
			"columns": [{ data: "studentID","orderable": true },{ data: "firstName","orderable": true },{ data: "lastName","orderable": true },{ data: "cardID","orderable": false },{ data: "secondCardID","orderable": false },{ data: "score","orderable": false}],
			ajax:  {
				url: "dataCenter.php",
				type: 'POST',
				data: function ( d ) {
					d.action = "get";
					d.type = "stuScoList";
					d.subjectID = $( "#subject" ).val();
					d.scoreID = $( "#scoreInfoSelector" ).val();
					d.scoreType = $( "#scoreType" ).val();
				}
			}
		});
		$('#search').button().click(function(event){
				event.preventDefault();
				if($('#subject').val()!=0){
					$.post('dataCenter.php',{
						action: 'set',
						type: 'setScoreInfo',
						subjectID: $( '#subject' ).val(),
						scoreType: $( '#scoreType' ).val(),
						scoreMax: $( '#scoreMax' ).val(),
						addStatus: $( '#addStatus' ).val(),
						scoreID: $( '#scoreInfoSelector' ).val()
					},function(data){
						data = $.parseJSON(data);
						switch($('#scoreType').val()){
							case "TASK":	var type = 'คะแนนชิ้นงาน'; break;
							case "QUIZ":	var type = 'คะแนนตอบคำถาม'; break;
							case "EXAM":	var type = 'คะแนนสอบ'; break;
							case "GRADE":	var type = 'เกรด'; break;
						}
						if(data.status=='SUCCESS'){
							if($( '#scoreType' ).val()=='GRADE'){
								$( '#infoScoreMaxHolder' ).hide('fade', 1);
							} else {
								$( "#scoreInfoSelector" ).load("dataCenter.php", {
									action: "get",
									type: "getScoreList",
									subjectID:$( "#subject" ).val()
								},function(){
									$( "#scoreInfoSelector" ).val(data.scoreID);
									$( "#scoreInfoSelector" ).selectmenu( "refresh" );
								});
								$( '#edit' ).show('fade', 500);
								$( '#infoScoreMax' ).html($( '#scoreMax' ).val());
							}
							studentListVar.ajax.reload();
							$( '#infoSubName' ).html($( 'option[value='+$( '#subject' ).val()+']' ).html());
							$( '#infoScoreType' ).html(type);
							$( '#search, #cancel' ).hide('fade', 1);
							$( '#scoreInfoConf' ).hide('blind', 500);
							$( '.studentList' ).show('blind', 500);
							$( '#addStatus' ).val('0');
						}
					});
				} else {
					alert('กรุณาเลือกวิชา');
				}
		});
		$( '#edit' ).button().click(function(){
			$( '#scoreInfoConf' ).show('blind', 500);
			$( '.studentList' ).hide('blind', 500);
			$( '#search, #cancel' ).show('fade', 500);
			$( '#edit' ).hide('fade', 1);
			$( '#addStatus' ).val('2');
		});
		$( '#add' ).button().click(function(){
			$( '#scoreInfoConf' ).show('blind', 500);
			$( '.studentList' ).hide('blind', 500);
			$( '#scoreInfoSelectHolder' ).hide('blind', 500);
			$( '#search, #cancel' ).show('fade', 500);
			$( '#addStatus' ).val('1');
		});
		$( '#cancel' ).button().click(function(){
			if($( '#addStatus' ).val()=='1'){
				$( '#scoreInfoConf' ).hide('blind', 500);
				$( '#scoreInfoSelectHolder' ).show('blind', 500);
			} else if($( '#addStatus' ).val()=='2'){
				$( '#scoreInfoConf' ).hide('blind', 500);
				$( '#edit' ).show('fade', 500);
				$( '.studentList' ).show('blind', 500);
			}
			$( '#cancel, #search' ).hide('fade', 1);
			$( '#addStatus' ).val('0');
		});
		$( '#save' ).button().click(function(){
			$.post('dataCenter.php',{
				action:'set',
				type:'setScore',
				scoreID:$( '#scoreInfoSelector' ).val(),
				scoreType: $( '#scoreType' ).val(),
				subjectID: $( '#subject' ).val(),
				score:JSON.stringify(genScore()),
				studentID:JSON.stringify(genStudentID())},
				function(data){
					data = $.parseJSON(data);
					console.log(data);
					alert('บันทึกคะแนนเรียบร้อย');
			});
		});
		$( '#editScore' ).button().click(function(){
			$.post('dataCenter.php',{
					action: 'get',
					type: 'scoreInfo',
					scoreID: $( '#scoreInfoSelector' ).val()
				},function(data){
					data = $.parseJSON(data);
				switch(data.type){
					case "TASK":	var type = 'คะแนนชิ้นงาน'; break;
					case "QUIZ":	var type = 'คะแนนตอบคำถาม'; break;
					case "EXAM":	var type = 'คะแนนสอบ'; break;
				}
				studentListVar.ajax.reload();
				$( '#scoreMax' ).val(data.maxScore);
				$( '#scoreType' ).val(data.type);
				$( "#scoreType" ).selectmenu( "refresh" );
				$( '#infoSubName' ).html($( 'option[value='+$( '#subject' ).val()+']' ).html());
				$( '#infoScoreType' ).html(type);
				$( '#infoScoreMax' ).html($( '#scoreMax' ).val());
				$( '#edit' ).show('fade', 1);
				$( '#search, #cancel' ).hide('fade', 1);
				$( '#scoreInfoSelectHolder' ).hide('blind', 500);
				$( '#scoreInfoConf' ).hide('blind', 500);
				$( '.studentList' ).show('blind', 500);
			});
		});
		$('fieldset').addClass("ui-corner-all");
		$('.studentListContainer').addClass("fg-toolbar ui-toolbar ui-widget-header ui-helper-clearfix");
	});
</script>
<div id="formHolder">
	<div><h1>เพิ่มการลงคะแนน</h1></div>
	<div>
		<select name="subject" style="width:300px;" id="subject">
	  		<option value="0">กำลังประมวลผล</option>
		</select>
	</div>
	<div id="scoreInfoConf">
		<div class="leftCell">
			<h3>กรอกข้อมูลการลงคะแนน</h3>
		</div>
		<div class="spacer"></div>
		<div>
			<select style="width:300px;" id="scoreType">
			  <option value="TASK">คะแนนชิ้นงาน</option>
			  <option value="QUIZ">คะแนนตอบคำถาม</option>
			  <option value="EXAM">คะแนนสอบ</option>
			  <option value="GRADE">เกรด</option>
			</select>
		</div>
		<div class="spacer"></div>
		<div id="scoreMaxHolder">
				<label>คะแนนเต็ม : </label><input name="scoreMax" id="scoreMax" style="width:50px;" value="10">
		</div>
		<div class="spacer"></div>
	</div>
	<div id="scoreInfoSelectHolder">
		<div class="leftCell">
			<h3>เลือกข้อมูลการลงคะแนน</h3>
		</div>
		<div class="spacer"></div>
		<div>
			<select style="width:300px;" id="scoreInfoSelector">
				<option value="0">กำลังประมวลผล</option>
			</select>
		</div>
		<div class="spacer"></div>
		<div>
			<input type="submit" id="add" value="เพิ่มการลงคะแนนใหม่">
			<input type="submit" id="editScore" value="แก้ไขคะแนน">
		</div>
	</div>
	<div>
		<input type="submit" id="search" value="บันทึกการลงคะแนน"/>
		<input type="submit" id="edit" value="แก้ไข"/>
		<input type="submit" id="cancel" value="ยกเลิก"/>
	</div>
	<div class="studentList">
		<fieldset class="studentListContainer">
			<legend style="margin-left:12px;">รายละเอียดการลงคะแนน</legend>
			วิชา : <span id="infoSubName"></span><br/>
			ประเภท : <span id="infoScoreType"></span><br/>
			<span id="infoScoreMaxHolder">คะแนนเต็ม : <span id="infoScoreMax"></span> คะแนน</span>
		</fieldset>
		<fieldset class="studentListContainer">
			<legend style="margin-left:12px;">รายชื่อนักเรียนที่ลงทะเบียน</legend>
			<div>
				<table id="studentList" class="display" style="width:100%">
					<thead>
						<tr class="ui-state-default">
							<th style="width: 100px;">รหัสนักเรียน</th>
							<th>ชื่อ</th>
							<th>นามสกุล</th>
							<th style="width: 0px;"></th>
							<th style="width: 0px;"></th>
							<th style="width: 120px;">คะแนน</th>
						</tr>
					</thead>
				</table>
			</div>
		</fieldset>
		<input type="submit" id="save" value="บันทึก"/>
	</div>
</div>
<input type="hidden" id="addStatus" value="0">
<?php
	
?>