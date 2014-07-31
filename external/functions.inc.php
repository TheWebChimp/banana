<?php
	/**
	 * functions.inc.php
	 * Add extra functions in this file
	 */

	# Basic set-up ------------------------------------------------------------

	# Include styles
	$site->registerStyle('webfont.ubuntu-mono', '//fonts.googleapis.com/css?family=Ubuntu+Mono:400,700,400italic,700italic' );
	$site->registerStyle('webfont.open-sans', '//fonts.googleapis.com/css?family=Open+Sans:400,400italic,700,700italic|Open+Sans+Condensed:700' );
	$site->registerStyle('webfont.font-awesome', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css' );
	$site->registerStyle('sticky-footer', $site->baseUrl('/css/sticky-footer.css') );
	$site->registerStyle('codemirror', $site->baseUrl('/css/codemirror.css') );
	$site->registerStyle('codemirror.neo', $site->baseUrl('/css/codemirror.neo.css') );
	$site->registerStyle('codemirror.monokai', $site->baseUrl('/css/codemirror.monokai.css') );
	$site->registerStyle('dropzone', $site->baseUrl('/css/dropzone.css') );
	$site->registerStyle('jquery.plugins', $site->baseUrl('/css/jquery.plugins.css') );
	$site->registerStyle('banana.client', $site->baseUrl('/css/banana.client.css'), array('codemirror', 'codemirror.monokai', 'codemirror.neo', 'twitter-bootstrap', 'webfont.open-sans', 'webfont.font-awesome', 'webfont.ubuntu-mono', 'sticky-footer', 'jquery.plugins') );
	$site->registerStyle('banana.admin', $site->baseUrl('/css/banana.admin.css'), array('codemirror', 'codemirror.monokai', 'codemirror.neo', 'twitter-bootstrap', 'dropzone', 'webfont.open-sans', 'webfont.font-awesome', 'webfont.ubuntu-mono', 'sticky-footer') );

	# Include scripts
	$site->registerScript('xtag', $site->baseUrl('/js/x-tag.min.js') );
	$site->registerScript('xtag-components', $site->baseUrl('/js/components.js'), array('xtag') );
	$site->registerScript('codemirror', $site->baseUrl('/js/codemirror.min.js') );
	$site->registerScript('marked', $site->baseUrl('/js/marked.min.js') );
	$site->registerScript('canvasjs', $site->baseUrl('/js/canvasjs.min.js') );
	$site->registerScript('class', $site->baseUrl('/js/class.js') );
	$site->registerScript('dropzone', $site->baseUrl('/js/dropzone.js'), array('jquery') );
	$site->registerScript('jquery-ui', $site->baseUrl('/js/jquery-ui-1.10.3.min.js'), array('jquery') );
	$site->registerScript('jquery.plugins', $site->baseUrl('/js/jquery.plugins.js'), array('jquery') );
	$site->registerScript('jquery.lazyload', $site->baseUrl('/js/jquery.lazyload.min.js'), array('jquery') );
	$site->registerScript('banana.client', $site->baseUrl('/js/banana.client.js'), array('codemirror', 'class', 'marked', 'jquery.plugins', 'canvasjs', 'jquery.lazyload', 'jquery.form', 'twitter-bootstrap') );
	$site->registerScript('banana.admin', $site->baseUrl('/js/banana.admin.js'), array('codemirror', 'class', 'jquery.plugins', 'canvasjs', 'jquery.lazyload', 'jquery.form', 'dropzone', 'jquery-ui', 'xtag-components', 'twitter-bootstrap') );

	# Include extra files
	include $site->baseDir('/external/utilities.inc.php');
	include $site->baseDir('/external/validator.inc.php');
	include $site->baseDir('/external/pagination.inc.php');
	include $site->baseDir('/external/cipher.inc.php');
	include $site->baseDir('/external/csrf.inc.php');
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

	# Models
	include $site->baseDir('/external/model/metadata.model.php');
	include $site->baseDir('/external/model/user.model.php');
	include $site->baseDir('/external/model/project.model.php');
	include $site->baseDir('/external/model/client.model.php');
	include $site->baseDir('/external/model/ticket.model.php');
	include $site->baseDir('/external/model/attachment.model.php');
	include $site->baseDir('/external/model/bite.model.php');
	include $site->baseDir('/external/model/keyring.model.php');
	include $site->baseDir('/external/model/todo.model.php');

	# Views
	include $site->baseDir('/external/view/client.view.php');
	include $site->baseDir('/external/view/admin.view.php');

	# Controllers
	include $site->baseDir('/external/controller/client.controller.php');
	include $site->baseDir('/external/controller/admin.controller.php');
	include $site->baseDir('/external/controller/admin/users.controller.php');
	include $site->baseDir('/external/controller/admin/clients.controller.php');
	include $site->baseDir('/external/controller/admin/projects.controller.php');
	include $site->baseDir('/external/controller/admin/attachments.controller.php');
	include $site->baseDir('/external/controller/client/todo.controller.php');
	include $site->baseDir('/external/controller/client/tickets.controller.php');
	include $site->baseDir('/external/controller/client/bites.controller.php');
	include $site->baseDir('/external/controller/client/keyring.controller.php');

	# MVC overrides
	$site->mvc->setDefaultController('client');

	# Localization
	if ( isset($i18n) ) {
		$i18n->addLocale('en', $site->baseDir('/lang/enUS.php'));
		$i18n->addLocale('es', $site->baseDir('/lang/esMX.php'));
		$i18n->setLocale('en');
	}

	# Restore user session (check for Users module first)
	if ( class_exists('Users') ) {
		Users::init();
		Users::checkLogin();
		if ( Users::getCurrentUserId() ) {
			$site->addBodyClass('session-active');
		}
		if (! Users::get(1) ) {
			$site->errorMessage('The database is not properly configured, there are no users on the system.');
			exit;
		}
		$site->user = Users::getCurrentUser();
	}

	# CSRF Protection
	$site->csrf = new CSRF();

?>