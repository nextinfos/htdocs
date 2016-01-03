<?php
  $tag = $_REQUEST['tag'];
  $subjectID = $_REQUEST['subjectID'];
  $normalize = $_REQUEST['normalize'];
  if($tag=='info'){
    $strSQL = sprintf(
        "
        SELECT
          maxScore,
          type
        FROM
          scoreinfo
        WHERE
          subjectID = '%s' AND
          registerID =
          (
            SELECT
              registerID
            FROM
              registerinfo
            WHERE
              term = '%s' AND
              year = '%s'
          )
        ",
        mysql_real_escape_string($subjectID),
        mysql_real_escape_string(getTerm()),
        mysql_real_escape_string(getYear())
    );
    $objQuery = mysql_query($strSQL);
    if($objQuery&&mysql_num_rows($objQuery)>0){
      $foundMid = false;
      $beforeMidScore = 0;
      $midScore = 0;
      $afterMidScore = 0;
      $finalScore = 0;
      while($row=mysql_fetch_assoc($objQuery)){
        if($row['type']!='EXAM'){
          if(!$foundMid){
            $beforeMidScore += $row['maxScore'];
          } else {
            $afterMidScore += $row['maxScore'];
          }
        } else {
          if(!$foundMid){
            $midScore += $row['maxScore'];
            $foundMid = true;
          } else {
            $finalScore += $row['maxScore'];
          }
        }
      }
      $return['beforeMidScore'] = $beforeMidScore;
      $return['midScore'] = $midScore;
      $return['afterMidScore'] = $afterMidScore;
      $return['finalScore'] = $finalScore;
      if($beforeMidScore>0&&$midScore>0&&$afterMidScore>0&&$finalScore>0){
        $return['status'] = 'SUCCESS';
        $maxScore = ($beforeMidScore+$midScore+$afterMidScore+$finalScore);
        $return['maxScore'] = $maxScore;
      } else {
        $return['status'] = 'INVALID';
      }
    } else {
      if($objQuery){
        $return['status'] = 'INVALID';
      } else {
        $return['status'] = 'ERROR';
      }
    }
    $return['subjectID'] = $subjectID;
  } elseif($tag=='preview'){
    $max = $_REQUEST['max'];
    if($subjectID&&$max){
      $max = json_decode($max);
      $maxPass = true;
      for($i=0;$i<sizeof($max);$i++){
        if($max[$i]==''||$max[$i]==null||!isset($max[$i])){
          $maxPass = false;
          break;
        }
      }
      if($maxPass){
        $maxScore = $max[7];
        $strSQL = sprintf(
            "
            SELECT
              stu.studentID,stu.firstName,stu.lastName,SUM(stusco.score) AS score
            FROM
              `studentscore` stusco RIGHT JOIN `student` stu ON stusco.studentID = stu.studentID
            WHERE
              stusco.scoreID IN
              (
              SELECT
                scoreID
              FROM
                scoreinfo
              WHERE
                subjectID = '%s' AND
                registerID =
                (
                  SELECT
                    registerID
                  FROM
                    registerinfo
                  WHERE
                    term = '%s' AND
                    year = '%s'
                )
              ) OR (stu.studentID IN (
                SELECT
                  studentID
                FROM
                  `register-student` regstu
                WHERE
                  regstu.subjectID = '%s' AND
                  regstu.registerID =
                  (
                    SELECT
                      registerID
                    FROM
                      registerinfo
                    WHERE
                      term = '%s' AND
                      year = '%s'
                  )
              ) AND stusco.subjectID IS NULL
            )
            GROUP BY
              studentID
            ",
            mysql_real_escape_string($subjectID),
            mysql_real_escape_string(getTerm()),
            mysql_real_escape_string(getYear()),
            mysql_real_escape_string($subjectID),
            mysql_real_escape_string(getTerm()),
            mysql_real_escape_string(getYear()),
            mysql_real_escape_string($subjectID)
        );
        $objQuery = mysql_query($strSQL);
        if($objQuery&&mysql_num_rows($objQuery)){
          while($row=mysql_fetch_assoc($objQuery)){
            $preData = $row;
            if($row['score']=='') $preData['score'] = 0;
            $grade = gradeCal(0,$max,0,$row['score']);
            $select = '<select name="grade" style="width: 80px;">';
  // 							$select.= '<option value="">--</option>';
            $select.= '<option value="'.$grade.'" selected>'.$grade.'</option>';
            $select.= '<option value="W">W</option>';
            $select.= '</select>';
            $preData['grade'] = '<input type="hidden" name="studentID" value="'.$row['studentID'].'">'.$select;
            $preData['score'] = $preData['score'].'/'.$maxScore;
            $return['data'][] = $preData;
          }
          $return['status'] = 'SUCCESS';
        } else {
          $return['status'] = 'FAIL';
          $return['strSQL'] = $strSQL;
        }
      } else {
        $return['status'] = 'MAXP';
        $return['data'] = '';
      }
    } else {
      $return['data'] = '';
    }
  }
  echo json_encode($return);
?>
