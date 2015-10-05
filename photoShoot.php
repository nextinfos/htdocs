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
 		<style>
 			body {
 				margin: 0px;
 			}
 			#video {
 				position: relative;
 				top: 0px;
 				left: 0px;
 				max-width: 400px;
 				height: 400px;
 			}
 			#videoWrapper {
 				position: relative;
 				height: 0px;
 				top: 0px;
 				left: 0px;
 				margin: auto;
 				z-index: 2;
 				text-align: center;
 			}
 			#canvas {
 				position: relative;
 				top: 0px;
 				left: 0px;
 				height: 400px;
 				max-width: 400px;
 			}
 			#canvasWrapper {
 				position: relative;
 				height: 0px;
 				top: 0px;
 				left: 0px;
 				margin: auto;
 				z-index: 3;
 				text-align: center;
 			}
 			#previewWrapper {
 				position: relative;
 				height: 0px;
 				top: 0px;
 				left: 0px;
 				margin: auto;
 				z-index: 4;
 				text-align: center;
 			}
 			#preview {
 				position: relative;
 				top: 0px;
 				left: 0px;
 				height: 400px;
 				max-width: 400px;
 			}
 			.camContainer {
 				width: 400px;
 				height: 400px;
 				margin: auto;
 				background: rgba(0,0,0,0.5);
 			}
 			.camcontent {
 				height: 400px;
 			}
 			.cambuttons {
 				position: relative;
 				width: 100%;
 				height: 100px;
 				text-align: center;
 				top: -100px;
 				left: 0px;
 				z-index: 5;
 				background: rgba(0,0,0,0.5);
 			}
 			.blurer {
			    position: relative;
			    z-index: 1;
			    -webkit-filter: blur(10px);
			    -moz-filter: blur(10px);
			    -ms-filter: blur(10px); 
			    -o-filter: blur(10px);
			    filter: blur(10px);
			    background: rgba(250,250,250,0.9);
			    top: 0px;
			    left: 0;
			    width: 100%;
			    height: 0px;
			    padding: 0;
			}
			.blurer canvas {
				height: 400px;
				width: 400px;
				/*-webkit-transform: translateY(-504px);*/
			}
			#preview,#previewWrapper {
				display: none;
			}
 		</style>
	</head>
	<body>
	<script>
        // Put event listeners into place
        $(function(){
        	$("#videoWrapper").css("width",$("#video").css('width'));
        	$("#fileSelect").change(previewFile);
        });
        window.addEventListener("DOMContentLoaded", function() {
            // Grab elements, create settings, etc.
            var canvas = document.getElementById("canvas"),
                context = canvas.getContext("2d"),
                video = document.getElementById("video"),
                videoObj = { "video": true },
                image_format= "jpeg",
                jpeg_quality= 85,
                notSupport=false,
                errBack = function(error) {
                    console.log("Video capture error: ", error.code); 
                };

            // Put video listeners into place
            if(navigator.userAgent.match(/Android/i)){
            	$('#fileSelect').show();
				$('#video,#videoWrapper,#canvas,#canvasWrapper').hide();
				$(".cambuttons").css({'height':'250px','top':'-400px','padding-top':'150px'});
				var notSupport = true;
            } else if(navigator.getUserMedia) { // Standard
                navigator.getUserMedia(videoObj, function(stream) {
                    video.src = stream;
                    video.play();
                    $("#snap").show();
                }, errBack);
            } else if(navigator.webkitGetUserMedia) { // WebKit-prefixed
            	navigator.getUserMedia=navigator.webkitGetUserMedia(videoObj, function(stream){
                    video.src = window.webkitURL.createObjectURL(stream);
                    video.play();
                    $("#snap").show();
                }, errBack);
            } else if(navigator.mozGetUserMedia) { // moz-prefixed
            	navigator.getUserMedia=navigator.mozGetUserMedia(videoObj, function(stream){
                    video.src = window.URL.createObjectURL(stream);
                    video.play();
                    $("#snap").show();
                }, errBack);
            } else {
				$('#fileSelect').show();
				$('#video,#videoWrapper,#canvas,#canvasWrapper').hide();
				$(".cambuttons").css({'height':'250px','top':'-400px','padding-top':'150px'});
				var notSupport = true;
            }
            $("#canvas").fadeOut("fast");
                  // video.play();       these 2 lines must be repeated above 3 times
                  // $("#snap").show();  rather than here once, to keep "capture" hidden
                  //                     until after the webcam has been activated.  

            // Get-Save Snapshot - image 
            document.getElementById("snap").addEventListener("click", function() {
                if(notSupport){
                    $('#fileSelect').click();
                } else {
	               context.drawImage(video, 0, 0, 1080, 1920);
	                // the fade only works on firefox?
	               $("#canvasWrapper").css("width",$("#canvas").css('width'));
	               if("vibrate" in window.navigator) navigator.vibrate(100);
	               $("#video").get(0).pause();
	                $("#canvas").fadeOut("100");
	                $("#video").fadeOut(100);
	                $("#video").fadeIn(100);
	                $("#canvas").fadeIn("1000");
	                $("#snap").hide();
	                $("#reset").show();
	                $("#upload").show();
                }
            });
            // reset - clear - to Capture New Photo
            document.getElementById("reset").addEventListener("click", function() {
                //$("#video").fadeIn("slow");
                if(notSupport){
                	$("#reset").hide();
                	$("#upload").hide();
                	$('#preview,#previewWrapper').hide();
                } else {
	                $("#video").get(0).play();
	                $("#canvas").fadeOut("fast");
	                $("#snap").show();
	                $("#reset").hide();
	                $("#upload").hide();
	                $('#preview,#previewWrapper').hide();
                }
            });
            // Upload image to sever 
            document.getElementById("upload").addEventListener("click", function(){
                if($('#fileSelect').val())
                    var dataUrl = $('#preview').attr("src");
                else
                	var dataUrl = canvas.toDataURL("image/jpeg", 0.85);
                $("#uploading").show();
                $.ajax({
                  type: "POST",
                  url: "photoSaver.php",
                  data: { 
                     imgBase64: dataUrl,
                     user: "Joe",       
                     userid: $('#userid').val()         
                  }
                }).done(function(msg) {
                  console.log("saved");
                  $("#uploading").hide();
                  $("#uploaded").show();
                });
            });
        }, false);
        function gotSources(sourceInfos) {
            var videoSelect = document.querySelector('select#videoSource');
        	  for (var i = 0; i !== sourceInfos.length; ++i) {
        	    var sourceInfo = sourceInfos[i];
        	    var option = document.createElement('option');
        	    option.value = sourceInfo.id;
        	   if (sourceInfo.kind === 'video') {
        		   console.log('Added Video source: ', sourceInfo);
        	      option.text = sourceInfo.label || 'camera ' + (videoSelect.length + 1);
        	      videoSelect.appendChild(option);
        	    } else {
        	      console.log('Some other kind of source: ', sourceInfo);
        	    }
        	  }
        	}
        MediaStreamTrack.getSources(gotSources);
        function start() {
        	 var videoSelect = document.querySelector('select#videoSource');
        	  var videoSource = videoSelect.value;
        	  var constraints = {
        	    video: {
        	      optional: [{
        	        sourceId: videoSource
        	      }],
        	      mandatory: {
        	          maxWidth: 400,
        	          maxHeight: 400,
        	          minWidth: 300,
        	          minHeight: 300
        	        }
        	    }
        	  };
        	  navigator.webkitGetUserMedia(constraints, successCallback, errorCallback);
        	}
        function successCallback(stream) {
        	  window.stream = stream; // make stream available to console
        	  var videoElement = document.querySelector('video');
        	  videoElement.src = window.URL.createObjectURL(stream);
        	  videoElement.play();
        	}
        	function errorCallback(error) {
        	  console.log('navigator.getUserMedia error: ', error);
        	}
        	function previewFile() {
        		  var preview = document.querySelector('#preview');
        		  var file    = document.querySelector('#fileSelect').files[0];
        		  var reader  = new FileReader();

        		  reader.onloadend = function () {
        		    preview.src = reader.result;
        		    $("#upload").show();
        		    $("#reset").show();
        		    $('#preview,#previewWrapper').show();
        		    $(".cambuttons").css({'height':'100px','top':'-100px','padding-top':'0px'});
        		  }

        		  if (file) {
        		    reader.readAsDataURL(file);
        		  } else {
        		    preview.src = "";
        		  }
        		}
   </script>

