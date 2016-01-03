<?php
  $term = $_POST['term'];
  $year = $_POST['year'];
  $subjectID = $_POST['subjectID'];
  $strSQL = "SELECT attendanceID,date FROM attendanceinfo WHERE subjectID = '$subjectID' AND registerID = (SELECT registerID FROM registerinfo WHERE term='$term' AND year='$year') AND date LIKE '".date("Y-m-d")."%'";
  $objQuery = mysql_query($strSQL);
  if(mysql_num_rows($objQuery)==0){
    $strSQL = "SELECT regsub.registerID FROM `register-subject` regsub, `registerinfo` reg WHERE reg.term = '$term' AND reg.year = '$year' AND reg.registerID = regsub.registerID AND regsub.subjectID = '$subjectID'";
    $objQuery = mysql_query($strSQL);
    if(mysql_num_rows($objQuery)==1){
      $row = mysql_fetch_array($objQuery);
      $registerID = $row['registerID'];
      $late = $_POST['late'];
      $time = $_POST['time'];
      $strSQL = 'INSERT INTO attendanceinfo VALUES (NULL,"'.$subjectID.'","'.$registerID.'","'.date('Y-m-d H:i:s',strtotime('Today '.$time)).'")';
      $objQuery = mysql_query($strSQL);
      if($objQuery){
        $atdID = mysql_insert_id();
        $_SESSION['atdID'] = $atdID;
        $_SESSION['atdLate'] = $late;
        $_SESSION['atdStart'] = $time;
        $data['status'] = "SUCCESS";
        $data['atdID'] = $atdID;
      } else {
        $data['status']  = "FAIL";
        $data['strSQL'] = $strSQL;
      }
    } else {
      $data['status'] = "NOTFOUND";
      $data['strSQL'] = $strSQL;
    }
  } else {
    $row = mysql_fetch_array($objQuery);
    $late = $_POST['late'];
    $atdID = $row['attendanceID'];
    $time = $row['date'];
    $_SESSION['atdID'] = $atdID;
    $_SESSION['atdLate'] = $late;
    $_SESSION['atdStart'] = date("H:i:s",strtotime($time));
    $data['status'] = "SUCCESS";
    $data['atdID'] = $atdID;
  }
echo json_encode($data);
?>
