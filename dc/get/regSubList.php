<?php
  $term = $_POST['term'];
  $year = $_POST['year'];
  $strSQL = 'SELECT * FROM `register-subject` regsub, `registerinfo` reg, `subject` sub WHERE regsub.subjectID = sub.subjectID AND reg.registerID = regsub.registerID AND reg.term="'.$term.'" AND reg.year="'.$year.'";';
  $objQuery = mysql_query($strSQL);
  if(mysql_num_rows($objQuery)>=1){
    while($row = mysql_fetch_array($objQuery)){
      $data.='<option value="'.$row['subjectID'].'">'.$row['subjectID'].' '.$row['name'].'('.($row['type']=='BASIC'?'พื้นฐาน':'เพิ่มเติม').')</option>';
    }
  } else {
    $data = '<option value="0">ไม่พบวิชา</option>';
  }
  echo $data;
?>
