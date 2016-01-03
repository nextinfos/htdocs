<?php
  $studentID = $_POST['studentID'];
  $cardID1 = $_POST['cardID1'];
  $cardID2 = $_POST['cardID2'];
  $strSQL = "INSERT INTO card VALUES('".$cardID1."','".$cardID2."','".$studentID."')";
  $objQuery = mysql_query($strSQL);
  if($objQuery){
    echo '{"status":"success"}';
  }
?>
