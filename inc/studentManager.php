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
	String.prototype.replaceArray = function(find, replace) {
		  var replaceString = this;
		  for(var j = 0; j < this.length; j++){
			  for (var i = 0; i < find.length; i++) {
			    replaceString = replaceString.replace(find[i], replace[i]);
			  }
		  }
		  return replaceString;
		};
	function convertCard(id){
		console.info('Card detect ID : '+id);
		var res = id;
		if($.isNumeric(id)){
			res = id;
		} else {
			var thn = ['ๅ','/','-','ภ','ถ','ุ','ึ','ค','ต','จ'];
			var thc = ['+','๑','๒','๓','๔','ู','฿','๕','๖','๗'];
			var num = ['1','2','3','4','5','6','7','8','9','0'];
			res = res.replaceArray(thn, num);
			res = res.replaceArray(thc, num);
			console.debug('Convert card to number ID : '+res);
		}
		return res;
	}
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
		function sendForm(){
			$('#loading' ).show('fade', '', '100');
			var studentID = $( '#studentID' ).val(),
			personalID = $( '#personalID' ).val(),
			firstName = $( '#firstName' ).val(),
			lastName = $( '#lastName' ).val(),
			gender = $( "input[type='radio']:checked", '#gender' ).val(),
			cardID = $( '#cardID' ).val(),
			secondCardID = $( '#secondCardID' ).val(),
			gradeYear = $( '#gradeYear' ).val(),
			instructorID = $( '#instructorID' ).val();
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
						data = JSON.parse(data);
						if(data.status=='OK'){
							alert('เพิ่มข้อมูลเรียบร้อยแล้ว\nข้อมูลเข้าสู่ระบบคือ\nUSERNAME : '+data.studentID+'\nPASSWORD : '+data.password);
							$( 'input[type=reset]' ).click();
							$( '#studentID' ).focus();
							$( '#RFIDCardAsked').val('false');
						} else if(data.status=='EXIST'){
							alert('ข้อผิดพลาด : มีนักเรียนรหัส '+data.studentID+' อยู่แล้วในระบบ');
						} else {
							console.error('UNKNOW ERROR\n'+data.strSQL);
							alert('ข้อผิดพลาด : ไม่ทราบสาเหตุ กรุณาติดต่อผู้พัฒนา');
						}
						$('#loading' ).hide('fade', '', '1000');
			});
		}
		$( '#RFIDCardAsked').val('false');
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
				console.log('valid data');
				if((cardID==''||secondCardID=='')&&$( '#RFIDCardAsked').val()!='true'){
					console.log('null card&no ask');
					while($( '#RFIDCardAsked').val()!='true'){
						if(cardID==''){
							while(cardID.length<10){
								if(cardID = prompt('กรอกรหัส RFID 10 หลักแรก หรือ แตะบัตรที่เครื่อง A\n(เว้นว่างไว้หากยังไม่ต้องการผู้บัตร)')){
									if(cardID=='') break;
									cardID=convertCard(cardID);
									if($.isNumeric(cardID)==false) cardID = '';
								} else {
									cardID='';
									break;
								}
							}
						}
						if(secondCardID==''){
							while(secondCardID.length<8) {
								if(secondCardID = prompt('กรอกรหัส RFID 8 หลักหลัง หรือ แตะบัตรที่เครื่อง B\n(เว้นว่างไว้หากยังไม่ต้องการผู้บัตร)')){
									if(secondCardID=='') break;
									secondCardID = convertCard(secondCardID);
									if($.isNumeric(secondCardID)==false) secondCardID = '';
								} else {
									secondCardID='';
									break;
								}
							}
						}
						if(cardID!=''||secondCardID!=''){
							if(confirm('ข้อมูลบัตร RFID เป็นดังนี้\nรหัส 10 หลักแรก : '+cardID+'\nรหัส  8 หลักหลัง : '+secondCardID)){
								$( '#cardID' ).val(cardID);
								$( '#secondCardID' ).val(secondCardID);
								$( '#RFIDCardAsked').val('true');
								break;
							} else {
								cardID = '';
								secondCardID = '';
							}
						} else {
							cardID = '';
							secondCardID = '';
							$( '#RFIDCardAsked').val('true');
							sendForm();
							break;
						}
					}
				} else {
					sendForm();
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
			<div><input type="text" name="studentID" id="studentID" placeholder="1234" required autofocus /></div>
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
				<input type="radio" name="gender" id="genderM" value="M"><label for="genderM">ชาย</label>
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
	<div style="height: 150px;">
		<input type="hidden" id="RFIDCardAsked" value="...">
	</div>
</div>