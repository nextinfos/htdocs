<?php
  $subjectID = $_POST['subjectID'];
  $term = $_POST['term'];
  $year = $_POST['year'];
  if($subjectID&&$term&&$year){
    $strSQL = "SELECT stu.studentID AS studentID, stu.firstName AS firstName, stu.lastName AS lastName, stu.gradeYear AS gradeYear FROM `register-subject` regsub, `registerinfo` reg, `student` stu WHERE reg.registerID = regsub.registerID AND reg.term='$term' AND reg.year='$year' AND regsub.subjectID = '$subjectID' AND regsub.gradeYear = stu.gradeYear AND stu.status='NORMAL' AND stu.studentID NOT IN (SELECT regstu.studentID FROM `register-student` regstu WHERE regstu.subjectID=regsub.subjectID AND regstu.registerID=regsub.registerID );";
    $objQuery = mysql_query($strSQL);
    if(mysql_num_rows($objQuery)>=1){
      $i=0;
      while($row = mysql_fetch_array($objQuery)){
        $data['data'][$i] = $row;
        $data['data'][$i]['gradeYear'] = getGradeYearName($row['gradeYear']);
        $i++;
      }
      echo json_encode($data);
    } else {
      $data['data'][] = array("studentID"=>"","firstName"=>"ไม่พบ","lastName"=>"","gradeYear"=>"");
      echo json_encode($data);
    }
  } else {
    $data['data'][] = array("studentID"=>"","firstName"=>"","lastName"=>"","gradeYear"=>"");
    echo json_encode($data);
  }
?>
