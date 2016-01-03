<?php
  $scoreID = $_REQUEST['scoreID'];
  $score = json_decode($_REQUEST['score']);
  $studentID = json_decode($_REQUEST['studentID']);
  $scoreType = $_REQUEST['scoreType'];
  $subjectID = $_REQUEST['subjectID'];
  $result['sizeOf'] = sizeof($score);
  for($i=0;$i<sizeof($score);$i++){
    $result['data'][$studentID[$i]] = $score[$i];
    if($score[$i]!=''){
      if($scoreType == 'GRADE'){
        if(!gradeSet($subjectID,getTerm(),getYear(), $studentID[$i], $score[$i])){
          $result['error'][$studentID[$i]] = TRUE;
        }
      } else {
        if(!scoreSet($scoreID, $studentID[$i], $score[$i])){
          $result['error'][$studentID[$i]] = TRUE;
        }
      }
    }
  }
  echo json_encode($result);
?>
