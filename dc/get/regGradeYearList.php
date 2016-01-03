<?php
  $term = $_REQUEST['term'];
  $year = $_REQUEST['year'];
  $strSQL = sprintf(
      "
      SELECT
        regsub.gradeYear
      FROM
        `register-subject` regsub
        INNER JOIN
        `subject` sub
        ON
        regsub.subjectID = sub.subjectID
      WHERE
        registerID = (
          SELECT
            registerID
          FROM
            `registerinfo`
          WHERE
            term = '%s' AND
            year = '%s'
        )
      GROUP BY
        regsub.gradeYear
      ORDER BY
        regsub.gradeYear ASC
      ",
      mysql_real_escape_string($term),
      mysql_real_escape_string($year)
  );
  $objQuery = mysql_query($strSQL);
  if($objQuery&&mysql_num_rows($objQuery)>0){
    while($row = mysql_fetch_array($objQuery)){
      $result.= '<option value="'.$row['gradeYear'].'">'.getGradeYearName($row['gradeYear']).'</option>';
    }
  } else {
    $result = '<option value="0">ไม่พบชั้นปี</optino>';
  }
  echo $result;
?>
