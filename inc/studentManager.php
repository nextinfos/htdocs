<?php
?>
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
		padding: 2px;
	}
	.spacer {
		height: 5px;
	}
	.leftCell {
		padding-left: 5px !important;
	}
	#gender {
		padding-left: 0px !important;
		padding-right: 0px !important;
	}
	#gender>label {
		width: 48%;
	}
	.ui-selectmenu-button {
		width: 100% !important;
	}
</style>
<script>
	$(function(){
		$('input:text, input:password').button().css({
			'font' : 'inherit',
			'color' : 'inherit',
			'text-align' : 'left',
			'outline' : 'none',
			'cursor' : 'text'
		});
		$('input:submit, input:reset').button();
		$( "#gender" ).buttonset();
		$( '#gradeYear' ).selectmenu();
		$( '#instructorID' ).combobox();
		$( '#instructorID' ).load("dataCenter.php", {action:"get",type:"insList"}, function(){
			$( "#subject" ).selectmenu( "refresh" );
		});
		$( "#studentRegistrar" ).submit(function(event){
			event.preventDefault();
			var studentID = $( '#studentID' ).val(),
			personalID = $( '#personalID' ).val(),
			firstName = $( '#firstName' ).val(),
			lastName = $( '#lastName' ).val(),
			gender = $( "input[type='radio']:checked", '#gender' ).val(),
			cardID = $( '#cardID' ).val(),
			secondCardID = $( '#secondCardID' ).val(),
			gradeYear = $( '#gradeYear' ).val(),
			instructorID = $( '#instructorID' ).val();
			if(studentID!=''&&personalID!=''&&firstName!=''&&lastName!=''&&gender!=''&&gradeYear!=''&&instructorID!=''){
				if(cardID==''||secondCardID==''){
					if(cardID==''){
						while(cardID.length<10){
							cardID = prompt('กรอกรหัส RFID 10 หลักแรก หรือ แตะบัตรที่เครื่อง A\n(เว้นว่างไว้หากยังไม่ต้องการผู้บัตร)');
							if(cardID=='') break;
							if($.isNumeric(cardID)==false) cardID = '';
						}
					}
					if(secondCardID==''&&cardID!=''){
						while(secondCardID.length<8) {
							secondCardID = prompt('กรอกรหัส RFID 8 หลักหลัง หรือ แตะบัตรที่เครื่อง B)');
							if(secondCardID=='') break;
							if($.isNumeric(secondCardID)==false) secondCardID = '';
						}
					}
					if(cardID!=''&&secondCardID!=''){
						alert('ข้อมูลบัตร RFID เป็นดังนี้\nรหัส 10 หลักแรก : '+cardID+'\nรหัส  8 หลักหลัง : '+secondCardID);
					} else {
						cardID = '';
						secondCardID = '';
					}
					$.post("dataCenter.php",{
							action: 'set',
							type: 'addStudent',
							'studentID': studentID,
							'personalID': personalID,
							'firstName': firstName,
							'lastName': lastName,
							'gender': gender,
							'cardID': cardID,
							'secondCardID': secondCardID,
							'gradeYear': gradeYear,
							'instructorID': instructorID},
						function(data){
								console.log(data);
					});
				}
			}
		});
	});
</script>
<div id="contentContainer">
	<h1>เพิ่มข้อมูลนักเรียน</h1>
	<form method="POST" id="studentRegistrar">
		<div id="formHolder">
			<div class="leftCell">เลขประจำตัวนักเรียน :</div>
			<div><input type="text" name="studentID" id="studentID" placeholder="1234" required /></div>
			<div class="spacer"></div>
			<div class="leftCell">เลขประจำตัวประชาชน :</div>
			<div><input type="text" name="personalID" id="personalID" placeholder="8619987654321" pattern="[0-9]{13}" required /></div>
			<div class="spacer"></div>
			<div class="leftCell">ชื่อจริง :</div>
			<div><input type="text" name="firstName" id="firstName" placeholder="สมชาย" required /></div>
			<div class="spacer"></div>
			<div class="leftCell">นามสกุล :</div>
			<div><input type="text" name="lastName" id="lastName" placeholder="หมายปอง" required /></div>
			<div class="spacer"></div>
			<div class="leftCell">เพศ :</div>
			<div id="gender">
				<input type="radio" name="gender" id="genderM" value="F"><label for="genderM">ชาย</label>
				<input type="radio" name="gender" id="genderF" value="F"><label for="genderF">หญิง</label>
			</div>
			<div class="spacer"></div>
			<div class="leftCell">รหัส RFID(10 หลักแรก) :</div>
			<div><input type="text" name="cardD" id="cardID" placeholder="1234567890" pattern="[0-9]{10}" /></div>
			<div class="spacer"></div>
			<div class="leftCell">รหัส RFID(8 หลักหลัง) :</div>
			<div><input type="text" name="secondCardID" id="secondCardID" placeholder="12345678" pattern="[0-9]{8}" /></div>
			<div class="spacer"></div>
			<div class="leftCell">ระดับชั้น :</div>
			<div>
				<select name="gradeYear" id="gradeYear" required>
					<option value="7">มัธยมศึกษาปีที่ 1</option>
					<option value="8">มัธยมศึกษาปีที่ 2</option>
					<option value="9">มัธยมศึกษาปีที่ 3</option>
				</select>
			</div>
			<div class="spacer"></div>
			<div class="leftCell">ครูที่ปรึกษา :</div>
			<div>
				<select name="instructorID" id="instructorID" required>
					<option value="0">ไม่ระบุ</option>
				</select>
			</div>
			<div class="spacer"></div>
			<div><input type="submit" value="บันทึก" /><input type="reset" value="ล้างข้อมูล" /></div>
		</div>
	</form>
	<div style="height: 150px;"></div>
</div>