<style>	
	#subject {
		width: 300px;
	}
	#atdContainer {
		text-align: center;
	}
	#lateTime {
		width: 20px;
		text-align: center !important;
	}
</style>
<script>
	$('#pageName').text('ระบบลงเวลาเรียน');
	var popupWin;
	$(function(){
		$('input:text, input:password').button().css({
			'font' : 'inherit',
			'color' : 'inherit',
			'text-align' : 'left',
			'outline' : 'none',
			'cursor' : 'text'
		});
		$('input:submit').button();
		$( "#subject" )
			.selectmenu()
			.selectmenu( "menuWidget" )
			.addClass( "overflow" );
		$( "#setting" ).hide();
		$('#startAtd').click(function(){
			var date = new Date;
			$.ajax({
				  type: "POST",
				  url: 'dataCenter.php',
				  data: {
					  'action':'set',
					  'type':'createAtd',
					  'regSubjectID':$('#subject').val(),
					  'time':date.getHours()+':'+date.getMinutes()+':'+date.getSeconds(),
					  'late':$('#lateTime').val()
				},
				beforeSend: function(){console.log('Creating Atdlist.');},
				dataType: 'json',
				success: function(data){
					console.log('Recieved status');
					if(data.status=="success"){
						popupWin = window.open('atd.php', "popupWindow", "width=1024,height=768,scrollbars=no");
						$(popupWin).load(function(){
							window.location = window.location;
						});
						setTimeout(function(){window.location = window.location;},5000);
					} else {
						console.error(data);
						alert('กรุณาติดต่อผู้ดูแลระบบ');
					}
				}
			});
		});
		$('#restoreAtd').click(function(){
			window.open('atd.php', "popupWindow", "width=1024,height=768,scrollbars=no");
		});
		setInterval(function(){
			var date = new Date();
			var hour = date.getHours()
			var min = (parseInt(date.getMinutes())+parseInt($('#lateTime').val()));
			var sec = date.getSeconds();
			while(min>=60){
				min-=60;
				hour = parseInt(hour);
				hour++;
			}
			if(min<=9) min = '0'+min;
			if(sec<=9) sec = '0'+sec;
			var dtcal = hour+':'+min+':'+sec;
			$('#lateTimeCal').text(dtcal);
		}, 1000);
		$(popupWin).load(function(){
			window.location = window.location;
		});
	});
	
</script>
<div id="atdContainer">
<?php
	$instructorID = $_SESSION['userID'];
	$strSQL = 'SELECT * FROM subject s, registersubject r WHERE s.subjectID = r.subjectID AND r.instructorID = "'.$instructorID.'"';
	$objQuery = mysql_query($strSQL);
	if($objQuery&&mysql_num_rows($objQuery)>=1){
		$data = '<select id="subject">';
		while($row = mysql_fetch_array($objQuery)){
			$data.= '<option value="'.$row['subjectID'].'-'.$row['term'].'-'.$row['year'].'">['.$row['subjectID'].'] '.$row['name'].'</option>';
		}
		echo $data.'</select><script>$(function(){$("#setting").show();});</script>';
	} else {
		echo '<select disabled id="subject"><option>ไม่พบข้อมูล</option></select>';
	}
?>
	<div id="setting">
		<label for="lateTime">เวลาก่อนสาย</label><input type="text" id="lateTime" name="lateTime" value="15"><label for="lateTime">นาที</label>
		<div id="lateShow">เวลาสายโดยประมาณ <span id="lateTimeCal"></span> น.</div>
		<div><input type="submit" id="startAtd" value="เริ่มการลงเวลาใหม่"></div>
		<?php if($_SESSION['atdID']) echo '<div><input type="submit" id="restoreAtd" value="เปิดการลงเวลาครั้งล่าสุด"></div>';?>
	</div><br>
	<div>
	แก้ปัญหาหน้าต่างไม่ขึ้น <a href="?action=soloveProblem&tag=popupBlock" target="_blank">คลิกที่นี่</a>
	</div>
</div>