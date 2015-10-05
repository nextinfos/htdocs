<style>
	img {
		max-width: 100px;
		max-height: 100px;
	}
</style>
<?
	$class = "images/students//";
	if ($handle = @opendir($class)) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != ".." && $entry != "index.php") {
				echo '<img src="'.($class."$entry").'">';
			}
		}
		closedir($handle);
	}
?>