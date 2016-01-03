<?php
  $source=$_POST['source'];
  $data = $_POST['data'];
  if($source=="studentID"){
    $strSQL = 'SELECT studentID FROM students WHERE studentID LIKE "'.$data.'%"';
    $objQuery = mysql_query($strSQL);
    if($objQuery){
      $data = '[';
      while($row=mysql_fetch_array($objQuery)){
        $i++;
        if($i>1) $data .= ',';
        $data.= '{"label":"'.$row['studentID'].'"}';
      }
      echo $data.']';
    }
  }
?>
