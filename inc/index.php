<style>
	#indexContainer {
		padding: 20px;
	}
	.ui-icon-atdIcon {
		background-image: url('images/atdIcon.png') !important;
		background-size: contain;width:30px;
	}
	.ui-icon-scoreIcon {
		background-image: url('images/scoreIcon.png') !important;
		background-size: contain;width:30px;
	}
	.ui-icon-trendIcon {
		background-image: url('images/trendIcon.png') !important;
		background-size: contain;width:30px;
	}
	.ui-icon-reportIcon {
		background-image: url('images/reportIcon.png') !important;
		background-size: contain;width:30px;
	}
</style>
<style>
	/*System*/
	ul.sdt_menu{
	margin:0;
	padding:0;
	list-style: none;
	font-family:"Myriad Pro", "Trebuchet MS", sans-serif;
	font-size:14px;
	max-width:800px;
}
ul.sdt_menu a{
	text-decoration:none;
	outline:none;
}
ul.sdt_menu li{
	float:left;
	width:200px;
	height:85px;
	position:relative;
	cursor:pointer;
}
ul.sdt_menu li > a{
	position:absolute;
	top:0px;
	left:0px;
	width:200px;
	height:85px;
	z-index:12;
	background:transparent url(../images/overlay.png) no-repeat bottom right;
	-moz-box-shadow:0px 0px 2px #000 inset;
	-webkit-box-shadow:0px 0px 2px #000 inset;
	box-shadow:0px 0px 2px #000 inset;
}
ul.sdt_menu li a img{
	border:none;
	position:absolute;
	width:0px;
	height:0px;
	bottom:0px;
	left:85px;
	z-index:100;
	-moz-box-shadow:0px 0px 4px #000;
	-webkit-box-shadow:0px 0px 4px #000;
	box-shadow:0px 0px 4px #000;
}
ul.sdt_menu li span.sdt_wrap{
	position:absolute;
	top:18px;
	left:0px; 
	width:200px;
	height:60px;
	z-index:15;
}
ul.sdt_menu li span.sdt_active{
	position:absolute;
	background:#111;
	top:85px;
	width:200px;
	height:0px;
	left:0px;
	z-index:14;
	-moz-box-shadow:0px 0px 4px #000 inset;
	-webkit-box-shadow:0px 0px 4px #000 inset;
	box-shadow:0px 0px 4px #000 inset;
}
ul.sdt_menu li span span.sdt_link,
ul.sdt_menu li span span.sdt_descr,
ul.sdt_menu li div.sdt_box a{
	margin-left:15px;
	text-transform:uppercase;
	text-shadow:1px 1px 1px #000;
}
ul.sdt_menu li span span.sdt_link{
	color:#fff;
	font-size:24px;
	float:left;
	clear:both;
}
ul.sdt_menu li span span.sdt_descr{
	color:#0B75AF;
	float:left;
	clear:both;
	width:155px; /*For dumbass IE7*/
	font-size:10px;
	letter-spacing:1px;
}
ul.sdt_menu li div.sdt_box{
	display:block;
	position:absolute;
	width:200px;
	overflow:hidden;
	height:170px;
	top:85px;
	left:0px;
	display:none;
	background:#000;
}
ul.sdt_menu li div.sdt_box a{
	float:left;
	clear:both;
	line-height:30px;
	color:#0B75AF;
}
ul.sdt_menu li div.sdt_box a:first-child{
	margin-top:15px;
}
ul.sdt_menu li div.sdt_box a:hover{
	color:#fff;
}
	/*Edit*/
			ul.sdt_menu{
				padding-top:200px;
				min-width: 350px;
				margin: auto;
			}
			.sdt_menu li {
				background:rgba(0,0,0,0.3);
			}
			ul.sdt_menu li span.sdt_active {
				background:rgba(25,25,25,0.3);
				z-index: 10;
			}
			ul.sdt_menu li div.sdt_box {
				background:rgba(0,0,0,0.5);
			}
			ul.sdt_menu li div.sdt_box a {
				color:silver;
			}
			ul.sdt_menu li span span.sdt_descr {
				color:beige;
				font-size:0.9em;
			}
		</style>
<script>
	$(function(){
		$('#pageName').text('หน้าหลัก');
		$( 'document' ).ready(function(){
			setTimeout(function(){$( '#showMenu' ).click();}, 1000);
	 	});
	});
</script>
<div id="indexContainer">
	<ul id="sdt_menu" class="sdt_menu">
