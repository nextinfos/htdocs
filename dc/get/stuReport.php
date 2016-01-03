<?php
  $report = $_REQUEST['report'];
  $studentID = $_REQUEST['studentID'];
  if($report=="atd"){
    $strSQL = sprintf(
    "
    SELECT
      regstu.subjectID,sub.name
    FROM
      `register-student` regstu,
      `subject` sub
    WHERE
      regstu.subjectID = sub.subjectID AND
      regstu.studentID = '%s' AND
      regstu.registerID =
      (
        SELECT
          registerID
        FROM
          `registerinfo`
        WHERE
          `term` = '%s' AND
          `year` = '%s'
      )
    ",
      mysql_real_escape_string($studentID),
      mysql_real_escape_string(getTerm()),
      mysql_real_escape_string(getYear())
    );
    $objQuery = mysql_query($strSQL);
    if($objQuery&&mysql_num_rows($objQuery)>0){
      while($row=mysql_fetch_array($objQuery)){
        $preData = NULL;
        $preData['subjectID'] = $row['subjectID'];
        $preData['subjectName'] = $row['name'];
        $strSQL = sprintf(
          "
          SELECT
            attendanceID
          FROM
            `attendanceinfo`
          WHERE
            subjectID = '%s' AND
            registerID =
            (
              SELECT
                registerID
              FROM
                `registerinfo`
              WHERE
                `term` = '%s' AND
                `year` = '%s'
            )
          ",
          mysql_real_escape_string($row['subjectID']),
          mysql_real_escape_string(getTerm()),
          mysql_real_escape_string(getYear())
        );
        $objQuery2 = mysql_query($strSQL);
        if($objQuery2&&mysql_num_rows($objQuery2)>0){
          $preData['total'] = mysql_num_rows($objQuery2);
          $check = 0;
          $late = 0;
          while($row2=mysql_fetch_array($objQuery2)){
            $strSQL = sprintf(
                "
                SELECT
                  status
                FROM
                  `studentattendance`
                WHERE
                  attendanceID = '%s' AND
                  subjectID = '%s' AND
                  studentID = '%s' AND
                  registerID =
                  (
                    SELECT
                      registerID
                    FROM
                      `registerinfo`
                    WHERE
                      `term` = '%s' AND
                      `year` = '%s'
                  )
                ",
                mysql_real_escape_string($row2['attendanceID']),
                mysql_real_escape_string($row['subjectID']),
                mysql_real_escape_string($studentID),
                mysql_real_escape_string(getTerm()),
                mysql_real_escape_string(getYear())
            );
            $objQuery3 = mysql_query($strSQL);
            if($objQuery3&&mysql_num_rows($objQuery3)>0){
              while($row3=mysql_fetch_array($objQuery3)){
                if($row3['status']=="ONTIME")
                  $check++;
                elseif($row3['status']=="LATE")
                  $late++;
              }
            }
          }
          $preData['check'] = $check;
          $preData['late'] = $late;
          $preData['abs'] = ($preData['total']-($preData['check']+$preData['late']));
          $preData['percent'] = @round(($preData['check']+$preData['late'])/$preData['total']*100,2).'%';
          $data['data'][] = $preData;
        } else {
          $preData['check'] = '--';
          $preData['late'] = '--';
          $preData['abs'] = '--';
          $preData['total'] = '--';
          $preData['percent'] = '--';
          $data['data'][] = $preData;
        }
      }
    } else {
      $data['data'] == NULL;
    }
  } elseif($report=="score"){
    $strSQL = sprintf(
        "
    SELECT
      regstu.subjectID,sub.name,regstu.grade
    FROM
      `register-student` regstu,
      `subject` sub
    WHERE
      regstu.subjectID = sub.subjectID AND
      regstu.studentID = '%s' AND
      regstu.registerID =
      (
        SELECT
          registerID
        FROM
          `registerinfo`
        WHERE
          `term` = '%s' AND
          `year` = '%s'
      )
    ",
        mysql_real_escape_string($studentID),
        mysql_real_escape_string(getTerm()),
        mysql_real_escape_string(getYear())
    );
    $objQuery = mysql_query($strSQL);
    if($objQuery&&mysql_num_rows($objQuery)>0){
      while($row=mysql_fetch_array($objQuery)){
        $preData['grade'] = $row['grade']!=''?$row['grade']:'--';
        $preData['subjectID'] = $row['subjectID'];
        $preData['subjectName'] = $row['name'];
        $strSQL = sprintf(
            "
          SELECT
            scoreID,maxScore
          FROM
            `scoreinfo`
          WHERE
            subjectID = '%s' AND
            registerID =
            (
              SELECT
                registerID
              FROM
                `registerinfo`
              WHERE
                `term` = '%s' AND
                `year` = '%s'
            )
          ",
            mysql_real_escape_string($row['subjectID']),
            mysql_real_escape_string(getTerm()),
            mysql_real_escape_string(getYear())
        );
        $objQuery2 = mysql_query($strSQL);
        if($objQuery2&&mysql_num_rows($objQuery2)>0){
          $maxScore = 0;
          $sumScore = 0;
          while($row2=mysql_fetch_array($objQuery2)){
            $maxScore += $row2['maxScore'];
            $strSQL = sprintf(
                "
                SELECT
                  score
                FROM
                  `studentscore`
                WHERE
                  scoreID = '%s' AND
                  subjectID = '%s' AND
                  studentID = '%s' AND
                  registerID =
                  (
                    SELECT
                      registerID
                    FROM
                      `registerinfo`
                    WHERE
                      `term` = '%s' AND
                      `year` = '%s'
                  )
                ",
                mysql_real_escape_string($row2['scoreID']),
                mysql_real_escape_string($row['subjectID']),
                mysql_real_escape_string($studentID),
                mysql_real_escape_string(getTerm()),
                mysql_real_escape_string(getYear())
            );
            $objQuery3 = mysql_query($strSQL);
            if($objQuery3&&mysql_num_rows($objQuery3)>0){
              while($row3=mysql_fetch_array($objQuery3)){
                $sumScore+=$row3['score'];
              }
            }
          }
          if($confUserType=='instructor'){
            $preData['score'] = $sumScore.'/'.$maxScore;
          }
          $data['data'][] = $preData;
        } else {
          $preData['score'] = '--';
          $data['data'][] = $preData;
        }
      }
    } else {
      $data['data'] == NULL;
    }
  }
  echo json_encode($data);
?>
