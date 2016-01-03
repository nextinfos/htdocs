<?php
  $subjectID = $_REQUEST['subjectID'];
  $instructorID = $confUserID;
  $term = getTerm();
  $year = getYear();
  $objQuery = getStuRegList($subjectID, $instructorID, $year, $term);
  if($objQuery&&mysql_num_rows($objQuery)>0){
    while($row=mysql_fetch_array($objQuery)){
      $data['data'][] = array(
          'cardID'=>'<span style="display:none;">'.$row['cardID'].'&nbsp'.$row['secondCardID'].'</span>',
          'studentID'=>$row['studentID'],
          'firstName'=>$row['firstName'],
          'lastName'=>$row['lastName'],
          'grade'=>$row['grade']==NULL?'--':$row['grade']
      );
    }
  } else {
    $data['data']='';
  }
  echo json_encode($data);
?>
