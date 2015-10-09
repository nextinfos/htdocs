<?php
	require_once 'config.php';
	$atdID = $_SESSION["atdID"];
	$time = $_SESSION['atdStart'];
	$late = $_SESSION['atdLate'];
	if(!$atdID) header("Location: index.php?action=atd&tag=create");
	$strSQL = "SELECT 
							sub.subjectID AS subjectID,
							sub.name AS subjectName,
							ins.firstName AS insFirstName,
							ins.lastName AS insLastName
					 FROM 
							`attendanceinfo` atd,
							`subject` sub,
							`register-subject` regsub,
							`instructor` ins
					WHERE
							atd.attendanceID = '$atdID' AND
							atd.subjectID = regsub.subjectID AND
							atd.registerID = regsub.registerID AND
							regsub.subjectID = sub.subjectID AND
							regsub.instructorID = ins.instructorID";
	$objQuery = mysql_query($strSQL);
	if($objQuery&&mysql_num_rows($objQuery)==1){
		$row = mysql_fetch_array($objQuery);
		$subName = $row['subjectID'].' '.$row['subjectName'];
		$instName = $row['insFirstName'].' '.$row['insLastName'];
		$startDateTime = date("H:i:s",strtotime("Today $time")).' [จนถึงเวลา '.date('H:i:s',strtotime("Today $time + $late minute")).']';
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>ระบบลงเวลาเรียน</title>
		<link href="scripts/jquery-ui/jquery-ui.css" rel="stylesheet">
		<link href="scripts/jquery-ui/dataTables/jquery.dataTables.css" rel="stylesheet">
		<script src="scripts/jquery-ui/external/jquery/jquery.js"></script>
 		<script src="scripts/jquery-ui/jquery-ui.js"></script>
 		<script src="scripts/clock.js"></script>
 		<script src="scripts/ZeroClipboard.js"></script>
 		<script src="scripts/jquery-ui/dataTables/jquery.dataTables.js"></script>
		<style>
			body {
/* 				width: 1024px; */
/* 				height: 768px; */
				margin: 0px;
				font-size: 12pt;
				background: url('images/background.jpg');
				background-size: cover !important;
				background-position: center bottom;
				background-attachment: fixed;
			}
			.mainContainer {
				background: rgba(255,255,255,0.5);
			}
			fieldset {
				border: 2px solid #CCCCCC;
			}
			.subInfoInput {
				width: 280px;
				text-align: left;
			}
			#digitalClock {
				width: 200px;
				font-weight: bold;
				font-size: 20pt;
				text-align: center;
				padding: .1em .1em;
			}
			.studentListContainer {
				padding-left: 0px;
				padding-right: 0px;
			}
			#studentCard {
				padding: 0px;
			}
			.noclose .ui-dialog-titlebar-close, #opener
			{
			    display:none;
			}
			#studentList_filter {
				padding-right: 20px;
				padding-bottom: 8px;
				margin-top: -8px;
			}
			#studentList_filter input[type="search"] {
				font-size: 8pt;
			}
			.notice, .alert {
/* 				position: relative; */
/* 				top: -1125px; */
				width: 940px;
				height: 30px;
				padding: 0px !important;
			}
			.notice p, .alert p {
				margin: 6px;
				font-size: 10pt;
			}
			.notice-dialog .ui-dialog-titlebar {
				display:none;
			}
			.ui-dialog.notice-dialog {
				padding: 0;
				border: 0;
				margin: 8px;
			}
			#cardID {
				font-size: 8pt !important;
			}
		</style>
		<script>
		String.prototype.replaceArray = function(find, replace) {
			  var replaceString = this;
			  for(var j = 0; j < this.length; j++){
				  for (var i = 0; i < find.length; i++) {
				    replaceString = replaceString.replace(find[i], replace[i]);
				  }
			  }
			  return replaceString;
			};
		</script>
	</head>
	<body>
	<script>
		var studentListVar;
		var autoHideTO;
		var autoHideNTO;
		var audioElement;
			$(function() {
				$('input:button, input:submit').button();
				$('input:text, input:password').button().css({
					'font' : 'inherit',
					'color' : 'inherit',
					'text-align' : 'left',
					'outline' : 'none',
					'cursor' : 'text'
				});;
				$('fieldset').addClass("ui-corner-all");
				setInterval(showClock, 1000);
				$( ".notice" ).dialog({
					autoOpen: false,
					draggable: false,
					resizeable: false,
					width: 940,
					height: 30,
					position: {at: "center top", of: window},
					show: {effect: "slide",direction:"up",easing: "easeInOutQuart", duration: 500},
					hide: {effect: "slide",direction:"up",easing: "easeInOutQuart", duration: 350 },
					dialogClass: "notice-dialog"
				});
				$( ".alert" ).dialog({
					autoOpen: false,
					draggable: false,
					resizeable: false,
					width: 940,
					height: 30,
					position: {at: "center top", of: window},
					show: {effect: "slide",direction:"up",easing: "easeInOutQuart", duration: 150},
					hide: {effect: "slide",direction:"up",easing: "easeInOutQuart", duration: 150 },
					dialogClass: "notice-dialog"
				});
				$( "#studentCard" ).dialog({
					autoOpen: false,
					width: 328,
					height: 260,
					draggable: false,
					resizable: false,
					show: {effect: "slide",direction:"down",easing: "easeInOutQuart", duration: 500},
					hide: {effect: "slide",direction:"down",easing: "easeInOutQuart", duration: 350 },
					position: { at: "right bottom", of: window },
					dialogClass: "noclose"
				});
				studentListVar = $('#studentList').DataTable( {
					paging: false,
				    scrollY: 280,
				    "order": [2,'desc'],
				    "orderFixed": [2,'desc'],
				    "columns": [{ data: "id","orderable": false },{ data: "name","orderable": false },{ data: "status","orderable": false }],
				    ajax:  {
			            url: "dataCenter.php",
			            type: 'POST',
			            data: {"action": "get","type":"atdList","atdID":"01"}
			        }
				} );
				$('.studentListContainer').addClass("fg-toolbar ui-toolbar ui-widget-header ui-helper-clearfix");
				$( "#opener" ).click(function() {
					$( "#studentCard" ).dialog( "open" );
				});
				$('input[type=search]').button();
				$.preloadImages = function() {
					for (var i = 0; i < arguments.length; i++) {
						$("<img />").attr("src", arguments[i]);
					}
				}
				$.preloadImages("images/cardBackground.png","images/logo.png");
			});
			$(document).keypress(function() {
				if(!$("#studentIDManual").is(":focus")&&!$("#cardID").is(":focus")&&!$("input[type=search]").is(":focus")){
					$( ".alert" ).dialog( "close" );
					$("#cardID").focus();
				}
			});
			function convertCard(id){
				console.info('Card detect ID : '+id);
				var res = id;
				if($.isNumeric(id)){
					res = id;
				} else {
					var thn = ['ๅ','/','-','ภ','ถ','ุ','ึ','ค','ต','จ'];
					var thc = ['+','๑','๒','๓','๔','ู','฿','๕','๖','๗'];
					var num = ['1','2','3','4','5','6','7','8','9','0'];
					res = res.replaceArray(thn, num);
					res = res.replaceArray(thc, num);
					console.debug('Convert card to number ID : '+res);
				}
				return res;
			}
			function cardCheck(type){
				if(type=='card'){
					var cardID = convertCard($("#cardID").val());
					var studentID = '';
				} else {
					var cardID = '';
					var studentID = $("#studentIDManual").val();
				}
				var date = new Date;
				$.ajax({
					  type: "POST",
					  url: 'dataCenter.php',
					  data: {
						  'action':'set',
						  'type':'atdList',
						  'cardID':cardID,
						  'studentID':studentID,
						  'time':date.getHours()+':'+date.getMinutes()+':'+date.getSeconds()
					},
					beforeSend: function(){console.log('checkCard Function is sending Ajax request to dataCenter.php');},
					dataType: 'json',
					success: function(dataR){
						console.log('checkCard Function has recived data from dataCenter.php');
						if(dataR.status=="SUCCESS"){
							console.info('Call onSuccessAtd Function');
							onSuccesAtd(dataR.data[0].studentID);
					    	playSound('images/sounds/pass.mp3');
						} else {
							var reason = dataR.data[0].reason;
							if(reason == "CheckedIn") telReason = 'มีชื่อนี้อยู่ในระบบแล้ว.';
							if(reason == "NotFoundReg") telReason = 'Not Found this student in registrar.';
							if(reason == "NotFoundCard"){
								telReason = 'ไม่พบข้อมูลบัตร กรุณาติดต่อผู้ดูแล.';
								playSound('images/sounds/cardNotFound.mp3');
							}
							notice(telReason);
							console.warn(telReason);
						}
					},
					error: function(xhr, ajaxOptions, thrownError){
						  console.warn('Check Card Fail');
						  console.warn('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
					  }
				});
				$("#cardID").val("")
				$("#studentIDManual").val("")
			}
			function onSuccesAtd(dataR){
				console.log('Attendance List Reload.');
				loadCard(dataR);
				studentListVar.ajax.reload();
			}
			function playSound(file){
				audioElement = document.createElement('audio');
		        audioElement.setAttribute('src', file);
		        audioElement.setAttribute('autoplay', 'autoplay');
		        //audioElement.load()
		        $.get();
		        return true;
			}
			function notice(msg){
				if(autoHideNTO) clearTimeout(autoHideNTO);
				$( "#notice-msg" ).html(msg);
				$( ".notice" ).dialog( "open" );
				console.log('Clear Notice  Autohide.');
				autoHideNTO = setTimeout(function() {
					console.log('Autohide Notice Called.');
					$( ".notice" ).dialog("close");
				}, 5000 );
				console.log('Autohide Notice Active.');
			}
			function loadCard(dataR){
				console.log(dataR);
				$( "#studentID" ).text(dataR.data[0].id);
				$( "#studentFName" ).text(dataR.data[0].fname);
				$( "#studentLName" ).text(dataR.data[0].lname);
				$( "#studentPhoto" ).css("background-image","url('"+dataR.data[0].photo+"')");
				$( "#studentCode" ).css("background-image","url('http://www.barcode-generator.org/zint/api.php?bc_number=8&bc_data=STD"+dataR.data[0].id+"')");
				clearTimeout(autoHideTO);
				$( "#studentCard" ).dialog( "open" );
				console.log('Clear Autohide.');
				autoHideTO = setTimeout(function() {
					console.log('Autohide Called.');
					$( "#studentCard" ).dialog("close");
				}, 3000 );
				console.log('Autohide Active.');
			}
		</script>
	<div class="mainContainer" style="width:1024px;height:768px;border-left:1px solid black;border-right:1px solid black;overflow:hidden;margin: 0 auto;">
		<div style="position:relative;width: 440px;height: 150px;left:40px;top:40px;">
 				<fieldset>
  					<legend>รายละเอียดวิชา</legend>
  						<div style="width:400px;text-align:right;">
	  						ชื่อวิชา : <input type="text" readonly id="subName" class="subInfoInput" value="<?php echo $subName;?>"><br>
	 						ครูผู้สอน : <input type="text" readonly id="instName" class="subInfoInput" value="<?php echo $instName;?>"><br>
	 						เวลาเริ่ม : <input type="text" readonly id="startDateTime" class="subInfoInput" value="<?php echo $startDateTime;?>">
 						</div>
			</fieldset>
		</div>
		<div style="position:relative;width: 200px;height: 260px;left:774px;top:-120px;">
			<canvas id="analogClock" width="200" height="200"></canvas>
			<input type="button" id="digitalClock">
		</div>
		<div style="position:relative;width: 540px;height: 80px;left:40px;top:-200px;">
			<fieldset>
  					<legend>กรอกเลขประจำตัวด้วยตัวเอง</legend><form action="javascript:void(0);" onsubmit="cardCheck('ID');">
  						<div style="width:500px;text-align:right;">
	 						เลขประจำตัว : <input type="text" id="studentIDManual" name="studentIDManual" class="subInfoInput">
	 						<input type="submit" value="เพิ่ม">
 						</div>
 						</form>
			</fieldset>
		</div>
		<div style="position:relative;width: 944px;height: 380px;left:40px;top:-180px;">
			<fieldset class="studentListContainer">
  					<legend style="margin-left:12px;">รายชื่อนักเรียนที่ลงเวลาเรียน</legend>
  						<div style="width:940px;height:380px;">
	 						<table id="studentList" class="display" style="width:100%">
	 							<thead>
		 							<tr class="ui-state-default">
		 								<th style="width: 100px;">เลขประจำตัว</th>
		 								<th>ชื่อ - สกุล</th>
		 								<th style="width: 100px;">สถานะ</th>
		 							</tr>
	 							</thead>
	 						</table>
 						</div>
			</fieldset>
		</div>
		<div style="position:relative;width: 200px;height: 260px;left:60px;top:-540px;">
			<form action="javascript:void(0);" onsubmit="cardCheck('card');"><input type="password" id="cardID" autofocus autocomplete="off"></form><button id="opener">Open Dialog</button>
		</div>
		<div class="ui-state-highlight ui-corner-all notice"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span><span id="notice-msg"></span></p></div>
		<div class="ui-state-error ui-corner-all alert"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><span id="alert-msg">ระบบแตะบัตร(RFID)ไม่ทำงาน </span></p></div>
	</div>
	<script>
		//	สคริปคัดลอก
			ZeroClipboard.setMoviePath('scripts/ZeroClipboard.swf');
			//create client
			var clip = new ZeroClipboard.Client();
			clip.addEventListener('mousedown',function() {
				clip.setText(document.getElementById('digitalClock').value);
			});
			clip.glue('digitalClock');
	</script>
	<div id="studentCard" title="ลงเวลาสำเร็จ">
		<?php require 'studentCard.php';?>
	</div>
	</body>
</html>