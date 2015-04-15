<?php

	# Utility functions
	include $app->baseDir('/external/utilities.inc.php');

	# Sample routes

	// GET '/test' ------------------------------------------------------------------------------
	function test_route($params) {
		global $app;
		$result = new Payload();
		$response = $app->getResponse();
		$result->status = 'success';
		$result->route = 'test_route';
		$response->setHeader('content-type', 'application/json');
		$response->setBody( $result->toJSON() );
		return $response->respond();
	}
	$app->addRoute('get', '/test', 'test_route');

?>