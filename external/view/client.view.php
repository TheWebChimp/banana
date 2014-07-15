<?php

	class ClientView extends View {

		/**
		 * Initialization callback
		 * @return nothing
		 */
		function init() {
			global $site;
			$this->pages_dir = $site->baseDir('/pages/client');
			$this->parts_dir = $site->baseDir('/pages/client');
			# Load client styles and scripts
			$site->enqueueStyle('banana.client');
			$site->enqueueScript('banana.client');
		}
	}

?>