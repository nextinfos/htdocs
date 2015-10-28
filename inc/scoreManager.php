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
	#edit, #search {
		display: none;
	}
	.scoreSpinner {
		width: 50px;
	}
</style>
<script>
	function genScore(){
		var score = new Array;
		var obj = $( 'input[name=score]' );
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
		$( "#scoreType" ).selectmenu();
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
							}	
						} else {
							alert('กรุณากรอกคะแนนเป็นตัวเลข');
							$(this).val('0');
						}
					}
				}).addClass('scoreSpinner');
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
					d.scoreID = $( "#scoreSelector" ).val();
				}
			}
		});
		$('#search').button().click(function(event){
				event.preventDefault();
				if($('#subject').val()!=0){
					studentListVar.ajax.reload();
					switch($('#scoreType').val()){
						case "TASK":	var type = 'คะแนนชิ้นงาน'; break;
						case "QUIZ":	var type = 'คะแนนตอบคำถาม'; break;
						case "EXAM":	var type = 'คะแนนสอบ'; break;
					}
					$( '#infoSubName' ).html($( 'option[value='+$( '#subject' ).val()+']' ).html());
					$( '#infoScoreType' ).html(type);
					$( '#infoScoreMax' ).html($( '#scoreMax' ).val());
					$( '#edit' ).show('fade', 500);
					$( '#search' ).hide('fade', 500);
					$( '#scoreInfoConf' ).hide('blind', 500);
					$( '.studentList' ).show('blind', 500);
				} else {
					alert('กรุณาเลือกวิชา');
				}
		});
		$( '#edit' ).button().click(function(){
			$( '#scoreInfoConf' ).show('blind', 500);
			$( '.studentList' ).hide('blind', 500);
			$( '#search' ).show('fade', 500);
			$( '#edit' ).hide('fade', 500);
		});
		$( '#add' ).button().click(function(){
			$( '#scoreInfoConf' ).show('blind', 500);
			$( '.studentList' ).hide('blind', 500);
			$( '#scoreInfoSelectHolder' ).hide('blind', 500);
			$( '#search' ).show('fade', 500);
		});
		$( '#save' ).button().click(function(){
			console.log(genScore());
			console.log(genStudentID());
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
			<h3>ข้อมูลการลงคะแนน</h3>
		</div>
		<div class="spacer"></div>
		<div>
			<select style="width:300px;" id="scoreType">
			  <option value="TASK">คะแนนชิ้นงาน</option>
			  <option value="QUIZ">คะแนนตอบคำถาม</option>
			  <option value="EXAM">คะแนนสอบ</option>
			</select>
		</div>
		<div class="spacer"></div>
		<div>
				<label>คะแนนเต็ม : </label><input name="scoreMax" id="scoreMax" style="width:50px;" value="10">
		</div>
		<div class="spacer"></div>
	</div>
	<div id="scoreInfoSelectHolder">
		<div class="leftCell">
			<h3>ข้อมูลการลงคะแนน</h3>
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
		<input type="submit" id="search" value="บันทึกการลงคะแนน">
		<input type="submit" id="edit" value="แก้ไข">
	</div>
	<div class="studentList">
		<fieldset class="studentListContainer">
			<legend style="margin-left:12px;">รายละเอียดการลงคะแนน</legend>
			วิชา : <span id="infoSubName"></span><br/>
			ประเภท : <span id="infoScoreType"></span><br/>
			คะแนนเต็ม : <span id="infoScoreMax"></span> คะแนน
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
<?php
	
?>