<?php
	if(date('Y-m-d H:i:s',strtotime('2015-05-02 18:18:04 +15 minute'))>date('Y-m-d H:i:s')) $status = 1; else $status = 2;
	echo $status;
?>