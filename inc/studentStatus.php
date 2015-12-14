<?php
	$studentID = $_REQUEST['studentID'];
	function selected($v1,$v2){
		if($v1==$v2) return ' selected'; else return '';
	}
?>
<style>
	.custom-combobox-input {
	    margin: 0;
	    padding: 4.5px 5px;
	    width: 260px;
	  }
	  #formHolder {
	  	width: 320px;
	  	text-align: center;
	  }
	  #studentStatusHolder {
	  	display: none;
	  }
</style>
<script>
	$(function(){
		$('#student').combobox();
		$('button').button();
		$( '#studentStatus').selectmenu();
		$( '#search' ).click(function(){
				if($('#student').val()!=''){
					$('#studentStatus').load('dataCenter.php',{
						'action': 'get',
						'type': 'studentStatus',
						'studentID': $('#student').val()
					},function(data){
						setTimeout(function(){
							$( '#studentStatus').selectmenu('refresh');
						}, 100);
						$( '#studentStatusHolder').show('blind', 500)
					});
				} else {
					alert('กรุณาเลือกนักเรียน');
				}
			});
		$('#save').click(function(){
				$.post('dataCenter.php',{
						'action': 'set',
						'type': 'studentStatus',
						'studentID': $('#student').val(),
						'status': $('#studentStatus').val()
					},function(data){
						if(data=='SUCCESS'){
							$( '#studentStatusHolder').hide('blind', 100);
							$('.custom-combobox-input').val('');
							$('#student').val('');
							alert('บันทึกสำเร็จ');
						} else {
							alert('ไม่สามารถบันทึกได้');
							console.log(data);
						}
					});
		});
<?php
	if($studentID){
?>
		$('#search').click();
<?php
	} 
?>
	});
</script>
<div id="formHolder">
	<h1>อัพเดทสถานะนักเรียน</h1>
	<select name="student" id="student">
		<option value="0"<?php $studentID?'':' selected';?>>กรุณากรอกชื่อหรือรหัสนักเรียน</option>
		<?php
			$strSQL = 'SELECT * FROM `student`';
			$objQuery = mysql_query($strSQL);
			if(mysql_num_rows($objQuery)>=1){
				while($row = mysql_fetch_array($objQuery)){
					$data.='<option value="'.$row['studentID'].'" '.selected($row['studentID'], $studentID).'>'.$row['studentID'].' '.$row['firstName'].' '.$row['lastName'].'</option>';
				}
			} else {
				$data = '<option>ไม่พบนักเรียน</option>';
			}
			echo $data;
		?>
	</select>
	<div class="spacer"></div>
	<button id="search">ค้นหาสถานะ</button>
	<div class="spacer"></div>
	<div id="studentStatusHolder">
		<select id="studentStatus">
		</select></br>
		<button id="save">บันทึก</button>
	</div>
</div>