<div class="camContainer">
<div class="blurer">
	<canvas width="1080" height="1920" id="canvasBlur"></canvas>
</div>
<div class="camcontent">
	<div id="videoWrapper">
		<video id="video" autoplay media='(min-device-pixel-ratio:2')></video>
	</div>
    <div id="canvasWrapper">
    	<canvas id="canvas"  width="1080" height="1920">
    </div>
    <div id="previewWrapper">
    	<img src="" id="preview">
    </div>
</div>
<div class="cambuttons">
	<svg id="reset" style="display:none;float:left;" width="100" height="100">
		<circle cx="50" cy="50" r="40" fill="white" />
		<circle cx="50" cy="50" r="32" stroke="black" stroke-width="3" fill="white" />
		<image xlink:href="images/reset.png" x="33" y="33" height="35px" width="35px">
	</svg>
	<svg id="snap" width="100" height="100">
		<circle cx="50" cy="50" r="40" fill="white" />
		<circle cx="50" cy="50" r="32" stroke="black" stroke-width="3" fill="white" />
		<image xlink:href="images/camera-icon.png" x="33" y="33" height="35px" width="35px">
	  		Sorry, your browser does not support inline SVG.
	</svg>
    <svg id="upload" style="display:none;float:right;" width="100" height="100">
    	<circle cx="50" cy="50" r="40" fill="white" />
		<circle cx="50" cy="50" r="32" stroke="black" stroke-width="3" fill="white" />
    	<image xlink:href="images/upload.png" x="33" y="33" height="35px" width="35px">
    </svg>
    <br> <span id=uploading style="display:none;"> Uploading has begun . . .  </span> 
         <span id=uploaded  style="display:none;"> Success, your photo has been uploaded! 
                <a href="javascript:history.go(-1)"> Return </a> </span> 
 </div>
 <select id="videoSource"></select>
 <?php
	if($_GET['classID']){
		$res = '<select id="userid">';
		
		$res .= '</select>';
		echo $res;
	} elseif($_GET['studentID']){
		echo '<input type="text" id="userid" value="'.$_GET['studentID'].'">';
	} else {
		echo '<input type="text" id="userid">';
	}
?>
 <input type="file" accept="image/*" id="fileSelect" name="fileSelect">
  </div>
  <script>
  var v = document.getElementById("video");
  var c = document.getElementById("canvasBlur");
  ctx = c.getContext("2d");

  v.addEventListener("play", function() {var i = window.setInterval(function() {ctx.drawImage(v,0,0,1080,1920)},20);}, false);
  v.addEventListener("pause", function() {window.clearInterval(i);}, false);
  v.addEventListener("ended", function() {clearInterval(i);}, false); 
  document.querySelector('select#videoSource').onchange=start;
  document.querySelector('select#rotate').onchange=onRotate;
  </script>
	</body>
</html>