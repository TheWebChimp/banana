<?php
	/**
	 * functions.inc.php
	 * Add extra functions in this file
	 */

	# Basic set-up ---------------------------------------------------------------------------------

	# Include styles
	$site->registerStyle('reset', $site->baseUrl('/css/reset.css') );
	$site->registerStyle('plugins', $site->baseUrl('/css/plugins.css') );
	$site->registerStyle('chimplate', $site->baseUrl('/css/chimplate.css') );
	$site->registerStyle('sticky-footer', $site->baseUrl('/css/sticky-footer.css') );
	$site->registerStyle('mobile', $site->baseUrl('/css/mobile.css'), array('reset', 'plugins', 'chimplate', 'sticky-footer') );
	$site->registerStyle('desktop', $site->baseUrl('/css/desktop.css'), array('mobile') );
	$site->enqueueStyle('desktop');

	# Include scripts
	$site->registerScript('plugins', $site->baseUrl('/js/plugins.js'), array('jquery') );
	$site->registerScript('ladybug', $site->baseUrl('/js/ladybug.min.js'), array('jquery', 'underscore') );
	$site->registerScript('banana', $site->baseUrl('/js/banana.js'), array('plugins', 'ladybug') );
	$site->enqueueScript('banana');

	# Include extra files
	include $site->baseDir('/external/utilities.inc.php');
	include $site->baseDir('/external/ajax.inc.php');

	# Meta tags
	$site->addMeta('UTF-8', '', 'charset');
	$site->addMeta('viewport', 'width=device-width, initial-scale=1');

	$site->addMeta('og:title', $site->getPageTitle(), 'property');
	$site->addMeta('og:site_name', $site->getSiteTitle(), 'property');
	$site->addMeta('og:description', $site->getSiteTitle(), 'property');
	$site->addMeta('og:image', $site->urlTo('/favicon.png'), 'property');
	$site->addMeta('og:type', 'website', 'property');
	$site->addMeta('og:url', $site->urlTo('/'), 'property');

	# Pages
	// $site->addPage('sample', 'sample-page');

?>