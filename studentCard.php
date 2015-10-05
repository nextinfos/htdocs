<?php
	require_once 'config.php';
	$barcodeType= 8;/*8=CODE39;20=CODE128*/
	$barcodeChar = 'STD';
	if($_GET['studentID']||$studentID){
		if(!$studentID) $studentID = $_GET['studentID'];
		$studentPhoto = 'images/students/'.$studentID.'.jpg';
		if(!file_exists($studentPhoto)) $studentPhoto = $noPhoto;
	} else {
		$studentID = '0000000000';
		$studentFName = 'สมชาย';
		$studentLName = 'หมายปอง';
		$studentPhoto = $noPhoto;
	}
?>
<style>
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
		<script>
			$(function() {
				$('.studentCardContainer').addClass("ui-corner-all");
			});
			</script>
<div class="studentCardContainer">
	<div style="position:relative;top:10px;left:20px;height:80px;width:70px;">
		<img src="images/logo.png" style="max-height:80px;">
	</div>
	<div style="position:relative;top:-80px;left:90px;height:20px;width:210px;">
		<h4>โรงเรียนชุมชนบ้านถ้ำสิงห์</h4>
	</div>
	<div style="position:relative;top:-100px;left:90px;height:20px;width:210px;text-align:center;">
		<h5>บัตรประจำตัวนักเรียน</h5>
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
		<div id="studentCode" style="background: url('http://www.barcode-generator.org/zint/api.php?bc_number=<?php echo $barcodeType?>&bc_data=<?php echo $barcodeChar.$studentID;?>') no-repeat center top;"></div>
	</div>
</div>