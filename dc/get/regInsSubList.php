<?php
  $term = getTerm();
  $year = getYear();
  $instructorID = $confUserID;
  $strSQL = 'SELECT sub.subjectID AS subjectID, sub.name AS name, sub.type AS type FROM `register-subject` regsub, `registerinfo` reg, `subject` sub WHERE reg.registerID = regsub.registerID AND reg.term="'.$term.'" AND reg.year="'.$year.'" AND regsub.subjectID = sub.subjectID AND regsub.instructorID="'.$instructorID.'";';
  $objQuery = mysql_query($strSQL);
  if(mysql_num_rows($objQuery)>=1){
    while($row = mysql_fetch_array($objQuery)){
      $data.='<option value="'.$row['subjectID'].'">'.$row['subjectID'].' '.$row['name'].' ('.($row['type']=='BASIC'?'พื้นฐาน':'เพิ่มเติม').')</option>';
    }
  } else {
    $data = '<option value="0">ไม่พบวิชา</option>';
  }
  echo $data;
?>
