<?php

	class SettingsView extends View {

		function init() {
			global $site;
			$this->setTemplatesDir( $site->baseDir('/pages/settings') );
			$this->addTemplate('index', 'index-page');
		}

	}

	class SettingsController extends Controller {
		protected $view;

		function init() {
			$this->view = new SettingsView();
		}

		function index($id, $format, $type) {
			Users::checkSession();
			$this->view->render('index');
		}

	}

	# Finalmente, instanciamos el controlador
	$site->mvc->addController('settings', 'SettingsController');

?>