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
		$strSQL = sprintf(
				"
			SELECT
				studentID
			FROM
				`register-student`
			WHERE
				`studentID` = '%s' AND
				`registerID` =
				(
					SELECT
						`registerID`
					FROM
						`registerinfo`
					WHERE
						`term` = '%s' AND
						`year` = '%s'
				)
			",
				mysql_real_escape_string($studentID),
				mysql_real_escape_string(getTerm()),
				mysql_real_escape_string(getYear())
		);
		$objQuery = mysql_query($strSQL);
		$registerCount = $objQuery?mysql_num_rows($objQuery):'0';
		$strSQL = sprintf(
				"
			SELECT
				*
			FROM
				`instructor`
			WHERE
				`instructorID` = '%s'
			LIMIT 1
			",
				mysql_real_escape_string($studentInfo['instructorID'])
		);
		$objQuery = mysql_query($strSQL);
		if($objQuery){
			$row = mysql_fetch_array($objQuery);
			$insName = $row['firstName'].' '.$row['lastName'];
		}
?>
<style>
	@import url(http://fonts.googleapis.com/css?family=Roboto:400,300);
	#studentInfo {
/* 		width: 300px;  */
 	  height: 70vh; 
/* 	  background-color: #f44336; */
	  -webkit-transition: background-color 300ms;
	          transition: background-color 300ms;
	  font-family: 'Roboto', sans-serif;
	}
	@media screen and (min-width: 30em) {
	  #studentInfo {
	    display: -webkit-box;
	    display: -webkit-flex;
	    display: -ms-flexbox;
	    display: flex;
	    -webkit-box-align: center;
	    -webkit-align-items: center;
	        -ms-flex-align: center;
	            align-items: center;
	    -webkit-box-pack: center;
	    -webkit-justify-content: center;
	        -ms-flex-pack: center;
	            justify-content: center;
	  }
	}
