<?php
	require_once 'config.php';
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>ระบบถ่ายภาพ</title>
		<link href="scripts/jquery-ui/jquery-ui.css" rel="stylesheet">
		<link href="scripts/jquery-ui/dataTables/jquery.dataTables.css" rel="stylesheet">
		<script src="scripts/jquery-ui/external/jquery/jquery.js"></script>
 		<script src="scripts/jquery-ui/jquery-ui.js"></script>
 		<script src="scripts/clock.js"></script>
 		<script src="scripts/ZeroClipboard.js"></script>
 		<script src="scripts/jquery-ui/dataTables/jquery.dataTables.js"></script>
		<style>
			#ipCamConf {
				display:none;
			}
			.video-image {
				max-width: 300px !important;
				max-height: 400px !important;
				display: block;
				/*-ms-transform: rotate(90deg); 
				-webkit-transform: rotate(90deg);
				transform: rotate(90deg);*/
			}
			#preImg {
				/*max-width: 300px !important;
				max-height: 400px !important;
				display: none;*/
				-ms-transform: rotate(90deg); 
				-webkit-transform: rotate(90deg);
				transform: rotate(90deg);
			}
		</style>
		<script>
		var videoMode;
		var root;
		  var working = null;
		  var showing = null;
		  var jsUpdate = true;
			$(function() {
				$( "#radio" ).buttonset();
				$( "input:text, input:submit" ).button();
				$('[name=videoModeSel]').change(onVideoModeChange);
				$( '#ipCamTest' ).submit(onIpCamTest);
				$( '#iploader' ).load(onIpCamLoad);
				$( '#focus' ).click(focus);
				$( '#takeAFShot' ).click(takeAFShot);
				$( '#preImg' ).load(preUpLoad);
				$( '#upload' ).click(upload);
			});
			var videoCreate = function () {
			    if (videoMode == 'off') {
			      $("#video_pane").css('display', 'none');
			      return;
			    }
			    $("#video_pane").css('display', 'table');
			    if (videoMode == "webcam") {
			    } else if (videoMode == "ipcam") {
			    	$("#ipCamConf").css('display', 'table');
			    }
			  }
			var videoDestroy = function () {
			    if (videoMode == 'webcam') {
			    
			    } else if (videoMode == "ipcam") {
			      $('#browser_video').remove();
			      $('#img1').remove()
			      $('#img2').remove()
			      jsUpdate = false;
			      working = null;
			      showing = null;
			      $("#ipCamConf").css('display', 'none');
			    }
			  }
			  var onVideoModeChange = function () {
			    var mode = $(this).val()
			    if (mode != videoMode) {
				    console.log(mode);
			      videoDestroy();
			      videoMode = mode;
			      videoCreate();
			    }
			  }
			  var onIpCamTest = function() {
				  if(!root||root!='http://'+$( '#ipCamIP' ).val()||!$( '#img1' ).attr('src')){
						root = 'http://'+$( '#ipCamIP' ).val();
						$( '#iploader' ).attr('src','http://'+$( '#ipCamIP' ).val()+'/videostatus');
				  }
			  }
			  var jsLoadImage = function() {
				    if (!jsUpdate)
				      return;

				    var oldshowing = showing;
				    showing = working;
				    working = oldshowing;
				    showing.css('visibility', 'visible')
				    working.css('visibility', 'hidden')

				    showing.unbind()
				    working.load(jsLoadImage);
				    working.attr("src", root + "/shot.jpg?rnd="+Math.floor(Math.random()*1000000));
				    if (!jsSizeOk) {
				      jsSizeOk = true;
				      $("#img2").css('margin-top', '-'+showing.height()+'px').css('display', 'block');
				    }
				  }
			 var onIpCamLoad = function() {
				console.log('ipCam Load');
				if(!root||root!='http://'+$( '#ipCamIP' ).val()||!$( '#img1' ).attr('src')){
					root = 'http://'+$( '#ipCamIP' ).val();
					$.ajax({
						type: 'GET',
						url: root+'/videostatus',
						jsonp: false,
						jsonpCallback: '',
						dataType: 'jsonp',
						crossDomain: true,
						error: function(xhr, ajaxOptions, thrownError){
							if(xhr.status==200){
									$('#video_pane').append($('<img id="img1" class="video-image" alt="video" src="'+root+'/shot.jpg?1"/>'))
								    $('#video_pane').append($('<img id="img2" class="video-image" style="display: none;" alt="video" src="'+root+'/shot.jpg?2"/>'))
								    working = $("#img2");
								    showing = $("#img1");
								    working.css("zIndex", -1);
								    jsSizeOk = false;
								    jsUpdate = true;
								    working.load(jsLoadImage);
								    working.attr("src", root + "/shot.jpg?rnd="+Math.floor(Math.random()*1000000));
							}
						}
					});
				}
			}
			var focus = function() {
				$.ajax({
					type: 'GET',
					url: root+'/focus',
					dataType: 'jsonp',
					crossDomain: true
				});
			}
			var takeAFShot = function() {
				console.log('TakeAFShot fire.');
				img.src = root+'/photoaf.jpg';
			}
			var preUpLoad = function() {
				var imgH = img.height;
				var imgW = img.width;
				$( '#photoShot' ).attr({'width':imgH,'height':imgW});
				$( '#photoShot' ).css({'width':300,'height':400});
				//$( '#preImg' ).attr({'width':imgW,'height':imgH});
				//$( '#preImg' ).css({'width':400,'height':300});
				var myCanvas = document.getElementById('photoShot');
				var ctx = myCanvas.getContext('2d');
				//ctx.translate(imgW/2,imgH);
				ctx.rotate(Math.PI/2);
				//ctx.fillRect(0, 0, 100, 50);
				ctx.drawImage(img, 0, 0, imgW, imgH,0,-imgH,imgW,imgH); // Or at whatever offset you like
			}
			var upload = function(){
				var canvas = document.getElementById('photoShot');
                var dataUrl = canvas.toDataURL("image/jpeg", 0.85);
                $("#uploading").show();
                $.ajax({
                  type: "POST",
                  url: "photoSaver.php",
                  data: { 
                     imgBase64: dataUrl,
                     user: "Joe",       
                     userid: 25         
                  }
                }).done(function(msg) {
                  console.log("saved");
                  $("#uploading").hide();
                });
            }
		</script>
	</head>
	<body>
			<div id="video_pane"></div>
			Select Source
			<form>
				<div id="radio">
					<input type="radio" id="webcam" name="videoModeSel" value="webcam"><label for="webcam">Web Cam</label>
					<input type="radio" id="ipcam" name="videoModeSel" value="ipcam"><label for="ipcam">IP Cam</label>
				</div>
			</form>
			<div id="ipCamConf">
				<form onsubmit="return false;" id="ipCamTest">
					<lable for="ipCamIP">IP:PORT</lable> : <input type="text" id="ipCamIP" value="192.168.1.104:8080">
					<input type="submit" value="ทดสอบ">
				</form>
				<button id="focus">Focus</button>
				<button id="takeAFShot">Take AF Photo</button>
				<button id="upload">Upload</button>
				<span id=uploading style="display:none;"> Uploading has begun . . .  </span>
			</div>
		<iframe src="about:blank" id='iploader' name='iploader'></iframe><img src="#" id="preImg">
		<canvas id="photoShot" width="1" height="1" crossorigin="anonymous"></canvas>
		<script>
			var img = document.getElementById('preImg');
		</script>
	</body>
</html>