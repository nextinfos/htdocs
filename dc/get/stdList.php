<?php
  $subID = $_REQUEST['regSubjectID'];
  if($subID){
    $strSQL = 'SELECT * FROM reg_student r, students s WHERE r.studentID = s.studentID AND r.status = "1" AND s.status = "1" AND r.regSubjectId = "'.$subID.'"';
    $objQuery = mysql_query($strSQL);
    if($objQuery){
      $data = '{	"data": [';
      $i==0;
  // 				$status = array('Cancel','Ontime','Late','Unknow');
      while($row=mysql_fetch_array($objQuery)){
        $i++;
        if($i>1) $data .= ',';
        $data .= '{	"num":"'.$i.'","id": "'.$row['studentID'].'",	"name": "'.$row['firstname'].'&nbsp;&nbsp;&nbsp;'.$row['lastname'].'"}';
      }
      echo $data.']	}';
    }
  }
?>
