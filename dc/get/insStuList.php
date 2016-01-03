<?php
  $strSQL = sprintf(
  "
  SELECT
    *
  FROM
    student
  WHERE
    instructorID = '%s'
  ",
  mysql_real_escape_string($confUserID)
  );
  if($confUserType=='instructor'){
    $objQuery = mysql_query($strSQL);
    if($objQuery&&mysql_num_rows($objQuery)>0){
      while($row=mysql_fetch_array($objQuery)){
        $gendata[] = array(
            'studentID' => $row['studentID'],
            'firstName' => $row['firstName'],
            'lastName' => $row['lastName'],
            'cardID' => "<span style='display:none;'>".$row['cardID']."</span>",
            'secondCardID' => "<span style='display:none;'>".$row['secondCardID']."</span>",
            'atd' => "<button name='viewAtd' data-studentID='".$row['studentID']."'>ดูเวลาเรียน</button>",
            'sco' => "<button name='viewSco' data-studentID='".$row['studentID']."'>ดูคะแนน</button>",
            'grade' => getAvgGrade($row['studentID'], getTerm(), getYear())
        );
      }
      $data['data'] = $gendata;
      echo json_encode($data);
    }
  }
?>