/* 	#studentInfo.show-menu { */
/* 	  background-color: #00ACC1; */
/* 	} */
	.card {
/* 	  background-color: #FFF; */
	background-color: rgba(255,255,255,0.7);
	  position: relative;
	  overflow: hidden;
	  width: 100%;
	  height: 100%;
	  min-height: 405px;
	  -webkit-transition: all 300ms;
	          transition: all 300ms;
	  -webkit-user-select: none;
	     -moz-user-select: none;
	      -ms-user-select: none;
	          user-select: none;
	  box-shadow: 0px 5px 43px rgba(0, 0, 0, 0.18);
	  -webkit-animation-timing-function: cubic-bezier(0.23, 1, 0.32, 1);
	          animation-timing-function: cubic-bezier(0.23, 1, 0.32, 1);
	}
	@media screen and (min-width: 30em) {
	  .card {
	    width: 290px;
	    height: 405px;
	    border-radius: 3px;
	  }
	  .card:hover {
	    cursor: pointer;
	  }
	}
	.card-face {
	  width: 100%;
	  height: 100%;
	  position: absolute;
	  border-radius: 3px;
	  display: -webkit-box;
	  display: -webkit-flex;
	  display: -ms-flexbox;
	  display: flex;
	  -webkit-box-align: center;
	  -webkit-align-items: center;
	      -ms-flex-align: center;
	          align-items: center;
	  -webkit-box-pack: start;
	  -webkit-justify-content: flex-start;
	      -ms-flex-pack: start;
	          justify-content: flex-start;
	  -webkit-transition: all 400ms cubic-bezier(0.215, 0.61, 0.355, 1);
	          transition: all 400ms cubic-bezier(0.215, 0.61, 0.355, 1);
	  -webkit-box-orient: vertical;
	  -webkit-box-direction: normal;
	  -webkit-flex-direction: column;
	      -ms-flex-direction: column;
	          flex-direction: column;
	}
	.card-face__bullet {
	  background-color: #f44336;
	  color: #FFF;
	  display: block;
	  padding: 4px 0;
	  border-radius: 50%;
	  width: 50px;
	  height: 23px;
	  box-sizing: border-box;
	  line-height: 1.2;
	  text-align: center;
	  font-size: 12px;
	  position: absolute;
	  top: 10px;
	  right: 0;
	  box-shadow: 0px 1px 5px rgba(0, 0, 0, 0.27);
	  -webkit-animation: bullet 500ms;
	          animation: bullet 500ms;
	  -webkit-animation-fill-mode: both;
	          animation-fill-mode: both;
	  -webkit-animation-delay: 1.5s;
	          animation-delay: 1.5s;
	  -webkit-animation-timing-function: cubic-bezier(0.23, 1, 0.32, 1);
	          animation-timing-function: cubic-bezier(0.23, 1, 0.32, 1);
	  font-weight: bold;
	}
	.card-face.face-1 {
	  -webkit-transform: translateX(0);
	      -ms-transform: translateX(0);
	          transform: translateX(0);
	}
	.show-menu .card-face.face-1 {
	  -webkit-transform: translateX(-100%);
	      -ms-transform: translateX(-100%);
	          transform: translateX(-100%);
	}
	.card-face.face-2 {
	  -webkit-box-pack: center;
	  -webkit-justify-content: center;
	      -ms-flex-pack: center;
	          justify-content: center;
	  -webkit-transform: translateX(100%);
	      -ms-transform: translateX(100%);
	          transform: translateX(100%);
	}
	.show-menu .card-face.face-2 {
	  -webkit-transform: translateX(0);
	      -ms-transform: translateX(0);
	          transform: translateX(0);
	}
	.card-face__menu-button {
	  position: absolute;
	  top: 10px;
	  right: 5px;
	  background: transparent;
	  border: none;
	  outline: none;
	  padding: 5px 15px;
	  -webkit-transform: translateX(150%);
	      -ms-transform: translateX(150%);
	          transform: translateX(150%);
	  -webkit-animation: menu 2s;
	          animation: menu 2s;
	  -webkit-animation-fill-mode: both;
	          animation-fill-mode: both;
	  -webkit-animation-timing-function: cubic-bezier(0.23, 1, 0.32, 1);
	          animation-timing-function: cubic-bezier(0.23, 1, 0.32, 1);
	}
	.card-face__menu-button img {
	  pointer-events: none;
	}
	.card-face__back-button {
	  position: absolute;
	  top: 10px;
	  left: 0px;
	  background: transparent;
	  border: none;
	  outline: none;
	  padding: 5px 15px;
	}
	.card-face__back-button img {
	  pointer-events: none;
	}
	.card-face__settings-button {
	  position: absolute;
	  bottom: 0px;
	  right: 0px;
	  background: transparent;
	  border: none;
	  outline: none;
	  padding: 10px;
	}
	.card-face__avatar {
	  display: block;
	  width: 110px;
	  height: 110px;
	  position: relative;
	  margin-top: 40px;
	  margin-bottom: 40px;
	  -webkit-transform: scale(1.2, 1.2);
	      -ms-transform: scale(1.2, 1.2);
	          transform: scale(1.2, 1.2);
	  opacity: 0;
	  -webkit-animation: avatar 1.5s;
	          animation: avatar 1.5s;
	  -webkit-animation-delay: 200ms;
	          animation-delay: 200ms;
	  -webkit-animation-fill-mode: both;
	          animation-fill-mode: both;
	  -webkit-animation-timing-function: cubic-bezier(0.23, 1, 0.32, 1);
	          animation-timing-function: cubic-bezier(0.23, 1, 0.32, 1);
	}
	.card-face__avatar img {
	  border-radius: 50%;
	}
	.card-face__name {
	  font-size: 24px;
	  font-weight: 400;
	  margin-top: 0;
	  margin-bottom: 0;
	  white-space: nowrap;
	  overflow: hidden;
	  text-overflow: ellipsis;
	  box-sizing: border-box;
	  padding: 0 20px;
	  text-align: center;
	  width: 100%;
	  color: #263238;
	  -webkit-animation: fadedown 1s;
	          animation: fadedown 1s;
	  -webkit-animation-fill-mode: both;
	          animation-fill-mode: both;
	  -webkit-animation-timing-function: cubic-bezier(0.23, 1, 0.32, 1);
	          animation-timing-function: cubic-bezier(0.23, 1, 0.32, 1);
	}
	.card-face__title {
	  font-size: 18px;
	  color: #90a4ae;
	  white-space: nowrap;
	  display: block;
	  overflow: hidden;
	  text-overflow: ellipsis;
	  width: 100%;
	  padding: 0 20px;
	  text-align: center;
	  box-sizing: border-box;
	  font-weight: 300;
	  -webkit-animation: fadedown 1s;
	          animation: fadedown 1s;
	  -webkit-animation-fill-mode: both;
	          animation-fill-mode: both;
	  -webkit-animation-delay: 100ms;
	          animation-delay: 100ms;
	  -webkit-animation-timing-function: cubic-bezier(0.23, 1, 0.32, 1);
	          animation-timing-function: cubic-bezier(0.23, 1, 0.32, 1);
	}
	.card-face__title:after {
	  content: "";
	  background-color: #E3EBEE;
	  width: 70px;
	  height: 1px;
	  margin: 20px auto 0 auto;
	  display: block;
	}
	.card-face-footer {
	  left: 0;
	  right: 0;
	  position: absolute;
	  text-align: center;
	  bottom: 20px;
	}
	.card-face__social {
	  display: inline-block;
	  margin: 0 7px;
	  width: 36px;
	  height: 36px;
	  -webkit-animation: socials 1.5s;
	          animation: socials 1.5s;
	  -webkit-animation-fill-mode: both;
	          animation-fill-mode: both;
	  -webkit-animation-timing-function: cubic-bezier(0.23, 1, 0.32, 1);
	          animation-timing-function: cubic-bezier(0.23, 1, 0.32, 1);
	}
	.card-face__social:nth-child(1) {
	  -webkit-animation-delay: 200ms;
	          animation-delay: 200ms;
	}
	.card-face__social:nth-child(2) {
	  -webkit-animation-delay: 400ms;
	          animation-delay: 400ms;
	}
	.card-face__social:nth-child(3) {
	  -webkit-animation-delay: 600ms;
	          animation-delay: 600ms;
	}
	.card-face__stats {
	  display: block;
	  margin: 10px 0;
	}
	.show-menu .card-face__stats {
	  -webkit-animation: stats 1s;
	          animation: stats 1s;
	  -webkit-animation-fill-mode: both;
	          animation-fill-mode: both;
	  -webkit-animation-timing-function: cubic-bezier(0.23, 1, 0.32, 1);
	          animation-timing-function: cubic-bezier(0.23, 1, 0.32, 1);
	}
	.card-face__stats:nth-of-type(1) {
	  -webkit-animation-delay: 0;
	          animation-delay: 0;
	}
	.card-face__stats:nth-of-type(2) {
	  -webkit-animation-delay: 100ms;
	          animation-delay: 100ms;
	}
	.card-face__stats:nth-of-type(3) {
	  -webkit-animation-delay: 200ms;
	          animation-delay: 200ms;
	}
	@-webkit-keyframes avatar {
	  from {
	    -webkit-transform: scale(1.2, 1.2);
	            transform: scale(1.2, 1.2);
	    opacity: 0;
	  }
	  to {
	    -webkit-transform: scale(1, 1);
	            transform: scale(1, 1);
	    opacity: 1;
	  }
	}
	@keyframes avatar {
	  from {
	    -webkit-transform: scale(1.2, 1.2);
	            transform: scale(1.2, 1.2);
	    opacity: 0;
	  }
	  to {
	    -webkit-transform: scale(1, 1);
	            transform: scale(1, 1);
	    opacity: 1;
	  }
	}
	@-webkit-keyframes menu {
	  from {
	    -webkit-transform: translateX(150%);
	            transform: translateX(150%);
	  }
	  to {
	    -webkit-transform: translateX(0);
	            transform: translateX(0);
	  }
	}
	@keyframes menu {
	  from {
	    -webkit-transform: translateX(150%);
	            transform: translateX(150%);
	  }
	  to {
	    -webkit-transform: translateX(0);
	            transform: translateX(0);
	  }
	}
	@-webkit-keyframes fadedown {
	  from {
	    -webkit-transform: translateY(-50%);
	            transform: translateY(-50%);
	    opacity: 0;
	  }
	  to {
	    -webkit-transform: translateY(0);
	            transform: translateY(0);
	    opacity: 1;
	  }
	}
	@keyframes fadedown {
	  from {
	    -webkit-transform: translateY(-50%);
	            transform: translateY(-50%);
	    opacity: 0;
	  }
	  to {
	    -webkit-transform: translateY(0);
	            transform: translateY(0);
	    opacity: 1;
	  }
	}
	@-webkit-keyframes socials {
	  from {
	    -webkit-transform: translateY(300%);
	            transform: translateY(300%);
	  }
	  to {
	    -webkit-transform: translateY(0%);
	            transform: translateY(0%);
	  }
	}
	@keyframes socials {
	  from {
	    -webkit-transform: translateY(300%);
	            transform: translateY(300%);
	  }
	  to {
	    -webkit-transform: translateY(0%);
	            transform: translateY(0%);
	  }
	}
	@-webkit-keyframes stats {
	  from {
	    -webkit-transform: translateX(300%);
	            transform: translateX(300%);
	  }
	  to {
	    -webkit-transform: translateX(0%);
	            transform: translateX(0%);
	  }
	}
	@keyframes stats {
	  from {
	    -webkit-transform: translateX(300%);
	            transform: translateX(300%);
	  }
	  to {
	    -webkit-transform: translateX(0%);
	            transform: translateX(0%);
	  }
	}
	@-webkit-keyframes bullet {
	  0%,
	  20%,
	  40%,
	  60%,
	  80%,
	  100% {
	    -webkit-transition-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
	            transition-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
	  }
	  0% {
	    opacity: 0;
	    -webkit-transform: scale3d(0.3, 0.3, 0.3);
	            transform: scale3d(0.3, 0.3, 0.3);
	  }
	  20% {
	    -webkit-transform: scale3d(1.1, 1.1, 1.1);
	            transform: scale3d(1.1, 1.1, 1.1);
	  }
	  40% {
	    -webkit-transform: scale3d(0.9, 0.9, 0.9);
	            transform: scale3d(0.9, 0.9, 0.9);
	  }
	  60% {
	    opacity: 1;
	    -webkit-transform: scale3d(1.03, 1.03, 1.03);
	            transform: scale3d(1.03, 1.03, 1.03);
	  }
	  80% {
	    -webkit-transform: scale3d(0.97, 0.97, 0.97);
	            transform: scale3d(0.97, 0.97, 0.97);
	  }
	  100% {
	    opacity: 1;
	    -webkit-transform: scale3d(1, 1, 1);
	            transform: scale3d(1, 1, 1);
	  }
	}
	@keyframes bullet {
	  0%,
	  20%,
	  40%,
	  60%,
	  80%,
	  100% {
	    -webkit-transition-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
	            transition-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
	  }
	  0% {
	    opacity: 0;
	    -webkit-transform: scale3d(0.3, 0.3, 0.3);
	            transform: scale3d(0.3, 0.3, 0.3);
	  }
	  20% {
	    -webkit-transform: scale3d(1.1, 1.1, 1.1);
	            transform: scale3d(1.1, 1.1, 1.1);
	  }
	  40% {
	    -webkit-transform: scale3d(0.9, 0.9, 0.9);
	            transform: scale3d(0.9, 0.9, 0.9);
	  }
	  60% {
	    opacity: 1;
	    -webkit-transform: scale3d(1.03, 1.03, 1.03);
	            transform: scale3d(1.03, 1.03, 1.03);
	  }
	  80% {
	    -webkit-transform: scale3d(0.97, 0.97, 0.97);
	            transform: scale3d(0.97, 0.97, 0.97);
	  }
	  100% {
	    opacity: 1;
	    -webkit-transform: scale3d(1, 1, 1);
	            transform: scale3d(1, 1, 1);
	  }
	}
	#attendance, #score {
 		display: none; 
 		text-align: center;
 		padding-bottom: 20px;
	}
	#attendance td, #score td {
		text-align: center;
	}
	h3 {
		margin: 0px;
	}
	fieldset {
		padding: 0px;
		margin: 20px;
		padding-bottom: 40px;
		background: rgba(255,255,255,0.3);
		border: 0px;
	}
	.studentID {
		background-color: #55F;
		width: 50px;
		top: 90px;
		left: 30px;
	}
	.card-face__ins {
		padding-top: 10px;
		color: #999;
	}
