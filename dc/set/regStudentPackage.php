<?php
  $term = $_REQUEST['term'];
  $year = $_REQUEST['year'];
  $studentID = json_decode($_REQUEST['studentID']);
  $data = $_REQUEST['data'];
  if($term&&$year&&$studentID&&$data){
    $error = false;
    foreach ($data as $key => $val){
      $objSubject = json_decode($val);
      $subjectID = $objSubject->subjectID;
      $objData = $objSubject->data;
      foreach ($objData as $dkey => $dval){
        if($dval){
          if(!isStuReg($studentID[$dkey], $subjectID, $term, $year)){
            $res=regStudent($studentID[$dkey], $subjectID, $term, $year);
            if($res===true){
              $success[$subjectID][] = $studentID[$dkey];
            } else {
              $error = true;
              $fail[$subjectID][] = $studentID[$dkey];
              $fail[$subjectID][$studentID[$dkey]][] = $res;
            }
          }
        }
      }
    }
    $result['success'] = $success;
    $result['fail'] = $fail;
    if($error==false){
      $result['status'] = 'SUCCESS';
    } else {
      if(sizeof($success)>0)
        $result['status'] = 'OK';
      else
        $result['status'] = 'ERROR';
    }
  } else {
    $result['status'] = 'UNVALID';
  }
  echo json_encode($result);
?>
