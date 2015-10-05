<?php
//	require_once '../config.php';
	$barcodeType= 8;/*8=CODE39;20=CODE128*/
	$barcodeChar = 'STD';
/*
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Card Adder</title>
		<link href="scripts/jquery-ui/jquery-ui.css" rel="stylesheet">
		<script src="scripts/jquery-ui/external/jquery/jquery.js"></script>
 		<script src="scripts/jquery-ui/jquery-ui.js"></script>*/
 		?>
 		<script>
			$(function() {
				$( "#studentID" ).autocomplete(
				{
					minLength: 0,
					 source:function( request, response ) {
						$.ajax({
							url: "dataCenter.php",
							dataType: "json",
							type: 'POST',
							data: {
								  'action':'get',
								  'type':'autoComplete',
								  'source':'studentID',
								  'data':$('#studentID').val()
							},
							success: function( data ) {
								response( data );
							}
						});
					 }
				})
				$('input:text').button();
				$(document).keyup(function(e) {
					var code = e.keyCode
					console.log('keyup['+code+']/'+$("#studentID").is(":focus")+'/'+$("#studentID").val().length);
					if($("#studentID").is(":focus")&&$("#studentID").val().length>=4){
						console.log('Focus CardID1 Fire');
						$('#cardID1').focus();
					}
					if($("#cardID1").is(":focus")&&$("#cardID1").val().length>=10&&code=="13"){
						console.log('Focus CardID2 Fire');
						$('#cardID2').focus();
					}
					if($("#cardID2").is(":focus")&&$("#cardID2").val().length>=8&&code=="13"){
						console.log('Submit Form Fire');
						$('#adder').submit();
					}
				});
				$('#adder').submit(function(){
					$('#Rstatus').text('Sending. . .');
					$.ajax({
						url: "dataCenter.php",
						dataType: "json",
						type: 'POST',
						data: {
							  'action':'set',
							  'type':'cardHolder',
							  'studentID':$('#studentID').val(),
							  'cardID1':$('#cardID1').val(),
							  'cardID2':$('#cardID2').val()
						},
						success: function( data ) {
							if(data.status=='success'){
								$('#Rstatus').text('Success!!');
								$('#studentID').val('');
								$('#cardID1').val('');
								$('#cardID2').val('');
								$('#studentID').focus();
							}
						}
					});
					return false;
				});
			});
		</script>
<?php /*	</head>
	<body>*/?>
		<form id="adder">
			<input type="text" id="studentID" placeholder="รหัสนักเรียน" autofocus><br>
			<input type="text" id="cardID1" placeholder="รหัสบัตร 10 หลักแรก"><br>
			<input type="text" id="cardID2" placeholder="รหัสบัตร 8 หลักหลัง">
		</form>
		<span id="Rstatus"></span>
<?php	/*</body>
</html>*/

	
?>