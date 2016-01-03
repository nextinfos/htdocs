<?php
  $instructorID = $_POST['instructorID'];
  $firstName = $_POST['firstName'];
  $lastName = $_POST['lastName'];
  $password = randomPassword(6);
  $strSQL = "SELECT * FROM `instructor` WHERE instructorID='$instructorID';";
  $objQuery = mysql_query($strSQL);
  if(mysql_num_rows($objQuery)<1){
    $strSQL = "INSERT INTO `instructor` VALUES ('$instructorID','$firstName','$lastName','$password',0);";
    $objQuery = mysql_query($strSQL);
    if($objQuery){
      $data['status'] = 'OK';
      $data['instructorID'] = $instructorID;
      $data['password'] = $password;
    } else {
      $data['status'] = 'ERROR';
      $data['strSQL'] = $strSQL;
    }
  } else {
    $data['status'] = 'EXIST';
    $data['studentID'] = $studentID;
  }
  echo json_encode($data);
?>
