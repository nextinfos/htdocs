<?php
	if($_REQUEST['systemErrorCheck']=="1"){
		if($_REQUEST['errorPage']=="1") {
			echo '1';
		} else {
			$value['status'] = '1';
			echo json_encode($value);
		}
		exit();
	}
	switch($error){
		case 'DBFAIL': $reason='ไม่สามารถติดต่อฐานข้อมูลได้'; break;
		default: $reason='ไม่ทราบสาเหตุ';	break;
	}
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
		<script src="scripts/jquery-ui/external/jquery/jquery.js"></script>
 		<script src="scripts/jquery-ui/jquery-ui.js"></script>
 		<style>
 			html, body {
			    height:100%;
			}
 			body {
 				overflow: hidden;
 				color: #333;
/* 				width: 1024px; */
/* 				height: 768px; */
				margin: 0px;
				font-size: 1em;
/*BG Version 1*/
				background: url('images/bg_fresh.jpg');
				background-size: cover !important;
				background-position: center bottom;
				background-attachment: fixed;
			}
			.error {
				color: red;
			}
			.reason {
				color: #CC0000;
				font-size: 1.8em;
			}
			.simBG {
				background: url('images/bg_fresh.jpg');
				background-size: cover !important;
				background-position: center bottom;
				background-attachment: fixed;
				position: absolute;
				top: 0px;
				left: 0px;
				min-width: 100%;
				min-height: 100%;
				z-index: -1;
				-webkit-filter: blur(5px);
			}
			.mainContainer {
/*  			background: rgba(255,255,255,0.5); */
/* 				background: rgba(0,0,0,0.3);  */
				background: transparent;
				overflow-x: hidden;
				display:none;
				min-height: 100%;
				background: rgba(0,0,0,0.3) !important;
				box-shadow: 30px 30px 40px rgba(0,0,0,0.3);
				text-align: center;
			}
			.holder {
				position: relative;
				height: 0px;
				width: 100%;
			}
			.holder div {
				position: relative;
			}
			#menuBar {
				width: 100%;
				height: 100px;
				background: rgba(0,0,0,0.3);
				top: 70px;
				box-shadow: 0px 0px 20px rgba(0,0,0,0.3);
			}
			#pageNameD {
				text-align: right;
				padding-right: 10px;
				padding-top: 15px;
				font-size: 8pt;
			}
			#logo {
				height: 100px;
				z-index: 2;
			}
			#logoImage {
				top: 10px;
				height: 100px;
				width: 100px;
				margin: auto;
				background: url('images/logo.png') no-repeat;
				background-size: contain !important;
				background-position: center;
				-webkit-filter: drop-shadow(0px 0px 5px rgba(0,0,0,1));
			}
			#logoText {
				top: 10px;
				left: 0px;
				margin: auto;
				text-align: center;
				font-size: 2em;
				letter-spacing: -2px;
				color: white;
				text-shadow: 0 0 5px #000,0 0 10px #000,0 0 15px #000,0 0 20px #000;
			}
			#name {
				top: -80px;
				left: 0px;
				font-size: 18pt;
				text-align: right;
				padding-right: 20px;
				color: white;
				text-shadow: 0 0 5px rgba(0,0,0,0.5),0 0 10px rgba(0,0,0,0.5),0 0 15px rgba(0,0,0,0.5),0 0 20px rgba(0,0,0,0.5);
			}
			#innerContent {
				padding-top: 60px;
				height: inherit;
			}
			.ui-widget {
				font-size: 1em;
				/*-----Drop Shadow-----*/
				box-shadow: 10px 10px 20px rgba(0,0,0,0.3);
			}
			.blur {
				-webkit-filter: blur(5px);
			}
			@media screen and (max-device-width: 480px) {
				#menuBar {
					height: 50px;
					top: 50px;
				}
				#logo {
					height: 50px;
				}
				#logoImage {
					top: 35px;
					height: 70px;
					width: 70px;
					margin-left: 0px;
				}
				#logoText {
					top: -15px;
					left: 25px;
 					font-size: 1.6em; 
 					margin: 0px;
				}
				#pageNameD {
					padding-top: 5px;
				}
				#innerContent {
					padding-top: 0px;
				}
			}
			#loading {
				min-height: 100%;
				min-width: 100%;
				position: absolute;
				top: 0px;
				left: 0px;
				background: rgba(0,0,0,0.3);
				z-index: 299;
			}
			#loading>div.spinnerHolder {
				position: absolute;
				width: 140px;
				margin-top: -70px;
				margin-left: -70px;
				top: 50%;
				left: 50%;
			}
			#formHolder {
				margin: auto;
				display: table;
			}
			#formHolder>div {
				margin: auto;
