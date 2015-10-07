<?php
	$atdID = 1;
	$strSQL = 'SELECT COUNT(*) AS count FROM atdlist a INNER JOIN reg_student r ON a.regSubjectID = r.regSubjectID WHERE a.atdID = "'.$atdID.'"';
	$objQuery = mysql_query($strSQL);
	if($objQuery){
		$row = mysql_fetch_array($objQuery);
		$regStudent = $row['count'];
	}
	$strSQL = 'SELECT COUNT(*) AS count FROM atdinfo WHERE atdID = "'.$atdID.'"';
	$objQuery = mysql_query($strSQL);
	if($objQuery){
		$row = mysql_fetch_array($objQuery);
		$chkStudent = $row['count'];
	}
	$strSQL = 'SELECT COUNT(*) AS count FROM atdinfo a LEFT JOIN students s ON a.studentID = s.studentID WHERE atdID = "'.$atdID.'" AND s.gender = "M"';
	$objQuery = mysql_query($strSQL);
	if($objQuery){
		$row = mysql_fetch_array($objQuery);
		$mStudent = $row['count'];
	}
	echo $chkStudent.'/'.$regStudent;
?>
<style>
	.chart {
		height: 300px;
		width: 400px;
		background: rgba(255,255,255,0.5);
	}
	.canvasjs-chart-credit {
		visibility: hidden !important;
	}
</style>
<script type="text/javascript">
	$(function(){
		$( ".chart" ).addClass("ui-corner-all");
	});
window.onload = function () {
	CanvasJS.addColorSet("checkAbsent",
            [
            "#BCF5A9",
            "#F78181"          
            ]);
	CanvasJS.addColorSet("gender",
            [
            "#8181F7",
            "#F5A9E1"          
            ]);
	var absent = new CanvasJS.Chart("absent",
	{
		theme: "theme2",
		title:{
			text: "นักเรียนที่มาเรียนและขาดเรียน"
		},
		backgroundColor: "transparent",
		colorSet:  "checkAbsent",
		width: 400,
		legend: {
			maxWidth: 350,
			itemWidth: 120
		},
		data: [
		{
			type: "pie",
			showInLegend: true,
			legendText: "{indexLabel}",
			dataPoints: [
				{ y: <?php echo $chkStudent;?>, indexLabel: "มาเรียน" },
				{ y: <?php echo $regStudent-$chkStudent;?>, indexLabel: "ขาดเรียน" }
			]
		}
		]
	});
	absent.render();
	var checked = new CanvasJS.Chart("checked",
			{
				theme: "theme2",
				title:{
					text: "มาเรียนแบ่งตามเพศ"
				},
				backgroundColor: "transparent",
				colorSet:  "gender",
				width: 400,
				legend: {
					maxWidth: 350,
					itemWidth: 120
				},
				data: [
				{
					type: "pie",
					showInLegend: true,
					legendText: "{indexLabel}",
					dataPoints: [
						{ y: <?php echo $mStudent;?>, indexLabel: "นักเรียนชาย" },
						{ y: <?php echo $chkStudent-$mStudent;?>, indexLabel: "นักเรียนหญิง" }
					]
				}
				]
			});
			checked.render();
}
</script>
<div class="chart" id="absent"></div>
<div class="chart" id="checked"></div>