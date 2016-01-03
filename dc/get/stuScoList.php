<?php
  $subjectID = $_REQUEST['subjectID'];
  $scoreType = $_REQUEST['scoreType'];
  $addStatus = $_REQUEST['addStatus'];
  $instructorID = $confUserID;
  $term = getTerm();
  $year = getYear();
  $scoreID = $_REQUEST['scoreID'];
  $objQuery = getStuRegList($subjectID, $instructorID, $year, $term);
  if($objQuery&&mysql_num_rows($objQuery)>0){
    while($row=mysql_fetch_array($objQuery)){
      if($scoreType=='GRADE'){
        $grade = getGrade($row['studentID'],$subjectID,$term,$year);
        $select = '<select name="score" style="width: 80px;">';
        $select.= '<option value="">--</option>';
        $select.= '<option value="4"'.selected($grade, '4').'>4.0</option>';
        $select.= '<option value="3.5"'.selected($grade, '3.5').'>3.5</option>';
        $select.= '<option value="3"'.selected($grade, '3').'>3.0</option>';
        $select.= '<option value="2.5"'.selected($grade, '2.5').'>2.5</option>';
        $select.= '<option value="2"'.selected($grade, '2').'>2.0</option>';
        $select.= '<option value="1.5"'.selected($grade, '1.5').'>1.5</option>';
        $select.= '<option value="1"'.selected($grade, '1').'>1.0</option>';
        $select.= '<option value="0"'.selected($grade, '0').'>0</option>';
        $select.= '<option value="W"'.selected($grade, 'W').'>W</option>';
        $select.= '</select>';
        $data['data'][] = array(
            'cardID'=>'<span style="display:none;">'.$row['cardID'].'</span>',
            'secondCardID'=>'<span style="display:none;">'.$row['secondCardID'].'</span>',
            'studentID'=>$row['studentID'],
            'firstName'=>$row['firstName'],
            'lastName'=>$row['lastName'],
            'score'=>'<input type="hidden" name="studentID" value="'.$row['studentID'].'">'.$select
        );
      } else {
        $dataScore = $addStatus=='1'?'':getScore($scoreID,$row['studentID']);
        $data['data'][] = array(
            'cardID'=>'<span style="display:none;">'.$row['cardID'].'</span>',
            'secondCardID'=>'<span style="display:none;">'.$row['secondCardID'].'</span>',
            'studentID'=>$row['studentID'],
            'firstName'=>$row['firstName'],
            'lastName'=>$row['lastName'],
            'score'=>'<input type="hidden" name="studentID" value="'.$row['studentID'].'"><input type="text" name="score" value="'.$dataScore.'" />'
        );
      }
    }
  } else {
    $data['data'][] = array(
        'cardID'=>'',
        'secondCardID'=>'',
        'studentID'=>'',
        'firstName'=>'ไม่พบข้อมูล',
        'lastName'=>'',
        'score'=>''
    );
  }
  echo json_encode($data);
?>
