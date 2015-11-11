<style>
	.studentListContainer {
		padding-left: 0px;
		padding-right: 0px;
		max-width: 960px;
		margin: auto;
	}
	td {
	    text-align: center;
	 }
</style>
<script>
  $(function() {
	    $( '#studentList' ).on( 'xhr.dt', function(){
			setTimeout(function(){
				$( 'button[name="viewAtd"]' ).button()
				.click(function(){
						window.open("?action=stuReport&type=atd&studentID="+$(this).attr('data-studentID'));
				});
				$( 'button[name="viewSco"]' ).button()
				.click(function(){
					window.open("?action=stuReport&type=sco&studentID="+$(this).attr('data-studentID'));
				});
			}, 100);
		}).DataTable( {
			paging: false,
			"order": [0,'asc'],
			"columns": [
						{
							data: "studentID",
							"orderable": true 
						},
						{
							data: "firstName",
							"orderable": true
						},
						{
							data: "lastName",
							"orderable": true
						},
						{
							data: "cardID",
							"orderable": false
						},
						{
							data: "secondCardID",
							"orderable": false
						},
						{
							data: "atd",
							"orderable": false
						},
						{
							data: "sco",
							"orderable": false
						},
						{
							data: "grade",
							"orderable": false
						}
					],
			ajax:  {
				url: "dataCenter.php",
				type: 'POST',
				data: function ( d ) {
					d.action = "get";
					d.type = "insStuList";
				}
			}
	    });
	    $('fieldset').addClass("ui-corner-all");
		$('.studentListContainer').addClass("fg-toolbar ui-toolbar ui-widget-header ui-helper-clearfix");
		$( '.profile' ).click(function(){
// 			window.open("?action=stuReport&studentID="+$(this).attr('data-studentID'));
			$( '#loading' ).show('fade', 100);
			document.location = "?action=stuReport&studentID="+$(this).attr('data-studentID');
		});
  });
</script>
<style>
	.profile {
		width: 200px;
		display: inline-block;
		font-family: 'Roboto', sans-serif;
		padding: 20px;
		margin: auto;
	}
	@media screen and (max-device-width: 640px) {
		.profile {
			width: 100%;
			padding: 20px 0px;
			margin: auto;
		}
	}
	.card-face__avatar:hover,.card-face__name:hover {
		cursor: pointer;
	}
	.card-face__avatar {
	  display: block;
	  width: 110px;
	  height: 110px;
	  position: relative;
	  margin-top: 10px;
	  margin-bottom: 10px;
	  margin: auto;
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
	.card-face__bullet {
	  background-color: #f44336;
	  color: #FFF;
	  display: block;
	  padding: 4px 0;
	  border-radius: 50%;
	  width: 23px;
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
	.card-face__name {
	  font-size: 24px;
	  font-weight: 400;
	  margin-top: 0;
	  margin-bottom: 0;
	  white-space: nowrap;
	  overflow: hidden;
	  text-overflow: ellipsis;
	  box-sizing: border-box;
	  padding: 0px;
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
	.studentListContainer {
		display: none;
	}
	.studentHolder {
		padding-top: 20px;
		width: 100%;
 		max-width: 1220px; /*730px=3,975px=4,1220px=5*/
		min-width: 240px;
		margin: auto;
	}
	.studentID {
		background-color: #55F;
		width: 50px;
		top: 90px;
		left: 30px;
	}
</style>
 <div style="text-align:center;">
	<h1>รายชื่อนักเรียนในการดูแล</h1>
</div>
<div class="studentHolder">
<?php
$strSQL = sprintf(
		"
		SELECT
			*
		FROM
			student
		WHERE
			instructorID = '%s'
		",
		mysql_real_escape_string($confUserID)
);
if($confUserType=='instructor'){
	$objQuery = mysql_query($strSQL);
	if($objQuery&&mysql_num_rows($objQuery)>0){
		while($row=mysql_fetch_array($objQuery)){
			$gendata[] = array(
					'studentID' => $row['studentID'],
					'firstName' => $row['firstName'],
					'lastName' => $row['lastName'],
					'cardID' => "<span style='display:none;'>".$row['cardID']."</span>",
					'secondCardID' => "<span style='display:none;'>".$row['secondCardID']."</span>",
					'atd' => "<button name='viewAtd' data-studentID='".$row['studentID']."'>ดูเวลาเรียน</button>",
					'sco' => "<button name='viewSco' data-studentID='".$row['studentID']."'>ดูคะแนน</button>"
			);
			$studentPhoto = 'images/students/'.$row['studentID'].'.jpg';
			if(!file_exists($studentPhoto)) $studentPhoto = $noPhoto;
?>
<div class="profile" data-studentID="<?php echo $row['studentID'];?>">
	<div class="card-face__avatar">
		<!-- Bullet notification -->
<!-- 		<span class="card-face__bullet">--</span> -->
		<span class="card-face__bullet studentID"><?php echo $row['studentID'];?></span>
		<!-- User avatar -->
		<img src="<?php echo $studentPhoto;?>" width="110" height="110" draggable="false"/>
	</div>
	<!-- Name -->
	<h2 class="card-face__name"><?php echo $row['firstName'].' '.$row['lastName']?></h2>
</div>
<?php
		}
	}
} 
?>
</div>
<fieldset class="studentListContainer">
	<legend style="margin-left:12px;">รายชื่อนักเรียน</legend>
	<div>
		<table id="studentList" class="display">
			<thead>
				<tr >
					<th width="91"> <div align="center">StudentID </div></th>
					<th width="98"> <div align="center">ชื่อ </div></th>
					<th width="98"> <div align="center">นามสกุล </div></th>
					<th width="1" style="width:1px !important"></th>
					<th width="1"></th>
					<th width="97"> <div align="center">เวลาเรียน </div></th>
					<th width="97"> <div align="center">คะแนน </div></th>
					<th width="50"> <div align="center">เกรด </div></th>
				</tr>
			</thead>
		</table>
	</div>
</fieldset>