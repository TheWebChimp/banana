<?php

	class AjaxResponse {
		public $result;
		public $status;

		function __construct() {
			$this->result = 'error';
			$this->status = 200;
		}

		function __toString() {
			return json_encode($this);
		}

		function respond() {
			header("X-PHP-Response-Code: {$this->status}", true, $this->status);
			header('Content-Type: application/json');
			echo $this;
		}
	}

	# Sample AJAX action ------------------------------------------------------
	function ajax_test() {
		echo '<pre>'.print_r($_REQUEST, true).'</pre>';
		exit;
	}
	$site->addAjaxAction('test', 'ajax_test');

?>