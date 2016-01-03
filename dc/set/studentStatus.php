<?php
  $studentID = $_REQUEST['studentID'];
  $status = $_REQUEST['status'];
  $data = '';
  $strSQL = sprintf(
      "
      UPDATE
        `student`
      SET
        `status` = '%s'
      WHERE
        `studentID` = '%s'
      ",
      mysql_real_escape_string($status),
      mysql_real_escape_string($studentID)
      );
  $objQuery = mysql_query($strSQL);
  if($objQuery){
    $data.='SUCCESS';
  } else {
    $data.="FAIL\n".$strSQL;
  }
  echo $data;
?>
