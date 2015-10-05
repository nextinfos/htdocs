<script>
	$('#pageName').text('เข้าสู่ระบบ');
	$(function(){
		$('input:text, input:password').button().css({
			'font' : 'inherit',
			'color' : 'inherit',
			'text-align' : 'left',
			'outline' : 'none',
			'cursor' : 'text'
		});
		$('input:submit').button();
		$( '#typeSelect' ).buttonset();
	});
</script>
<style>
	#loginContainer {
		text-align:center;
		padding-top: 80px;
	}
	#loginContainer>form>div {
		padding: 5px;
		width: 260px;
		margin: auto;
	}
	#username, #password {
		padding-left: 30px;
		width: 194px;
	}
	#usernameIcon {
		width: 20px;
		height: 20px;
		top: 7px;
		left: 15px;
		background: url('images/user.png') no-repeat center;
		background-size: contain;
		z-index: 2;
	}
	#passwordIcon {
		width: 20px;
		height: 20px;
		top: 7px;
		left: 15px;
		background: url('images/key.png') no-repeat center;
		background-size: contain;
		z-index: 2;
	}
	#typeSelect>label {
		width: 121px;
	}
</style>
<div id="loginContainer">
	<?php echo $reason;?>
	<form method="POST">
		<div><div class="holder"><div id="usernameIcon"></div></div><input type="text" id="username" name="username" placeholder="ชื่อผู้ใช้"<?php echo $username?' value="'.$username.'"':'';?> /></div>
		<div><div class="holder"><div id="passwordIcon"></div></div><input type="password" id="password" name="password" placeholder="รหัสผ่าน"<?php echo $password?' value="'.$password.'"':'';?> /></div>
		<div><div id="typeSelect"><input type="radio" name="type" id="student" value="student"<?php echo $type=='student'?' checked':'';?> /><label for="student">นักเรียน</label><input type="radio" name="type" id="instructor" value="instructor"<?php echo $type=='instructor'?' checked':'';?> /><label for="instructor">บุคลากร</label></div></div>
		<div><input type="submit" value="เข้าสู่ระบบ" /></div>
	</form>
</div>