</style>
<script>
$(function() {
	var menu_trigger = $("[data-card-menu]");
	var back_trigger = $("[data-card-back]");

	menu_trigger.click(function() {
			$(".card, #studentInfo").toggleClass("show-menu");
	});

	back_trigger.click(function() {
			$(".card, #studentInfo").toggleClass("show-menu");
	});
	$( '#reportAttendance' ).dataTable({
		paging: false,
		"bFilter" : false,
		"bInfo": false,
		"columns":
			[
				{
					data: "subjectID",
					orderable: false
				},
				{
					data: "subjectName",
					orderable: false
				},
				{
					data: "check",
					orderable: false
				},
				{
					data: "late",
					orderable: false
				},
				{
					data: "abs",
					orderable: false
				},
				{
					data: "total",
					orderable: false
				},
				{
					data: "percent",
					orderable: false
				}
			],
			ajax:  {
				url: "dataCenter.php",
				type: 'POST',
				data: function ( d ) {
					d.action = "get";
					d.type = "stuReport";
					d.report = "atd";
					d.studentID = "<?php echo $studentID;?>";
				}
			}
	});
	$( '#reportScore' ).dataTable({
		paging: false,
		"bFilter" : false,
		"bInfo": false,
		"columns":
			[
				{
					data: "subjectID",
					orderable: false
				},
				{
					data: "subjectName",
					orderable: false
				},
<?php if($confUserType=='instructor'){?>
				{
					data: "score",
					orderable: false
				},
<?php }?>
				{
					data: "grade",
					orderable: false
				}
			],
			ajax:  {
				url: "dataCenter.php",
				type: 'POST',
				data: function ( d ) {
					d.action = "get";
					d.type = "stuReport";
					d.report = "score";
					d.studentID = "<?php echo $studentID;?>";
				}
			}
	});
	$('fieldset').addClass("ui-corner-all");
});
function showAttendance(){
	$( '#attendance' ).toggle('blind',1000);
	if($( '#attendance' ).css('display')!='none'){
		$('html,body,.mainContainer').animate({
			scrollTop: $( '#attendance' ).offset().top
		}, 1000, 'swing');
	}
}
function showScore(){
	$( '#score' ).toggle('blind',1000);
	if($( '#score' ).css('display')!='none'){
		$('html,body,.mainContainer').animate({
			scrollTop: $( '#score' ).offset().top
		}, 1000, 'swing');
	}
}
</script>
<div id="studentInfo">
	<div class="card">
		<!-- Face 2 -->
		<div class="card-face face-2">
			<!-- Back trigger -->
			<button data-card-back="data-card-back" class="card-face__back-button">
				<img src="http://imgh.us/arrow_1.svg" width="19" height="19" draggable="false"/>
			</button>
			<img src="images/register.png" width="100" height="100" draggable="false" class="card-face__stats"/>
			<img src="images/status.png" width="100" height="100" draggable="false" class="card-face__stats"/>
			<img src="images/grade.png" width="100" height="100" draggable="false" class="card-face__stats"/>
			<!-- Settings Button -->
			<img src="http://imgh.us/cog.svg" width="17" height="17" draggable="false" class="card-face__settings-button"/>
		</div>
		<!-- Face 1 -->
		<div class="card-face face-1">
			<!-- Menu trigger -->
			<button data-card-menu="data-card-menu" class="card-face__menu-button">
				<img src="http://imgh.us/dots_1.svg" width="5" height="23" draggable="false"/>
			</button>
			<!-- Avatar -->
			<div class="card-face__avatar">
				<!-- Bullet notification -->
				<span class="card-face__bullet studentID"><?php echo $studentID;?></span>
				<!-- User avatar -->
				<img src="<?php echo $studentPhoto;?>" width="110" height="110" draggable="false"/>
			</div>
			<!-- Name -->
			<h2 class="card-face__name"><?php echo $studentInfo['firstName'].' '.$studentInfo['lastName']?></h2>
			<!-- Title -->
			<span class="card-face__title">ชั้น<?php echo getGradeYearName($studentInfo['gradeYear'])?></span>
			<span class="card-face__ins">ครูที่ปรึกษา <?php echo $insName;?></span>
			<div class="card-face-footer">
				<?php if($confUserType=="instructor"){?>
				<a href="?action=insReport" class="card-face__social" title="ย้อนกลับ">
					<img src="images/back.png" width="36" height="36" draggable="false"/>
				</a>
				<?php }?>
				<a href="javascript:showAttendance();" class="card-face__social" title="ดูข้อมูลเวลาเรียน">
					<img src="images/clock.png" width="36" height="36" draggable="false"/>
				</a>
				<a href="javascript:showScore();" class="card-face__social" title="ดูข้อมูลคะแนน">
					<img src="images/list.png" width="36" height="36" draggable="false"/>
				</a>
