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
    $(function() {
        $( "input[type=submit], a, button" )
          .button()
          .click(function( event ) {
          });
      });
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
  });
</script>
	<div class="tablelHolder">
		<select name="subjectID" style="width:300px;" id="subject">
			<option value="0">กำลังประมวลผล</option>
			<?php
// 			echo $subjectData;
			?>
		</select>
	</div><br>
	<div id="scoreInfoConf">
		<div class="tablelHolder">
			แจ้งเตือนเมื่อคะแนนต่ำกว่า : <input type="number" style="width:50px;" value="50" step="5" name="warnPer" id="score">%
		</div><br/>
		<div class="tablelHolder">
			<input type="submit" value="ดูข้อมูล">
		</div><br>
	</div>