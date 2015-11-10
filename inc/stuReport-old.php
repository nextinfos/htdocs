<?php
	if($confUserType=='instructor'){
		$studentID = $_REQUEST['studentID'];
		if($studentID==''||!isset($studentID)) header("Location: ?action=insReport");
	} else {
		$studentID = $confUserID;
	}
	$strSQL = sprintf(
			"
			SELECT
				*
			FROM
				`student`
			WHERE
				`studentID` = '%s'
			LIMIT 1
			",
			mysql_real_escape_string($studentID)
			);
	$objQuery = mysql_query($strSQL);
	if($objQuery&&mysql_num_rows($objQuery)){
		$studentInfo = mysql_fetch_array($objQuery);
		$studentPhoto = 'images/students/'.$studentID.'.jpg';
		if(!file_exists($studentPhoto)) $studentPhoto = $noPhoto;
?>
<style>
	#studentInfo {
		width: 300px;
		height: 600px;
	}
	.polaroid {
	  position: relative;
	  width: 220px;
	  left: 50px;
	}
	.polaroid div.picture {
	  border: 10px solid #fff;
	  border-bottom: 45px solid #fff;
	  -webkit-box-shadow: 3px 3px 3px #777;
	     -moz-box-shadow: 3px 3px 3px #777;
	          box-shadow: 3px 3px 3px #777;
	}
	
	.polaroid p {
	  position: absolute;
	  text-align: center;
	  width: 100%;
	  bottom: 0px;
	  margin-bottom: 10px;
	  font: 400 25px/1 'Kaushan Script', cursive;
	  color: #888;
	}
	.picture {
		height: 300px;
		width: 200px;
		background-size: cover !important;
		background: url('<?php echo $studentPhoto;?>') no-repeat center top;
	}
</style>
<div id="studentInfo">
	<div class="polaroid">
		<div class="picture"></div>
		<p><?php echo $studentInfo['firstName'].' '.$studentInfo['lastName'];?></p>
	</div>
	<div>
	ชื่อ : <?php echo $studentInfo['firstName'].' '.$studentInfo['lastName'];?>
	</div>
</div>
<div>
	
</div>
<?php
	} else {
		echo '<script>alert(\'ไม่พบนักเรียน\');window.close();</script>';
	}
?>