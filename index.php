<?php
	require_once 'config.php';
	if(!$confUserID) $action = 'login'; else $action = $_GET['action'];
	if(!$action) $action = 'index';
	$filename = 'inc/'.$action.'.php';
	if(!file_exists($filename)) $filename = 'inc/404.php';
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
		<link rel="stylesheet" type="text/css" href="scripts/normalize.css" />
		<link rel="stylesheet" type="text/css" href="scripts/component.css" />
		<link href="scripts/NinjaRadial.css" rel="stylesheet" type="text/css" />
		<script src="scripts/jquery-ui/external/jquery/jquery.js"></script>
 		<script src="scripts/jquery-ui/jquery-ui.js"></script>
 		<script src="scripts/clock.js"></script>
 		<script src="scripts/ZeroClipboard.js"></script>
 		<script src="scripts/jquery-ui/dataTables/jquery.dataTables.js"></script>
 		<script type="text/javascript" src="scripts/jquery.canvasjs.min.js"></script>
 		<script src="scripts/comboBox.js"></script>
		<script src="scripts/NinjaRadial.js" type="text/javascript"></script>
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
/*BG Version 2			
				background: url("images/bg.png");
    			background-attachment: fixed;
    			background-size: contain;*/
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
			}
			.container {
				background: rgba(0,0,0,0.3) !important;
				box-shadow: 30px 30px 40px rgba(0,0,0,0.3);
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
			#logoImage:hover, #logoText:hover {
				cursor: pointer;
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
			.outer-nav a {
				text-shadow: 0 0 1px #FFF;
				color: #666;
				text-decoration: none;
			}
			.outer-nav a:hover {
				color: #111;
			}
			.ui-widget {
				font-size: 1em;
				/*-----Drop Shadow-----*/
				box-shadow: 10px 10px 20px rgba(0,0,0,0.3);
			}
			.blur {
				-webkit-filter: blur(5px);
			}
			.custom-combobox {
			    position: relative;
			    display: inline-block;
			  }
			  .custom-combobox-input {
			    margin: 0;
			    padding: 5px 10px;
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
				.outer-nav a {
					font-size: 1em;
				}
			}
			#loading {
				min-height: 100%;
				min-width: 100%;
				position: absolute;
				top: 0px;
				left: 0px;
				background: rgba(0,0,0,0.3);
				z-index: 99;
			}
			#loading>div.spinnerHolder {
				position: absolute;
				width: 140px;
				margin-top: -70px;
				margin-left: -70px;
				top: 50%;
				left: 50%;
			}
			.fa {
			    display: inline-block;
			    font: normal normal normal 14px/1 'typicons';
			    font-size: inherit;
			    text-rendering: auto;
			    -webkit-font-smoothing: antialiased;
			    -moz-osx-font-smoothing: grayscale;
			    transform: translate(0, 0);
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
 	 			$('#logout').click(function(){
 	 				$('body' ).addClass( "blur", 1000);
 	 				$('.mainContainer' ).effect('fade', '', '1000');
 	 	 			$.post("index.php",{logout: 'true'})
 	 	 				.done(function(){
 	 	 					document.location = "?action=index&ref=logout";
 	 	 	 			});
 	 			});
 	 			$( '#logoImage, #logoText' ).click(function(){
 	 	 			$( '#logo' ).click();
 	 			});
 	 			buttonsModel = new Array();
 	 			buttonsModel.push({ text: "เพิ่มวิชา", click: function (e, obj) { window.location.href = '?action=subjectManager&tag=add'; } , cssIcon: "fa icon-plus", cssLabel: "", cssItem: "itemRadial" });
 	 			buttonsModel.push({ text: "แก้ไขวิชา", click: function (e, obj) { window.location.href = '?action=subjectManager&tag=edit'; } , cssIcon: "fa icon-pencil", cssLabel: "", cssItem: "itemRadial" });
 	 			buttonsModel.push({ text: "เปิดวิชา", click: function (e, obj) { window.location.href = '?action=subjectManager&tag=subjectRegistrar'; }, cssIcon: "fa icon-folder-add", cssLabel: "", cssItem: "itemRadial" });
 	 			$("#menuSubject").ninjaRadial({
 	 			     buttons: buttonsModel,
 	                backColor: "rgba(20,20,20,0.7)",
 	                borderColor: "rgba(200,200,200,0.1)"
 	 			});
 	 			buttonsModel = new Array();
 	 			buttonsModel.push({ text: "คะแนน", click: function (e, obj) { window.location.href = '?action=report&type=score'; } , cssIcon: "fa icon-attachment-outline", cssLabel: "", cssItem: "itemRadial" });
 	 			buttonsModel.push({ text: "เวลาเรียน", click: function (e, obj) { window.location.href = '?action=report&type=attendance'; } , cssIcon: "fa icon-stopwatch", cssLabel: "", cssItem: "itemRadial" });
 	 			$("#menuReport").ninjaRadial({
 	 			     buttons: buttonsModel,
 	                backColor: "rgba(20,20,20,0.7)",
 	                borderColor: "rgba(200,200,200,0.1)"
 	 			});
 			});
 			setInterval(function(){
 				$.post('index.php',{systemErrorCheck: '1'},function(data){
 	 				if(data = $.parseJSON(data)){
 	 					if(data.status=='1') document.location = document.location;
 	 				}
 	 			});
 	 		}, 30000);
 		</script>
 		<script src="scripts/modernizr.custom.25376.js"></script>
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
 		<div class="mainContainer perspective effect-rotateleft" id="perspective">
 			<div class="container">
				<div class="wrapper">
		 			<div class="holder">
		 				<div id="pageNameD">หน้าปัจจุบัน : <span id="pageName">ไม่สามารถระบุได้</span></div>
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
<!-- 						<button id="showMenu">Show Menu</button> -->
					<?php require_once $filename;?>
					</div>
				</div>
			</div>
			<nav class="outer-nav right vertical">
				<a href="?action=index&ref=menu" class="icon-home">หน้าหลัก</a>
				<?php if($confUserType=="instructor"){?>
				<?php		if($confSuperUser=="1"){?>
				<a href="#" class="icon-book" id="menuSubject">จัดการรายวิชา</a>
				<a href="?action=subjectManager&tag=studentRegistrar" class="icon-persons">ลงทะเบียนนร.</a>
				<a href="?action=studentManager&tag=add" class="icon-personadd">เพิ่มข้อมูลนร.</a>
				<a href="#" class="icon-personadd">เพิ่มข้อมูลครู</a>
				<?php 		} else {?>
				<a href="?action=atd" class="icon-book">การลงเวลาเรียน</a>
				<a href="?action=scoreManager" class="icon-book">การลงคะแนน</a>
				<a href="?action=studentRegisterList" class="icon-group-outline">รายชื่อนักเรียนที่ลงทะเบียน</a>
				<a href="?action=insReport" class="icon-group-outline">นักเรียนในที่ปรึกษา</a>
				<?php 		}?>
				<a href="#" class="icon-pie-outline" id="menuReport">รายงาน</a>
				<?php } else {?>
				<a href="#" class="icon-star">Favorites</a>
				<a href="#" class="icon-mail">Messages</a>
				<?php }?>
				<?php if($confUserID) echo '<a href="#" id="logout" class="icon-lock">ออกจากระบบ</a>';?>
			</nav>
 		</div>
 		<script src="scripts/classie.js"></script>
		<script src="scripts/menu.js"></script>
 	</body>
 </html>