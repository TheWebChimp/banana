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
			'yValueFormatString' => '#0 Ticket(s)',
			'y' => (int) Tickets::count("DATE(created) = '{$date}' AND status = 'Open'")
		);
		$series[1]['dataPoints'][] = array(
			'x' => $days - $d,
			'xValueFormatString' => sprintf('"%s"', date('F j', $time)),
			'yValueFormatString' => '#0 Ticket(s)',
			'y' => (int) Tickets::count("DATE(created) = '{$date}' AND status = 'Closed'")
		);
	}
?>
	<div class="panel panel-default">
		<div class="panel-heading">Tickets</div>
		<div class="panel-body">
			<div id="tickets-chart" data-theme="theme2" data-chart="line" style="height: 200px; width: 100%;">
				<textarea><?php echo json_encode($series) ?></textarea>
			</div>
		</div>
	</div>