<?php if($confUserType=="instructor"){?>
<?php		if($confSuperUser=="1"){?>
				<li>
					<a>
						<img src="images/sdt_student.jpg" alt="" style="width: 0px; height: 0px; left: 85px;">
						<span class="sdt_active" style="height: 0px;"></span>
						<span class="sdt_wrap" style="top: 18px;">
							<span class="sdt_link">นักเรียน</span>
							<span class="sdt_descr">ระบบจัดการนักเรียน</span>
						</span>
					</a>
					<div class="sdt_box" style="display: none; left: 200px;">
							<a href="#">บันทึก/แก้ไข นักเรียน</a>
							<a href="#">ค้นหาผลของนักเรียน</a>
							<a href="#">เรียกดูผลตามกลุ่ม</a>
							<a href="#">เรียกดูผลตามด้าน</a>
					</div>
				</li>
				<li>
					<a>
						<img src="images/sdt_subject.jpg" alt="" style="width: 0px; height: 0px; left: 85px;">
						<span class="sdt_active" style="height: 0px;"></span>
						<span class="sdt_wrap" style="top: 18px;">
							<span class="sdt_link">วิชาเรียน</span>
							<span class="sdt_descr">ระบบจัดการวิชาเรียน</span>
						</span>
					</a>
					<div class="sdt_box" style="display: none; left: 200px;">
							<a href="?action=subjectManager&tag=add">เพิ่มวิชาเรียน</a>
							<a href="?action=subjectManager&tag=edit">ค้นหา,แก้ไข,ลบ วิชาเรียน</a>
					</div>
				</li>
				<li>
					<a>
						<img src="images/sdt_subject.jpg" alt="" style="width: 0px; height: 0px; left: 85px;">
						<span class="sdt_active" style="height: 0px;"></span>
						<span class="sdt_wrap" style="top: 18px;">
							<span class="sdt_link">ลงทะเบียน</span>
							<span class="sdt_descr">ระบบลงทะเบียน</span>
						</span>
					</a>
					<div class="sdt_box" style="display: none; left: 200px;">
							<a href="?action=subjectManager&tag=subjectRegistrar">เปิดวิชาสำหรับลงทะเบียน</a>
							<a href="#">ลงทะเบียนนักเรียน</a>
					</div>
				</li>
<?php 		} else {?>
				<li>
					<a>
						<img src="images/sdt_attendance.jpg" alt="" style="width: 0px; height: 0px; left: 85px;">
						<span class="sdt_active" style="height: 0px;"></span>
						<span class="sdt_wrap" style="top: 18px;">
							<span class="sdt_link">จัดการเวลาเรียน</span>
							<span class="sdt_descr">ระบบจัดการเวลาเรียน</span>
						</span>
					</a>
					<div class="sdt_box" style="display: none; left: 0px;">
							<a href="?action=atd">เริ่มการลงเวลาเรียน</a>
							<a href="?action=atdview">ดูข้อมูลการลงเวลาเรียน</a>
					</div>
				</li>
				<li>
					<a>
						<img src="images/sdt_score.jpg" alt="" style="width: 0px; height: 0px; left: 85px;">
						<span class="sdt_active" style="height: 0px;"></span>
						<span class="sdt_wrap" style="top: 18px;">
							<span class="sdt_link">จัดการคะแนน</span>
							<span class="sdt_descr">ระบบจัดการคะแนน</span>
						</span>
					</a>
					<div class="sdt_box" style="display: none; left: 0px;">
							<a href="?action=scoreman">เริ่มการลงคะแนน</a>
							<a href="?action=atd">แก้ไขข้อมูลคะแนน</a>
							<a href="?action=atdview">ดูข้อมูลการลงคะแนน</a>
					</div>
				</li>
<?php 		}?>
				<li>
					<a href="#">
						<img src="images/sdt_report.jpg" alt="" style="width: 0px; height: 0px; left: 85px;">
						<span class="sdt_active" style="height: 0px;"></span>
						<span class="sdt_wrap" style="top: 18px;">
							<span class="sdt_link">รายงาน</span>
							<span class="sdt_descr">ระบบรายงาน</span>
						</span>
					</a>
					<div class="sdt_box" style="display: none; left: 0px;">
							<a href="?p=studReport">รายชื่อนักเรียน</a>
							<a href="#">ผลการศึกษา</a>
							<a href="#">สุขภาพ</a>
					</div>
				</li>
<?php } else {?>
				<li>
					<a href="#">
						<img src="images/sdt_report.jpg" alt="" style="width: 0px; height: 0px; left: 85px;">
						<span class="sdt_active" style="height: 0px;"></span>
						<span class="sdt_wrap" style="top: 18px;">
							<span class="sdt_link">รายงาน</span>
							<span class="sdt_descr">ระบบรายงาน</span>
						</span>
					</a>
					<div class="sdt_box" style="display: none; left: 0px;">
							<a href="?p=studReport">รายชื่อนักเรียน</a>
							<a href="#">ผลการศึกษา</a>
							<a href="#">สุขภาพ</a>
					</div>
				</li>
				<li style="background:transparent;"></li>
<?php }?>
			</ul>
</div>
<script type="text/javascript">
            $(function() {
				/**
				* for each menu element, on mouseenter, 
				* we enlarge the image, and show both sdt_active span and 
				* sdt_wrap span. If the element has a sub menu (sdt_box),
				* then we slide it - if the element is the last one in the menu
				* we slide it to the left, otherwise to the right
				*/
                $('#sdt_menu > li').bind('mouseenter',function(){
					var $elem = $(this);
					$elem.find('img')
						 .stop(true)
						 .animate({
							'width':'200px',
							'height':'170px',
							'left':'0px'
						 },400,'easeOutBack')
						 .andSelf()
						 .find('.sdt_wrap')
					     .stop(true)
						 .animate({'top':'140px'},500,'easeOutBack')
						 .andSelf()
						 .find('.sdt_active')
					     .stop(true)
						 .animate({'height':'170px'},300,function(){
						var $sub_menu = $elem.find('.sdt_box');
						if($sub_menu.length){
							var left = '200px';
							if($elem.parent().children().length == $elem.index()+1)
								left = '-200px';
							$sub_menu.show().animate({'left':left},200);
						}	
					});
				}).bind('mouseleave',function(){
					var $elem = $(this);
					var $sub_menu = $elem.find('.sdt_box');
					if($sub_menu.length)
						$sub_menu.hide().css('left','0px');
					
					$elem.find('.sdt_active')
						 .stop(true)
						 .animate({'height':'0px'},300)
						 .andSelf().find('img')
						 .stop(true)
						 .animate({
							'width':'0px',
							'height':'0px',
							'left':'85px'},400)
						 .andSelf()
						 .find('.sdt_wrap')
						 .stop(true)
						 .animate({'top':'18px'},500);
				});
            });
        </script>