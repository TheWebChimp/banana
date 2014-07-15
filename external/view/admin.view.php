<?php

	class AdminView extends View {

		/**
		 * Initialization callback
		 * @return nothing
		 */
		function init() {
			global $site;
			$this->pages_dir = $site->baseDir('/pages/admin');
			$this->parts_dir = $site->baseDir('/pages/admin');
			# Load admin styles and scripts
			$site->enqueueStyle('banana.admin');
			$site->enqueueScript('banana.admin');
		}
	}

?>