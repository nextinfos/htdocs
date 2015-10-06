<style>
			#tableHolder {
			margin: auto;
			width: 300px;
			font-size:25px;
			}
			#detailHolder {
			margin: auto;
			width: 300px;
			font-size:20px;
			}
</style>
<script>
  $(function() {
    var score = $( "#score" ).spinner({min:0});
    $( "#subject" ).selectmenu();
    $( "#scoretype" ).selectmenu();
    $( "#subject" ).load("dataCenter.php", {
        action: "get",
        type: "regInsSubList"
	}, function(){
        $( "#subject" ).selectmenu("refresh");
    });
  });
 </script>
<div id="detailHolder"><select name="subject" style="width:300px;" id="subject">
  <option value="0">กำลังประมวลผล</option>
</select></div><br>
<div id="tableHolder" p align=center>ข้อมูลการลงคะแนน</div><br>
</style><div id="detailHolder">
<select style="width:300px;" id="scoretype">
  <option value="QUIZ">คะแนนชิ้นงาน</option>
  <option value="TASK">คะแนนตอบคำถาม</option>
  <option value="EXAM">คะแนนสอบ</option>
</select></div><br>
<div id="detailHolder"><center><label>คะแนนเต็ม : </label><input name="score" id="score" style="width:50px;"></center></div><br>
<?php
	
?>