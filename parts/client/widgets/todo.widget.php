<?php
	$series = array(
		array(
			'type' => 'line',
			'dataPoints' => array()
		),
		array(
			'type' => 'line',
			'dataPoints' => array()
		)
	);
	//
	$days = 7;
	for ($d = $days - 1; $d >= 0; $d--) {
		$time = strtotime("-{$d} days");
		$date = date('Y-m-d', $time);
		$series[0]['dataPoints'][] = array(
			'x' => $days - $d,
			'xValueFormatString' => sprintf('"%s"', date('F j', $time)),
			'yValueFormatString' => '#0 Task(s)',
			'y' => (int) ToDos::count("DATE(created) = '{$date}' AND status = 'Pending'")
		);
		$series[1]['dataPoints'][] = array(
			'x' => $days - $d,
			'xValueFormatString' => sprintf('"%s"', date('F j', $time)),
			'yValueFormatString' => '#0 Task(s)',
			'y' => (int) ToDos::count("DATE(created) = '{$date}' AND status = 'Done'")
		);
	}
?>
	<div class="panel panel-default">
		<div class="panel-heading">ToDo</div>
		<div class="panel-body">
			<div id="todo-chart" data-theme="theme2" data-chart="line" style="height: 200px; width: 100%;">
				<textarea><?php echo json_encode($series) ?></textarea>
			</div>
		</div>
	</div>