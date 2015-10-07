<style>
			div.tablelHolder {
				margin: auto;
				width: 300px;
				font-size: 1em;
				text-align: center;
			}
			#scoreInfoConf {
				display: none;
			}
			#scoreInfoText {
				font-size: 1.5em;
			}
</style>
<script>
  $(function() {
    var score = $( "#score" ).spinner({min:0});
    $( "#subject" ).selectmenu({
        change: function(){
        	if($( "#subject" ).val()!=0){
            	$( '#scoreInfoConf' ).show('blind', 500);
        	}
    	}
});
    $( "#scoretype" ).selectmenu();
    $( "#subject" ).load("dataCenter.php", {
        action: "get",
        type: "regInsSubList"
	}, function(){
        $( "#subject" ).selectmenu("refresh");
        if($( "#subject" ).val()!=0){
        	$( '#scoreInfoConf' ).show('blind', 500);
    	} else {
        	$( '#subject' ).selectmenu('disable');
    	}
    });
    $(function() {
        $( "input[type=submit], a, button" )
          .button()
          .click(function( event ) {
          });
      });
  });
 </script>
<div class="tablelHolder">
	<select name="subject" style="width:300px;" id="subject">
  		<option value="0">กำลังประมวลผล</option>
	</select>
</div><br>
<div id="scoreInfoConf">
	<div class="tablelHolder">
		<span id="scoreInfoText">ข้อมูลการลงคะแนน</span>
	</div><br>
	<div class="tablelHolder">
		<select style="width:300px;" id="scoretype">
		  <option value="QUIZ">คะแนนชิ้นงาน</option>
		  <option value="TASK">คะแนนตอบคำถาม</option>
		  <option value="EXAM">คะแนนสอบ</option>
		</select>
	</div><br>
	<div class="tablelHolder">
			<label>คะแนนเต็ม : </label><input name="score" id="score" style="width:50px;">
	</div><br>
	<div class="tablelHolder">
			<input type="submit" value="ตกลง">
	</div>
</div>
<?php
	
?>