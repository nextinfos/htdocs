<?php
  $term = $_POST['term'];
  $year = $_POST['year'];
  $subjectID = $_POST['subjectID'];
  if($term&&$year&&$subjectID){
    $strSQL = "INSERT INTO `register-student` ";
    $strSQL.= "SELECT stu.studentID AS studentID, regsub.subjectID, regsub.registerID, NULL FROM `register-subject` regsub, `registerinfo` reg, `student` stu WHERE reg.registerID = regsub.registerID AND reg.term='$term' AND reg.year='$year' AND regsub.subjectID = '$subjectID' AND regsub.gradeYear = stu.gradeYear AND stu.status='NORMAL' AND stu.studentID NOT IN (SELECT regstu.studentID FROM `register-student` regstu WHERE regstu.subjectID=regsub.subjectID AND regstu.registerID=regsub.registerID );";
    $objQuery = mysql_query($strSQL);
    if($objQuery){
      echo "OK";
    } else {
      echo "ERROR";
    }
  } else {
    echo "UNVALID";
}
?>
