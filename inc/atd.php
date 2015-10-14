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
	#setting {
		display: none;
	}
	<?php if(!$_SESSION['atdID']){?>
	#restoreAtd {
		display: none;
	}
	<?php }?>
</style>
<script>
	$('#pageName').text('ระบบลงเวลาเรียน');
	var popupWin;
	$(function(){
		$('input:text, input:password').button().css({
			'font' : 'inherit',
			'text-align' : 'left',
			'outline' : 'none',
			'cursor' : 'text'
		});
		$('input:submit').button();
		$( "#subject" )
			.selectmenu()
			.selectmenu( "menuWidget" )
			.addClass( "overflow" );
		$('#startAtd').click(function(){
			var date = new Date;
			$.ajax({
				  type: "POST",
				  url: 'dataCenter.php',
				  data: {
					  'action':'set',
					  'type':'createAtd',
					  'term':$( "input[type='radio']:checked","#term" ).val(),
					  'year':$('#year').val(),
					  'subjectID':$('#subject').val(),
					  'time':date.getHours()+':'+date.getMinutes()+':'+date.getSeconds(),
					  'late':$('#lateTime').val()
				},
				beforeSend: function(){console.log('Creating Atdlist.');},
				dataType: 'json',
				success: function(data){
					console.log('Recieved status');
					if(data.status=="SUCCESS"){
						popupWin = window.open('atd.php', "popupWindow", "width=1024,height=768,scrollbars=no");
						$(popupWin).load(function(){
// 							window.location = window.location;
							$('#restoreAtd').show('blind', 500);
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
		$('#subject').load("dataCenter.php", {action:"get",type:"regInsSubList"},function(data){
			$('#subject').selectmenu('refresh');
			$('#setting').show('blind', 500);
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
		$( "#term" ).buttonset();
		$( "#year" ).selectmenu();
	});
</script>
<div id="atdContainer">
<?php
	$year = getYear();
	$term = getTerm();
?>
	<div id="formHolder">
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
		<div class="leftCell">วิชา :</div> 
		<div>
			<select name="subject" id="subject">
				<option value="0">กำลังประมวลผล</option>
			</select>
		</div>
		<div class="spacer"></div>
		<div id="setting">
			<label for="lateTime">เวลาก่อนสาย</label><input type="text" id="lateTime" name="lateTime" value="15"><label for="lateTime">นาที</label>
			<div class="spacer"></div>
			<div id="lateShow">เวลาสายโดยประมาณ <span id="lateTimeCal"></span> น.</div>
			<div class="spacer"></div>
			<div><input type="submit" id="startAtd" value="เริ่มการลงเวลาใหม่"></div>
			<div class="spacer"></div>
			<div><input type="submit" id="restoreAtd" value="เปิดการลงเวลาครั้งล่าสุด"></div>
		</div>
		<div>
		แก้ปัญหาหน้าต่างไม่ขึ้น <a href="?action=soloveProblem&tag=popupBlock" target="_blank">คลิกที่นี่</a>
		</div>
	</div>
</div>