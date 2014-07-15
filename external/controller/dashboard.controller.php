<?php

	class DashboardView extends View {

		function init() {
			global $site;
			$this->setTemplatesDir( $site->baseDir('/pages/dashboard') );
			$this->addTemplate('index', 'index-page');
			$this->addTemplate('install', 'install-page');
		}

	}

	class DashboardController extends Controller {
		protected $view;

		function init() {
			$this->view = new DashboardView();
		}

		function index($id, $format, $type) {
			Users::checkSession();
			$this->view->render('index');
		}

		function install($id, $format, $type) {
			// Users::create('admin', 'Administrador', 'admin@mailinator.com', 'abre', 'admin', array('edit_options', 'create_users', 'edit_users', 'delete_users'));
			// $this->view->render('install');
		}

	}

	# Finalmente, instanciamos el controlador
	$site->mvc->addController('dashboard', 'DashboardController');

?>