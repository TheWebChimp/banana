<?php

	# Utility functions
	include $app->baseDir('/external/utilities.inc.php');

	# Sample routes

	// GET '/sample' ------------------------------------------------------------------------------
	function sample_route($params) {
		global $app;
		$result = new Payload();
		$response = $app->getResponse();
		$result->status = 'success';
		$result->route = 'sample_route';
		$response->setHeader('content-type', 'application/json');
		$response->setBody( $result->toJSON() );
		return $response->respond();
	}
	$app->addRoute('get', '/sample', 'sample_route');

	// POST '/sample' -----------------------------------------------------------------------------
	function sample_post_route($params) {
		global $app;
		$result = new Payload();
		$response = $app->getResponse();
		$result->status = 'success';
		$result->route = 'sample_post_route';
		$result->params = $_POST;
		$response->setHeader('content-type', 'application/json');
		$response->setBody( $result->toJSON() );
		return $response->respond();
	}
	$app->addRoute('post', '/sample', 'sample_post_route');

	// DELETE '/sample' ---------------------------------------------------------------------------
	function sample_delete_route($params) {
		global $app;
		$result = new Payload();
		$response = $app->getResponse();
		$result->status = 'success';
		$result->route = 'sample_delete_route';
		$result->params = $_REQUEST;
		$response->setHeader('content-type', 'application/json');
		$response->setBody( $result->toJSON() );
		return $response->respond();
	}
	$app->addRoute('delete', '/sample', 'sample_delete_route');

	// GET '/sample/with/:variable' ---------------------------------------------------------------
	function sample_variable_route($params) {
		global $app;
		$result = new Payload();
		$response = $app->getResponse();
		$result->status = 'success';
		$result->route = 'sample_variable_route';
		$result->params = $_REQUEST;
		$result->variable = get_item($params, 1, '--');
		$response->setHeader('content-type', 'application/json');
		$response->setBody( $result->toJSON() );
		return $response->respond();
	}
	$app->addRoute('GET', '/sample/with/:variable', 'sample_variable_route');

?>