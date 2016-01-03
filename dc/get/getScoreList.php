<?php
  $term = getTerm();
  $year = getYear();
  $instructorID = $confUserID;
  $subjectID = $_POST['subjectID'];
  $strSQL = sprintf(
    "
    SELECT
      *
    FROM
      `scoreinfo`
    WHERE
      subjectID = '%s' AND
      registerID =
        (
        SELECT
          registerID
        FROM
          `registerinfo`
        WHERE
          term = '%s' AND
          year = '%s'
        )
      ORDER BY
        date
      DESC
    ",
    mysql_real_escape_string($subjectID),
    mysql_real_escape_string($term),
    mysql_real_escape_string($year)
  );
  $objQuery = mysql_query($strSQL);
  if(mysql_num_rows($objQuery)>=1){
    while($row = mysql_fetch_array($objQuery)){
      $data.='<option value="'.$row['scoreID'].'">'.($row['type']=='TASK'?'ชิ้นงาน':($row['type']=='QUIZ'?'ตอบคำถาม':'สอบ')).' '.$row['maxScore'].' คะแนน ('.(date("d/m/Y H:i",strtotime($row['date']))).')</option>';
    }
  } else {
    $data = '<option value="0">ไม่พบข้อมูลลงคะแนน</option>';
  }
  echo $data;
?>
