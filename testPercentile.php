<?php
function getPercentile($array){
	arsort($array);
	$i=0;
	$total = count($array);
	$percentiles = array();
	$previousValue = -1;
	$previousPercentile = -1;
	foreach ($array as $key => $value) {
		echo "\$array[$key] => $value<br/>";
	    if ($previousValue == $value) {
	    $percentile = $previousPercentile;
	    } else {
	    $percentile = 100 - $i*100/$total;
	    $previousPercentile = $percentile;
	    }
	    $percentiles[$key] = $percentile;
	    $previousValue = $value;
	    $i++;
	}
	return $percentiles;
}
$array = array(
		45=>5,
		42=>4.9,
		48=>5,
		41=>4.8,
		40=>4.9,
		34=>4.9,
);
print_r(getPercentile($array));
?>