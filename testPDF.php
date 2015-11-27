<?php
require_once 'prince\prince.php';
$prince = new Prince('D:\xampp\htdocs\prince\bin\prince.exe');
if($prince->convert_file_to_file('testPDF.html', 'output\testPDF.pdf', $msg)) echo 'Complete'; else echo 'ERROR';
print_r($msg);
?>