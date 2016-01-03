<?php
  $studentID = $_REQUEST['studentID'];
  $data = '';
  $strSQL = sprintf(
      "
      SELECT
        status
      FROM
        student
      WHERE
        studentID = '%s'
      ",
      mysql_real_escape_string($studentID)
      );
  $objQuery = mysql_query($strSQL);
  if($objQuery&&mysql_num_rows($objQuery)>0){
    $row = mysql_fetch_assoc($objQuery);
    $data .= '<option value="NORMAL"'.selected($row['status'], 'NORMAL').'>ปกติ</option>';
    $data .= '<option value="GRADUATE" '.selected($row['status'], 'GRADUATE').'>สำเร็จการศึกษา</option>';
    $data .= '<option value="SUSPEND"'.selected($row['status'], 'SUSPEND').'>พ้นสภาพ</option>';
  }
  echo $data;
?>
