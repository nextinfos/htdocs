<?php
	function gradeCal($min,$max,$point,$score){
		$score = round($score,$point,PHP_ROUND_HALF_UP);
		$result = NULL;
		if($score<$max[0])	$result = '0';
		elseif($score<$max[1])	$result = '1';
		elseif($score<$max[2])	$result = '1.5';
		elseif($score<$max[3])	$result = '2';
		elseif($score<$max[4])	$result = '2.5';
		elseif($score<$max[5])	$result = '3';
		elseif($score<$max[6])	$result = '3.5';
		elseif($score<=$max[7])	$result = '4';
		else $result = 'N/A';
		return $result!=NULL?$result.'/'.$score:false;
	}
	$score = $_REQUEST['score'];
	$maxScore = $_REQUEST['maxScore'];
	$min = $_REQUEST['min'];
	$max = $_REQUEST['max'];
	$point = $_REQUEST['point'];
	$submit = $_REQUEST['submit'];
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>ระบบจัดการเวลาเรียนและคะแนน</title>
		<meta name="viewport" content="user-scalable=no, width=device-width">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<link rel="shortcut icon" href="favicon.ico" />
		<link rel="icon" href="favicon.ico" />
		<link rel="icon" type="image/png" href="images/favicon.png" />
		<link rel="apple-touch-icon" href="images/icon.png">
		<link href="scripts/jquery-ui/jquery-ui.css" rel="stylesheet">
		<link href="scripts/jquery-ui/dataTables/jquery.dataTables.css" rel="stylesheet">
		<link href="scripts/NinjaRadial.css" rel="stylesheet" type="text/css" />
		<script src="scripts/jquery-ui/external/jquery/jquery.js"></script>
 		<script src="scripts/jquery-ui/jquery-ui.js"></script>
 		<script src="scripts/jquery-ui/dataTables/jquery.dataTables.js"></script>
		<style>
			input[name=min\[\]], input[name=max\[\]] {
				width: 25px;
			}
		</style>
		<script>
			function valueadd(ok){
				var value=parseFloat(ok)+1;
				return value;
			}
			$(function(){
				$( 'input[name="max[]"]' ).eq(0).val('<?php echo $max[0];?>);
				$( 'input[name="max[]"]' ).eq(1).val('55');
				$( 'input[name="max[]"]' ).eq(2).val('60');
				$( 'input[name="max[]"]' ).eq(3).val('65');
				$( 'input[name="max[]"]' ).eq(4).val('70');
				$( 'input[name="max[]"]' ).eq(5).val('75');
				$( 'input[name="max[]"]' ).eq(6).val('80');
				$( 'input[name="max[]"]' ).eq(7).val('100');
				$( 'input[name="min[]"]' ).eq(0).val('0');
				$( 'input[name="min[]"]' ).eq(1).val($( 'input[name="max[]"]' ).eq(0).val());
				$( 'input[name="min[]"]' ).eq(2).val($( 'input[name="max[]"]' ).eq(1).val());
				$( 'input[name="min[]"]' ).eq(3).val($( 'input[name="max[]"]' ).eq(2).val());
				$( 'input[name="min[]"]' ).eq(4).val($( 'input[name="max[]"]' ).eq(3).val());
				$( 'input[name="min[]"]' ).eq(5).val($( 'input[name="max[]"]' ).eq(4).val());
				$( 'input[name="min[]"]' ).eq(6).val($( 'input[name="max[]"]' ).eq(5).val());
				$( 'input[name="min[]"]' ).eq(7).val($( 'input[name="max[]"]' ).eq(6).val());
				$( 'input[name=point]' ).val('<?php echo $point?$point:'0';?>');
			});
		</script>
	</head>
	<body>
		<form method="POST">
			Max Score : <input type="text" name="maxScore" value="100"/><br/>
			Score : <input type="text" name="score" value="<?php echo $score;?>"/><br/>
			Point : <input type="number" name="point"/><br/>
			Grade Range<br/>
			ตั้งแต่ <input type="text" name="min[]"> ไม่ถึง <input type="text" name="max[]"> => 0<br/>
			ตั้งแต่ <input type="text" name="min[]"> ไม่ถึง <input type="text" name="max[]"> => 1<br/>
			ตั้งแต่ <input type="text" name="min[]"> ไม่ถึง <input type="text" name="max[]"> => 1.5<br/>
			ตั้งแต่ <input type="text" name="min[]"> ไม่ถึง <input type="text" name="max[]"> => 2<br/>
			ตั้งแต่ <input type="text" name="min[]"> ไม่ถึง <input type="text" name="max[]"> => 2.5<br/>
			ตั้งแต่ <input type="text" name="min[]"> ไม่ถึง <input type="text" name="max[]"> => 3<br/>
			ตั้งแต่ <input type="text" name="min[]"> ไม่ถึง <input type="text" name="max[]"> => 3.5<br/>
			ตั้งแต่ <input type="text" name="min[]"> ไม่ถึง <input type="text" name="max[]"> => 4<br/>
			<input type="submit" name="submit"/>
		</form>
		<?php
		if($submit){
			echo 'Grade Cal = '.gradeCal($min,$max,$point,$score);
		} 
		?>
	</body>
</html>