<?php require_once 'config.php';
	$barcodeType= 8;/*8=CODE39;20=CODE128*/
	$barcodeChar = 'INS';
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>ระบบออกบัตรอาจารย์</title>
		<link href="scripts/jquery-ui/jquery-ui.css" rel="stylesheet">
		<script src="scripts/jquery-ui/external/jquery/jquery.js"></script>
 		<script src="scripts/jquery-ui/jquery-ui.js"></script>
		<style>
			body {
/* 				width: 1024px; */
/* 				height: 768px; */
				margin: 0px;
				font-size: 12pt;
			}
			.studentCardContainer {
				border: 2px solid black;
				height: 54mm !important;
				width: 85.5mm !important;
				background: white url('images/cardBackground.png') no-repeat;
				background-size: 85.5mm;
				background-position: 0px -120px;
			}
			h4,h5 {
/* 				-webkit-stroke-width: 5.3px; */
/* 				-webkit-stroke-color: #FFFFFF; */
/* 				-webkit-fill-color: #FFFFFF; */
				text-shadow: 0 0 5px #FFF,0 0 10px #FFF,0 0 15px #FFF,0 0 20px #FFF,0 0 25px #FFF,0 0 30px #FFF,0 0 35px #FFF,0 0 40px #FFF,0 0 45px #FFF,0 0 50px #FFF;
			}
			.cardDetailContainer {
				font-size: 11pt;
			}
			 .cardDetail {
			 	width: 100px;
			 	display: inline-block;
			 	text-align: left;
			 }
			 #studentPhoto {
			 	height: 100px;
			 	width: 75px;
			 	background-size: cover !important;
			 }
			 #studentCode {
			 	width: 200px;
			 	height: 30px;
				background-size: cover !important;
			 }
		</style>
		<style type="text/css" media="print">
			@page { 
			    size: auto;   /* auto is the initial value */ 
			    /* this affects the margin in the printer settings */ 
			    margin: 5mm 5mm 5mm 5mm;  
			} 
			body {
			    PAGE-BREAK-BEFORE: always;
			    width:100%;
			    height:100%;
				-moz-transform: scale(96);
				zoom: 96%; /*Fixed border is 93%-95%*/
				margin: 0px;
			}
			footer {page-break-after: always;}
			#tf_hui_container {
				display:none;
			}
		</style>
	</head>
	<body>
		<script>
			$(function() {
				$('.studentCardContainer').addClass("ui-corner-all");
			});
		</script>
		<table>
		<?php
			$strSQL = "SELECT * FROM instructors";
			$objQuery = mysql_query($strSQL);
			if($objQuery){
			$c=1;
			while($row = mysql_fetch_array($objQuery)){
				$studentID=$row['instructorID'];
				$studentFName=$row['firstname'];
				$studentLName=$row['lastname'];
				$studentPhoto = 'images/instructors/'.$studentID.'.jpg';
				if(!file_exists($studentPhoto)) $studentPhoto = $noPhoto;
				if($c%2==1) echo '<tr>';?>
		<td>
			<div class="studentCardContainer">
				<div style="position:relative;top:10px;left:20px;height:80px;width:70px;">
					<img src="images/logo.png" style="max-height:80px;">
				</div>
				<div style="position:relative;top:-80px;left:90px;height:20px;width:210px;">
					<h4>โรงเรียนชุมชนบ้านถ้ำสิงห์</h4>
				</div>
				<div style="position:relative;top:-100px;left:90px;height:20px;width:210px;text-align:center;">
					<h5>บัตรประจำตัวอาจารย์</h5>
				</div>
				<div style="position:relative;top:-65px;left:10px;height:60px;width:200px;text-align:right;" class="cardDetailContainer">
					เลขประจำตัว : <div id="studentID" class="cardDetail"><?php echo $studentID;?></div><br>
					ชื่อ : <div id="studentFName" class="cardDetail"><?php echo $studentFName;?></div><br>
					สกุล : <div id="studentLName" class="cardDetail"><?php echo $studentLName;?></div>
				</div>
				<div style="position:relative;top:-140px;left:225px;height:100px;width:75px;text-align:center;border:1px solid #CCCCCC;">
					<div id="studentPhoto" style="background: url('<?php echo $studentPhoto;?>') no-repeat center top;"></div>
				</div>
				<div style="position:relative;top:-160px;left:10px;height:20px;width:200px;">
					<div id="studentCode" style="background: url('http://www.barcode-generator.org/zint/api.php?bc_number=<?php echo $barcodeType?>&bc_data=<?php echo $barcodeChar.$studentID;?>&bc_size=l') no-repeat center top;"></div>
				</div>
			</div>
		</td>
		<?php if($c%2==0) echo '</tr>'; $c++;
			if($c>10){echo '<footer>';$c=1;}
		?>
		<?php }
			}?>
		</table>
	</body>
</html>