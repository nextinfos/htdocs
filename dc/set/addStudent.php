<?php
  $studentID = $_POST['studentID'];
  $personalID = $_POST['personalID'];
  $firstName = $_POST['firstName'];
  $lastName = $_POST['lastName'];
  $gender = $_POST['gender'];
  $cardID = $_POST['cardID'];
  if($cardID=='') $cardID = 'NULL'; else $cardID="'$cardID'";
  $secondCardID = $_POST['secondCardID'];
  if($secondCardID=='') $secondCardID = 'NULL'; else $secondCardID="'$secondCardID'";
  $gradeYear = $_POST['gradeYear'];
  $instructorID = $_POST['instructorID'];
  if($instructorID==0) $instructorID = 'NULL'; else $instructorID="'$instructorID'";
  $password = randomPassword(6);
  $status = 'NORMAL';
  $strSQL = "SELECT * FROM `student` WHERE studentID='$studentID' OR personalID='$personalID';";
  $objQuery = mysql_query($strSQL);
  if(mysql_num_rows($objQuery)<1){
    $strSQL = "INSERT INTO `student` VALUES ('$studentID','$personalID','$firstName','$lastName','$gender',$cardID,$secondCardID,'$gradeYear',$instructorID,'$password','$status');";
    $objQuery = mysql_query($strSQL);
    if($objQuery){
      $data['status'] = 'OK';
      $data['studentID'] = $studentID;
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
