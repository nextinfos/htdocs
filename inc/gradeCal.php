<?php
?>
<style>
	input[name='min[]'],input[name='max[]'] {
		width: 30px;
		text-align: center !important;
	}
		#contentContainer {
		text-align:center;
		padding: 10px;
	}
	#formHolder {
/* 		text-align:center; */
		margin: auto;
		display: table;
	}
	#formHolder>div>div, #formHolder>div {
		margin: auto;
		padding: 2px;
	}
	.spacer {
		height: 5px;
	}
	.leftCell {
		padding-left: 5px !important;
	}
	#gradeInfo {
		margin: auto;
	}
	.gradeRange {
		width: 25px;
		text-align: center !important;
	}
	#gradeinfo>fieldset {
		width: 300px;
		font-size: 0.9em;
	}
	#gradeInfo, #searchHolder, #gradePreview {
		display:none;
	}
	#subject {
		width: 300px;
	}
	#normalizeHolder {
		display: none;
	}
</style>
<script>
function maxCheck(){
//		var index = $(this).attr("data-index");
	for(var index=0; index<7; index++){
		var obj = 'input[name="max[]"][data-index='+index+']';
// 		console.log(obj);
			var objSelector = 'input[name="min[]"][data-index='+index+']';
// 			console.log($(obj).val()+'<='+$( objSelector ).val());
			if(parseInt($(obj).val())<=parseInt($( objSelector ).val())){
// 				console.log('Changed');
				var objVal = parseInt($( objSelector ).val())+1;
				var minIndex = parseInt(index)+1;
				var minObjSelector = 'input[name="min[]"][data-index='+minIndex+']';
				$(obj).val(objVal);
				$( minObjSelector ).val(objVal);
			}
		if(parseInt($(obj).val())<=parseInt($('input[name="min[]"][data-index=0]').val())){
// 			console.log('min');
			$(obj).val('');
			var minIndex = parseInt(index)+1;
			var minObjSelector = 'input[name="min[]"][data-index='+minIndex+']';
			$( minObjSelector ).val('');
		}
		if(parseInt($(obj).val())>=parseInt($('input[name="max[]"][data-index=7]').val())){
// 			console.log($(obj).val()+'>='+$('input[name="max[]"][data-index=7]').val()+'max');
			$(obj).val('');
			var minIndex = parseInt(index)+1;
			var minObjSelector = 'input[name="min[]"][data-index='+minIndex+']';
			$( minObjSelector ).val('');
		}
	}
}
function genGradeRange(){
	var score = new Array;
	var obj = $( 'input[name="max[]"]' );
	for(var i =0; i<obj.length; i++){
		score[i] = obj.eq(i).val();
	}
	return score;
}
function genGrade(){
	var score = new Array;
		var obj = $( 'select[name=grade]' );
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
	$(function(){
		$('input:text, input:password').button().css({
			'font' : 'inherit',
			'text-align' : 'left',
			'outline' : 'none',
			'cursor' : 'text'
		});
		$('input:submit, button').button();
		$('button.gradeRange').button();
		$('fieldset').addClass("ui-corner-all fg-toolbar ui-toolbar ui-widget-header ui-helper-clearfix");
		$( 'input[name="min[]"]' ).change(function(){
			var index = parseInt($(this).attr("data-index"));
			if(index>0){
				$( 'input[name="max[]"]' ).eq((index-1)).val($( 'input[name="min[]"]' ).eq(index).val());
			}
			maxCheck();
		});
		$( 'input[name="max[]"]' ).change(function(){
			var index = parseInt($(this).attr("data-index"));
			if(index<7){
				$( 'input[name="min[]"]' ).eq((index+1)).val($( 'input[name="max[]"]' ).eq(index).val());
			}
			maxCheck();
		});
		$( 'input[name="max[]"]' ).change(function(){
			var index = $(this).attr("data-index");
			if(index>0){
				var checkIndex = parseInt(index)-1;
				var objSelector = 'input[name="max[]"][data-index='+checkIndex+']';
				if($(this).val()<=$( objSelector ).val()){
					var objVal = parseInt($( objSelector ).val())+1;
					var minIndex = parseInt(index)+1;
					var minObjSelector = 'input[name="min[]"][data-index='+minIndex+']';
					$(this).val(objVal);
					$( minObjSelector ).val(objVal);
				}
			}
			if(parseInt($(this).val())<=parseInt($('input[name="min[]"][data-index=0]').val())){
				console.log('min');
				$(this).val('');
				var minIndex = parseInt(index)+1;
				var minObjSelector = 'input[name="min[]"][data-index='+minIndex+']';
				$( minObjSelector ).val('');
			}
			if(parseInt($(this).val())>=parseInt($('input[name="max[]"][data-index=7]').val())){
				console.log($(this).val()+'>='+$('input[name="max[]"][data-index=7]').val()+'max');
				$(this).val('');
				var minIndex = parseInt(index)+1;
				var minObjSelector = 'input[name="min[]"][data-index='+minIndex+']';
				$( minObjSelector ).val('');
			}
		});
		$( "#subject" ).selectmenu({
			change:function(){
				$( '#gradeInfo' ).hide('blind', 500);
				$( '#searchHolder' ).show('fade', 500);
				$( '#gradePreview' ).hide('blind', 500);
			}
		});
		$( "#normalizeHolder" ).buttonset();
		$( "#normalization" ).button();
		$( "#subject" ).load("dataCenter.php", {
			action: "get",
			type: "regInsSubList"
		}, function(){
			$( "#subject" ).selectmenu("refresh");
			if($( "#subject" ).val()!=0){
				$( '#searchHolder' ).show('fade', 500);
			} else {
				$( '#subject' ).selectmenu('disable');
			}
		});
		$( '#search').click(function(){
			$( '#loading' ).show('fade',100);
			$.post('dataCenter.php',{
				action: 'get',
				type: 'gradeCal',
				tag: 'info',
				subjectID: $( '#subject' ).val(),
				normalize: $('#normalization').is(':checked')
			},function(data){
				data = $.parseJSON(data);
				if(data.status=='SUCCESS'){
					$( '#searchHolder' ).hide('blind', 500);
					if($('#normalization').is(':checked')){
						$( '#maxScore' ).val('100');
						$( 'input[name="max[]"][data-index=7]' ).val('100');
					} else {
						$( '#maxScore' ).val(data.maxScore);
						$( 'input[name="max[]"][data-index=7]' ).val(data.maxScore);
					}
					$( '#gradeInfo' ).show('blind', 500);
				} else if(data.status=='INVALID'){
					alert('ข้อมูลการเก็บคะแนนยังไม่ครบ\n(กรุณาบันทึกคะแนนจนถึง คะแนนสอบปลายภาค ก่อนทำการตัดเกรด)');
				} else if(data.status=='ERROR'){
					alert('มีข้อผิดพลาดเกิดขึ้น : ไม่สามารถเรียกข้อมูลการเก็บคะแนนได้\nกรุณาติดต่อผู้พัฒนา');
				} else {
					alert('มีข้อผิดพลาดเกิดขึ้น\nกรุณาติดต่อผู้พัฒนา');
				}
				$( '#loading' ).hide('fade',100);
			});
		});
		$( '#preview' ).click(function(){
			$( '#loading' ).show('fade',10);
			studentListVar.ajax.reload();
		});
		var studentListVar = $('#studentList').on( 'xhr.dt', function( e, settings, json, xhr){
// 			console.log(json);
			setTimeout(function(){
				$( '#loading' ).hide('fade',100);
			}, 100);
			if(json.status=="SUCCESS"){
				$( '#gradeInfo' ).hide('blind',500);
				$( '#gradePreview' ).show('blind',500);
				setTimeout(function(){
					$( 'select[name=grade]' ).selectmenu();
				}, 100);
			} else if(json.status=="MAXP"){
				alert('ค่าช่วงคะแนนไม่ถูกต้อง โปรดตรวจสอบ');
			}
		}).DataTable({
			paging: false,
			"columns": [{ data: "studentID","orderable": true },{ data: "firstName","orderable": true },{ data: "lastName","orderable": true },{ data: "score","orderable": false },{ data: "grade","orderable": false}],
			ajax:  {
				url: "dataCenter.php",
				type: 'POST',
				data: function ( d ) {
					d.action = "get";
					d.type = "gradeCal";
					d.tag = "preview";
					d.subjectID = $( "#subject" ).val();
					d.max = JSON.stringify(genGradeRange());
				}
			}
		});
		$( '#editGradeInfo' ).click(function(){
			$( '#gradeInfo' ).show('blind',500);
			$( '#gradePreview' ).hide('blind',500);
		});
		$( '#save' ).button().click(function(){
			$( '#loading' ).show('fade',100);
			$.post('dataCenter.php',{
				action:'set',
				type:'setScore',
				scoreType: 'GRADE',
				subjectID: $( '#subject' ).val(),
				score:JSON.stringify(genGrade()),
				studentID:JSON.stringify(genStudentID())},
				function(data){
					setTimeout(function(){
						$( '#loading' ).hide('fade',100);
						$( '#gradeInfo' ).hide('blind',500);
						$( '#gradePreview' ).hide('blind',500);
					}, 100);
					data = $.parseJSON(data);
					console.log(data);
					alert('บันทึกเกรดเรียบร้อย');
			});
		});
	});
</script>
<div id="contentContainer">
	<div id="formHolder">
	<div><h1>ตัดเกรด</h1></div>
		<div id="subjectInfo">
			<div class="leftCell">วิชา :</div>
			<div>
				<select id="subject">
					<option value="0">กำลังประมวลผล</option>
				</select>
			</div>
			<div class="spacer"></div>
			<div id="normalizeHolder"><input type="checkbox" name="normalization" id="normalization"/><label for="normalization">ทำให้คะแนนอยู่ในช่วง [0-100]</label></div>
			<div class="spacer"></div>
			<div id="searchHolder">
				<input type="submit" id="search" value="ตกลง">
			</div>
		</div>
		<div id="gradeInfo">
			<div class="leftCell">คะแนนเต็ม :</div>
			<div><input type="text" id="maxScore" readonly/></div>
			<div class="spacer"></div>
			<fieldset>
				<legend>ช่วงเกรด</legend>
				<div>ตั้งแต่ <input type="text" name="min[]" data-index="0" value="0" readonly> ไม่ถึง <input type="text" name="max[]" data-index="0"> => <input class="gradeRange" value="0" readonly></div>
				<div>ตั้งแต่ <input type="text" name="min[]" data-index="1"> ไม่ถึง <input type="text" name="max[]" data-index="1"> => <input class="gradeRange" value="1" readonly></div>
				<div>ตั้งแต่ <input type="text" name="min[]" data-index="2"> ไม่ถึง <input type="text" name="max[]" data-index="2"> => <input class="gradeRange" value="1.5" readonly></div>
				<div>ตั้งแต่ <input type="text" name="min[]" data-index="3"> ไม่ถึง <input type="text" name="max[]" data-index="3"> => <input class="gradeRange" value="2" readonly></div>
				<div>ตั้งแต่ <input type="text" name="min[]" data-index="4"> ไม่ถึง <input type="text" name="max[]" data-index="4"> => <input class="gradeRange" value="2.5" readonly></div>
				<div>ตั้งแต่ <input type="text" name="min[]" data-index="5"> ไม่ถึง <input type="text" name="max[]" data-index="5"> => <input class="gradeRange" value="3" readonly></div>
				<div>ตั้งแต่ <input type="text" name="min[]" data-index="6"> ไม่ถึง <input type="text" name="max[]" data-index="6"> => <input class="gradeRange" value="3.5" readonly></div>
				<div>ตั้งแต่ <input type="text" name="min[]" data-index="7"> ไม่ถึง <input type="text" name="max[]" data-index="7" readonly> => <input class="gradeRange" value="4" readonly></div>
			</fieldset>
			<div><input type="submit" id="preview" value="ดูผลการตัดเกรด"/></div>
		</div>
		<div id="gradePreview">
			<button id="editGradeInfo">แก้ไขช่วงคะแนน</button>
			<div class="spacer"></div>
			<fieldset class="studentListContainer">
				<legend style="margin-left:12px;">ผลการตัดเกรด</legend>
				<div>
					<table id="studentList">
						<thead>
							<th>รหัสนักเรียน</th>
							<th>ชื่อ</th>
							<th>สกุล</th>
							<th>คะแนน</th>
							<th>เกรด</th>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</fieldset>
			<button id="save">บันทึกเกรด</button>
		</div>
	</div>
</div>