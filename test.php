<?php
// 	if(date('Y-m-d H:i:s',strtotime('2015-05-02 18:18:04 +15 minute'))>date('Y-m-d H:i:s')) $status = 1; else $status = 2;
// 	echo $status;
// $date = date('m-d');
$date=date('m-d', strtotime("06/05"));;
// echo $paymentDate; // echos today!
$term1Begin = date('m-d', strtotime("05/18"));
$term1End = date('m-d', strtotime("10/10"));
$term2Begin = date('m-d', strtotime("11/02"));
$term2End = date('m-d', strtotime("03/25"));
if (($date > $term1Begin) && ($date < $term1End)) {
	echo "Term 1";
} elseif(($date > $term2Begin) && ($date < $term2End)) {
	echo "Term 2";
} else {
	echo "Term 3";
}
?>