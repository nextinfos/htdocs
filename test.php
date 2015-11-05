<?php
// 	if(date('Y-m-d H:i:s',strtotime('2015-05-02 18:18:04 +15 minute'))>date('Y-m-d H:i:s')) $status = 1; else $status = 2;
// 	echo $status;
// $date = date('m-d');
// $date=date('m-d', strtotime("06/05"));;
// // echo $paymentDate; // echos today!
// $term1Begin = date('m-d', strtotime("05/18"));
// $term1End = date('m-d', strtotime("10/10"));
// $term2Begin = date('m-d', strtotime("11/02"));
// $term2End = date('m-d', strtotime("03/25"));
// if (($date > $term1Begin) && ($date < $term1End)) {
// 	echo "Term 1";
// } elseif(($date > $term2Begin) && ($date < $term2End)) {
// 	echo "Term 2";
// } else {
// 	echo "Term 3";
// }
// function randomPassword($l=8) {
// 	$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
// 	$pass = array(); //remember to declare $pass as an array
// 	$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
// 	for ($i = 0; $i < $l; $i++) {
// 		$n = rand(0, $alphaLength);
// 		$pass[] = $alphabet[$n];
// 	}
// 	return implode($pass); //turn the array into a string
// }
// echo randomPassword(6);
$date = "2015-11-04 13:04:27";
echo date("N",strtotime($date));
?>
<script>
new Morris.Line({
  // ID of the element in which to draw the chart.
  element: 'myfirstchart',
  // Chart data records -- each entry in this array corresponds to a point on
  // the chart.
  <?php echo $data;?>
  // The name of the data record attribute that contains x-values.
  xkey: 'year',
  // A list of names of data record attributes that contain y-values.
  ykeys: ['value'],
  // Labels for the ykeys -- will be displayed when you hover over the
  // chart.
  labels: ['Value']
});
</scrpit>