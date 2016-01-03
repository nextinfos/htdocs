<?php
  $term = $_REQUEST['term'];
  $year = $_REQUEST['year'];
  $gradeYear = $_REQUEST['gradeYear'];
  $strSQL = sprintf(
      "
      SELECT
        sub.subjectID,
        sub.name
      FROM
        `register-subject` regsub,
        `subject` sub
      WHERE
        regsub.subjectID = sub.subjectID AND
        regsub.gradeYear = '%s' AND
        regsub.registerID =
        (
          SELECT
            registerID
          FROM
            `registerinfo`
          WHERE
            term = '%s' AND
            year = '%s'
        )
      ",
      mysql_real_escape_string($gradeYear),
      mysql_real_escape_string($term),
      mysql_real_escape_string($year)
  );
  $objQuery = mysql_query($strSQL);
  if($objQuery&&mysql_num_rows($objQuery)>0){
    while($row=mysql_fetch_assoc($objQuery)){
      $predata = NULL;
      $predata['subjectID'] = $row['subjectID'];
      $predata['name'] = $row['name'];
      $data['subject'][] = $predata;
  // 				$data['subject'][$row['subjectID']] = $row['name'];
    }
  }
  $strSQL = sprintf(
      "
      SELECT
        stu.studentID,
        stu.firstName,
        stu.lastName,
        stu.gradeYear
      FROM
        `student` stu
      WHERE
        gradeYear = '%s'
      ",
      mysql_real_escape_string($gradeYear)
  );
  $objQuery = mysql_query($strSQL);
  if($objQuery&&mysql_num_rows($objQuery)>0){
    while($row = mysql_fetch_array($objQuery)){
      $predata = NULL;
      $predata['studentID'] = $row['studentID'];
      $predata['firstName'] = $row['firstName'];
      $predata['lastName'] = $row['lastName'];
      $predata['gradeYear'] = getGradeYearName($row['gradeYear']);
      $data['data'][] = $predata;
      foreach ($data['subject'] as $mval){
        foreach ($mval as $key=>$val){
          if($key=='subjectID'){
            $predata = NULL;
            $data['isStuReg'][$row['studentID']][$val] = isStuReg($row['studentID'], $val, $term, $year);
          }
        }
      }
    }
  } else {
    $data['data'] = '';
    $data['srtSQL'] = $strSQL;
  }
echo json_encode($data);
?>
