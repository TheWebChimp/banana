<?php

	class AdminController extends Controller {

		function init() {
			global $site;
			# Create view object
			$this->view = new AdminView();
			# Make sure caching is not a problem
			header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
			header('Pragma: no-cache'); // HTTP 1.0.
			header('Expires: 0'); // Proxies.
		}

		function checkUser() {
			global $site;
			$request = $site->mvc->getRequest();
			Users::requireLogin('/admin/login');
			if (! Users::currentUserCan('manage_options') ) {
				$site->redirectTo( $site->urlTo('/dashboard') );
				exit;
			}
		}

		function indexAction() {
			global $site;
			$request = $site->mvc->getRequest();
			$this->checkUser();
			$this->view->render('index-page');
		}

		function loginAction() {
			global $site;
			$request = $site->mvc->getRequest();
			switch ($request->type) {
				case 'get':
					$this->view->render('login-page');
					break;
				case 'post':
					$user = $request->post('user');
					$password = $request->post('password');
					$remember = $request->post('remember');
					if ( Users::login($user, $password, $remember) ) {
						$user = Users::getCurrentUser();
						if ($user->role == 'user') {
							Users::logout();
							$site->redirectTo( $site->urlTo('/admin/login?err=200') );
							exit;
						} else {
							$site->redirectTo( $site->urlTo('/admin') );
							exit;
						}
					} else {
						$site->redirectTo( $site->urlTo('/admin/login?err=100') );
						exit;
					}
					break;
			}
		}

		function logoutAction() {
			global $site;
			Users::logout();
			$site->redirectTo( $site->urlTo('/admin/login') );
			exit;
		}

		function showAction($id) {
			global $site;
			$this->checkUser();
			$request = $site->mvc->getRequest();
			$response = $site->mvc->getResponse();
			$controller = ucfirst($id);
			$extra = isset( $request->parts[2] ) ? explode( '/', $request->parts[2] ) : array();
			$action = isset( $extra[0] ) ? $extra[0] : 'index';
			$id = isset( $extra[1] ) ? $extra[1] : '';
			$controllerClass = "Admin{$controller}Controller";
			$controllerClass = str_replace(' ', '', ucwords(str_replace('-', ' ', $controllerClass)));
			$site->addBodyClass('admin');
			if ( class_exists($controllerClass) ) {
				$instance = new $controllerClass;
				$instance->view = $this->view;
				$method = "{$action}Action";
				$method = str_replace(' ', '', ucwords(str_replace('-', ' ', $method)));
				$alias = $instance->getActionAs($action);
				$method = method_exists($instance, $method) ? $method : ($alias ? $alias : 'showAction');  // check existing methods, then check aliases, then default to 'show'
				# Check action
				if ($method == 'showAction' && $method != $action) {
					$id = $action;
					$action = 'show';
				}
				# Check format
				$matches = null;
				if ( preg_match('/(\w+)\.(\w+)$/', $id, $matches) === 1 ) {
					$id = $matches[1];
					$request->format = $matches[2];
				}
				if ( method_exists($instance, $method) ) {
					# Modify request object
					$request->controller = strtolower($controller);
					$request->action = $action;
					$request->id = $id;
					#
					ob_start();
					$instance->$method($id);
					$response->write( ob_get_clean() );
				} else {
					$site->errorMessage("Router error: method '{$method}' from '{$controllerClass}' class does not exist.");
					return true;
				}
			} else {
				$site->errorMessage("Router error: '{$controllerClass}' class does not exist.");
				return true;
			}
		}
	}

?>