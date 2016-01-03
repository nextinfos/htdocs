<?php
  if($atdID){
    $strSQL = sprintf(
      "
      SELECT
        stuatd.studentID,
        stuatd.status,
        stu.firstName,
        stu.lastName
      FROM
        `studentattendance` stuatd,
        `student` stu
      WHERE
        stuatd.attendanceID = '%s' AND
        stuatd.studentID = stu.studentID
      ",
      mysql_real_escape_string($atdID)
    );
    $objQuery = mysql_query($strSQL);
    if($objQuery){
      if(mysql_num_rows($objQuery)>0){
        $status = array('ONTIME'=>'Ontime','LATE'=>'Late','Unknow');
        while($row=mysql_fetch_array($objQuery)){
          $data['data'][] = array(
              "id"=> $row['studentID'],
              "name"=> $row['firstName'].'&nbsp;&nbsp;&nbsp;'.$row['lastName'],
              "status"=> $status[$row['status']]);
        }
      } else {
        $data['data'] = NULL;
      }
    }
  }
  echo json_encode($data);
?>
