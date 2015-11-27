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
  $(function() {
	  $('input:text, input:password').button().css({
			'font' : 'inherit',
			'color' : 'inherit',
			'text-align' : 'left',
			'outline' : 'none',
			'cursor' : 'text'
		});
		$( "#gender" ).buttonset();
		$('input:submit, input:reset').button();
		$('#instructorRegistrar').submit(function(){
			$('#loading' ).show('fade', '', '100');
			event.preventDefault();
			var personalID = $('#personalID').val();
			var firstName = $('#firstName').val();
			var lastName = $('#lastName').val();
			if(personalID&&firstName&&lastName){
				$.post('dataCenter.php',{
					action: 'set',
					type: 'addInstructor',
					'instructorID': personalID,
					'firstName': firstName,
					'lastName': lastName,
				},function(data){
					data = JSON.parse(data);
					if(data.status=='OK'){
						alert('เพิ่มข้อมูลเรียบร้อยแล้ว\nข้อมูลเข้าสู่ระบบคือ\nUSERNAME : '+data.instructorID+'\nPASSWORD : '+data.password);
						$( 'input[type=reset]' ).click();
						$( '#personalID' ).focus();
					} else if(data.status=='EXIST'){
						alert('ข้อผิดพลาด : มีอาจารย์รหัส '+data.instructorID+' อยู่แล้วในระบบ');
					} else {
						console.error('UNKNOW ERROR\n'+data.strSQL);
						alert('ข้อผิดพลาด : ไม่ทราบสาเหตุ กรุณาติดต่อผู้พัฒนา');
					}
					$('#loading' ).hide('fade', '', '1000');
				});
			}
		});
  });
  </script>
<div id="contentContainer">
	<h1>เพิ่มข้อมูลคุณครู</h1>
	<form id="instructorRegistrar">
		<div id="formHolder">
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
			<div><input type="submit" value="บันทึก" /><input type="reset" value="ล้างข้อมูล" /></div>
		</div>
	</form>
	<div style="height: 150px;">
		<input type="hidden" id="RFIDCardAsked" value="...">
	</div>
</div>