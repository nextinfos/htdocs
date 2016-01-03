<?php
  $strSQL = 'SELECT * FROM `subject` WHERE 1';
  $objQuery = mysql_query($strSQL);
  if($objQuery){
    $data = '{	"data": [';
    $i==0;
    while($row=mysql_fetch_array($objQuery)){
      $i++;
      if($i>1) $data .= ',';
      $data .= '{	"ch":"<input type=\'checkbox\' name=\'subjectID\' value=\''.$row['subjectID'].'\' />","subjectID": "'.$row['subjectID'].'",	"subjectName": "'.$row['name'].'",	"subjectType": "'.($row['type']=='BASIC'?'พื้นฐาน':'เพิ่มเติม').'",	"edit": "e","delete":"d"}';
    }
    echo $data.']	}';
  }
?>