<!-- 				<a href="https://plus.google.com/u/0/+MattiaAstorino/posts" target="_blank" class="card-face__social"> -->
<!-- 					<img src="http://imgh.us/plus_5.svg" width="36" height="36" draggable="false"/> -->
<!-- 				</a> -->
			</div>
		</div>
	</div>
</div>
<div class="report" id="attendance">
	<fieldset>
		<legend style="margin-left:12px;">รายงานเวลาเรียน</legend>
		<table class="display" id="reportAttendance">
			<thead>
				<tr>
					<th>รหัสวิชา</th>
					<th>วิชา</th>
					<th>มา</th>
					<th>สาย</th>
					<th>ขาด</th>
					<th>รวม</th>
					<th>%</th>
				</tr>
			</thead>
		</table>
	</fieldset>
</div>
<div class="report" id="score">
	<fieldset>
		<legend style="margin-left:12px;">รายงานคะแนน</legend>
		<table class="display" id="reportScore">
			<thead>
				<tr>
					<th>รหัสวิชา</th>
					<th>วิชา</th>
<?php if($confUserType=='instructor'){?>
					<th>คะแนน</th>
<?php }?>
					<th>เกรด</th>
				</tr>
			</thead>
		</table>
	</fieldset>
</div>
<?php
	} else {
		echo '<script>alert(\'ไม่พบนักเรียน\');window.close();</script>';
	}
?>