/* 		 		display: block;  */
 		 		text-align: center;  
				padding: 2px;
			}
			#subjectType>label {
				width: 114px;
				height: 33px;
			}
			#subjectType>label>span,#term>label>span {
				padding-top: 5px;
			}
			#term>label {
				width: 50px;
			}
			#year {
				width: 150px;
			}
			.spacer {
				height: 5px;
			}
			.leftCell {
				padding-left: 5px !important;
			}
			.ui-dialog-titlebar-close {
				display: none !important;
			}
			#dialog {
				text-align: center;
			}
			.ui-widget-header {
				background: #EE0000 url("images/ui-bg_highlight-soft_75_EE0000_1x100.png") 50% 50% repeat-x !important;
				color: white;
			}
 		</style>
 		<style type="text/css">
		    /* start basic spinner styles*/
		    
		    div.spinner {
		      position: relative;
		      width: 100px;
		      height: 100px;
		      display: inline-block;
		    }
		    
		    div.spinner div {
		      width: 12%;
		      height: 26%;
		      background: #000;
		      position: absolute;
		      left: 44.5%;
		      top: 37%;
		      opacity: 0;
		      -webkit-animation: fade 1s linear infinite;
		      -webkit-border-radius: 50px;
		      -webkit-box-shadow: 0 0 3px rgba(0,0,0,0.2);
		    }
		    
		    div.spinner div.bar1 {-webkit-transform:rotate(0deg) translate(0, -142%); -webkit-animation-delay: 0s;}    
		    div.spinner div.bar2 {-webkit-transform:rotate(30deg) translate(0, -142%); -webkit-animation-delay: -0.9167s;}
		    div.spinner div.bar3 {-webkit-transform:rotate(60deg) translate(0, -142%); -webkit-animation-delay: -0.833s;}
		    div.spinner div.bar4 {-webkit-transform:rotate(90deg) translate(0, -142%); -webkit-animation-delay: -0.75s;}
		    div.spinner div.bar5 {-webkit-transform:rotate(120deg) translate(0, -142%); -webkit-animation-delay: -0.667s;}
		    div.spinner div.bar6 {-webkit-transform:rotate(150deg) translate(0, -142%); -webkit-animation-delay: -0.5833s;}
		    div.spinner div.bar7 {-webkit-transform:rotate(180deg) translate(0, -142%); -webkit-animation-delay: -0.5s;}
		    div.spinner div.bar8 {-webkit-transform:rotate(210deg) translate(0, -142%); -webkit-animation-delay: -0.41667s;}
		    div.spinner div.bar9 {-webkit-transform:rotate(240deg) translate(0, -142%); -webkit-animation-delay: -0.333s;}
		    div.spinner div.bar10 {-webkit-transform:rotate(270deg) translate(0, -142%); -webkit-animation-delay: -0.25s;}
		    div.spinner div.bar11 {-webkit-transform:rotate(300deg) translate(0, -142%); -webkit-animation-delay: -0.1667s;}
		    div.spinner div.bar12 {-webkit-transform:rotate(330deg) translate(0, -142%); -webkit-animation-delay: -0.0833s;}
		
		     @-webkit-keyframes fade {
		      from {opacity: 1;}
		      to {opacity: 0.25;}
		    }
		    
		    /* end basic spinner styles*/
		    
		    .spinnerHolder>div.container {
		      display: inline-block;
		      padding: 1.5em 1.5em 1.25em;
		      background: rgba(0,0,0,0.8);
		      -webkit-box-shadow: 1px 1px 1px #fff;
		      -webkit-border-radius: 1em;
		      margin: 1em;
		    }
		    
		    .spinnerHolder>div.container.grey {background: rgba(0,0,0,0.2);}    
		    .spinnerHolder>div.container.grey.blue {background: -webkit-gradient(linear, left top, left bottom, color-stop(0, #0a2744), color-stop(1, #064483));}
		    
		    .spinnerHolder>div.container div.spinner {
		      width: 28px;
		      height: 28px;
		    }
		    
		    .spinnerHolder>div.container.grey div.spinner {
		      width: 60px;
		      height: 60px;
		    }
		    
		    .spinnerHolder>div.container div.spinner div {background: #fff;}
		    
		    div.labeled {
		      font-family: sans-serif;
		      font-size: 14px;
		      margin: 0 auto;
		      background: #fff;
		      padding: 0.5em 0.75em 0.5em 0.5em;
		      display: inline-block;
		      color: #c00;
		      line-height: 25px;
		      -webkit-box-shadow: 0 0 3px rgba(0,0,0,0.4);
		      -webkit-border-radius: 1em;
		      background: -webkit-gradient(linear, left top, left bottom, color-stop(0, #fff), color-stop(1, #ccc));
		    }
		    
		    div.labeled div.spinner {
		      float: left;
		      vertical-align: middle;
		      width: 25px;
		      height: 25px;
		      margin-right: 0.5em;
		    }
		    
		    div.labeled div.spinner div {background: #c00;}
		    
			</style>
 		<script>
 			$(function(){
 				$( 'document' ).ready(function(){
 					$('#loading' ).hide('fade', '', '1000');
 					$('.mainContainer' ).show('fade', '', '1500');
 				});
 				$( "#dialog" ).dialog({
 					modal: true,
 					width: 600,
 					height: 300,
 					resizeable: false,
 					draggable: false,
 					show: {
 				        effect: "bounce",
 				        duration: 1000
 				      }
 	 			});
 			});
 			setInterval(function(){
 				$( '#dialog' ).effect('highlight',500);
 				$.post('index.php',{systemErrorCheck: '1', errorPage: '1'},function(data){
 	 				if(data!='1') document.location = document.location;
 	 			});
 	 		}, 1000);
 		</script>
 	</head>
 	<body>
 	<div class="simBG"></div>
 		<div id="loading">
 			<div class="spinnerHolder">
	 			<div class="container grey">
					<div class="spinner">
						<div class="bar1"></div>
						<div class="bar2"></div>
						<div class="bar3"></div>
						<div class="bar4"></div>
						<div class="bar5"></div>
						<div class="bar6"></div>
						<div class="bar7"></div>
						<div class="bar8"></div>
						<div class="bar9"></div>
						<div class="bar10"></div>
				  		<div class="bar11"></div>
						<div class="bar12"></div>
					</div>
				</div>
			</div>
		</div>
 		<div class="mainContainer">
 			<div class="container">
				<div class="holder">
					<div id="pageNameD">หน้าปัจจุบัน : <span id="pageName">แสดงข้อมูลระบบ</span></div>
				</div>
		 		<div class="holder">
					<div id="logo">
						<div id="logoImage"></div>
						<div id="logoText">โรงเรียนชุมชนบ้านถ้ำสิงห์</div>
						<div id="name"></div>
					</div>
				</div>
				<div class="holder" style="height:120px;">
					<div id="menuBar">
					</div>
				</div>
				<div id="innerContent">
					<h1 class="error">เกิดข้อผิดพลาด กรุณาติดต่อผู้พัฒนา</h1>
					<span class="reason">สาเหตุ: <?php echo $reason;?></span>
				</div>
			</div>
 		</div>
 		<div id="dialog" title="ข้อผิดพลาด">
			<h1 class="error">เกิดข้อผิดพลาด กรุณาติดต่อผู้พัฒนา</h1>
			<span class="reason">สาเหตุ: <?php echo $reason;?></span>
		</div>
 	</body>
 </html>
<?php exit();?>