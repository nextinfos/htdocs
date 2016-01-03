<?php
  $scoreID = $_REQUEST['scoreID'];
  $strSQL = sprintf(
    "
    SELECT
      type,maxScore
    FROM
      `scoreinfo`
    WHERE
      scoreID = '%s'
    LIMIT 1
    ",
      mysql_real_escape_string($scoreID)
  );
  $objQuery = mysql_query($strSQL);
  if($objQuery){
    $row = mysql_fetch_array($objQuery);
    $data['type'] = $row['type'];
    $data['maxScore'] = $row['maxScore'];
  } else {
    $data = NULL;
  }
  echo json_encode($data);
?>
