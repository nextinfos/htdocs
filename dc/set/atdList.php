<?php
  if($atdID){
    $strSQL = sprintf(
      "
      SELECT
        subjectID,
        registerID,
        date
      FROM
        `attendanceinfo`
      WHERE
        attendanceID = '%s'
      ",
      mysql_real_escape_string($atdID)
    );
    $objQuery = mysql_query($strSQL);
    if($objQuery){
      $row = mysql_fetch_array($objQuery);
      $subjectID = $row['subjectID'];
      $registerID = $row['registerID'];
      $startDateTime = $row['date'];
      if($cardID||$studentID){
        if($cardID){
          $studentID = getStdFromCard($cardID);
        }
        if($studentID){
  // 						$stdIsReg = isStdRegis($studentID,$atdID);
          if(isStdRegis($studentID,$atdID)){
            $late = $_SESSION['atdLate'];
            if(!isCheckedIn($studentID, $atdID)){
              if(date('Y-m-d H:i:s',strtotime($startDateTime.' +'.$late.' minute 2 second'))>date('Y-m-d H:i:s',strtotime('Today '.$time))) $status = 'ONTIME'; else $status = 'LATE';
              $strSQL = "INSERT INTO `studentattendance` VALUES('$atdID','$studentID','$subjectID','$registerID','$status')";
              $objQuery = mysql_query($strSQL);
              if($objQuery){
                $data['status'] = "SUCCESS";
                $cardInfo = getCardInfo($studentID);
                $data['data'][] = array(
                    "responseText"=>"Added",
                    "studentID"=>$cardInfo
                );
                $data['atdData'] = array(
                  "id"=>$cardInfo['data'][0]['id'],
                  "name"=>$cardInfo['data'][0]['fname']."   ".$cardInfo['data'][0]['lname'],
                  "status"=>ucfirst(strtolower($status))
                );
              } else {
                $data['status'] = "FAIL";
                $data['data'][] = array(
                    "reason"=>"SaveFail",
                    "strSQL"=>$strSQL
                );
              }
            } else {
              $data['status'] = "FAIL";
              $data['data'][] = array(
                  "reason"=>"CheckedIn",
                  "strSQL"=>$strSQL
              );
            }
          } else {
            $data['status'] = "FAIL";
            $data['data'][] = array(
                "reason"=>"NotFoundReg",
                "strSQL"=>$strSQL
            );
          }
        } else {
          $data['status'] = "FAIL";
          $data['data'][] = array(
              "reason"=>"NotFoundCard",
              "cardID"=>$_POST['cardID']
          );
        }
      }
    } else {
      $data['status'] = "FAIL";
      $data['data'][] = array(
        "reason"=>"NotFoundAtd",
        "atdID"=>$atdID
      );
    }
  } else {
    $data['status'] = "FAIL";
    $data['data'][] = array("reason"=>"NotLogIn");
  }
  echo json_encode($data);
?>
