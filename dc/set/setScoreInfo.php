<?php
  $subjectID = $_REQUEST['subjectID'];
  $scoreType = $_REQUEST['scoreType'];
  $scoreMax = $_REQUEST['scoreMax'];
  $scoreID = $_REQUEST['scoreID'];
  $addStatus = $_REQUEST['addStatus'];
  $date = date("Y-m-d H:i:s",time());
  if($addStatus=='1'){
    if($scoreType=='GRADE'){
      $data['status'] = 'SUCCESS';
      echo json_encode($data);
      exit();
    }
    $strSQL = sprintf(
      "
      INSERT INTO
        scoreinfo
        (
        SELECT
          NULL,
          '%s',
          registerID,
          '%s',
          '%s',
          '%s'
        FROM
          registerinfo
        WHERE
          term = '%s' AND
          year = '%s'
        )
      ",
      mysql_real_escape_string($subjectID),
      mysql_real_escape_string($date),
      mysql_real_escape_string($scoreType),
      mysql_real_escape_string($scoreMax),
      mysql_real_escape_string(getTerm()),
      mysql_real_escape_string(getYear())
    );
    $objQuery = mysql_query($strSQL);
    $scoreID = mysql_insert_id();
    $data['status'] = 'SUCCESS';
    $data['strSQL']=$strSQL;
    $data['scoreID'] = $scoreID;
  } elseif($addStatus=='2') {
    $strSQL = sprintf(
      "
      UPDATE
        `scoreinfo`
      SET
        type = '%s',
        maxScore = '%s'
      WHERE
        scoreID = '%s'
      ",
        mysql_real_escape_string($scoreType),
        mysql_real_escape_string($scoreMax),
        mysql_real_escape_string($scoreID)
    );
    $objQuery = mysql_query($strSQL);
    $data['status'] = 'SUCCESS';
    $data['strSQL']=$strSQL;
    $data['scoreID'] = $scoreID;
  } else {
    $data['status']='FAIL';
  }
echo json_encode($data);
?>
