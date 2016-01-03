<?php
  $strSQL = 'SELECT * FROM `instructor`;';
  $objQuery = mysql_query($strSQL);
  $data = '<option value="0">ไม่ระบุ</option>';
  if(mysql_num_rows($objQuery)>=1){
    while($row = mysql_fetch_array($objQuery)){
      $data.='<option value="'.$row['instructorID'].'">'.$row['firstName'].' '.$row['lastName'].'</option>';
    }
  }
  echo $data;
?>
