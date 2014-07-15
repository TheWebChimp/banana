<?php

	class ClientController extends Controller {

		function init() {
			global $site;
			# Create view object
			$this->view = new ClientView();
			# Make sure caching is not a problem
			header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
			header('Pragma: no-cache'); // HTTP 1.0.
			header('Expires: 0'); // Proxies.
		}

		function checkUser() {
			global $site;
			$request = $site->mvc->getRequest();
			Users::requireLogin('/login');
			$user = Users::getCurrentUser();
			if ($user) {
				$site->addBodyClass('has-navbar');
			} else {
				$site->redirectTo( $site->urlTo('/login') );
				exit;
			}
		}

		function indexAction() {
			global $site;
			$request = $site->mvc->getRequest();
			$this->view->render('index-page');
		}

		function dashboardAction() {
			global $site;
			$request = $site->mvc->getRequest();
			$this->checkUser();
			$this->view->render('dashboard-page');
		}

		function profileAction() {
			global $site;
			$request = $site->mvc->getRequest();
			$this->checkUser();
			switch ($request->type) {
				case 'get':
					$this->view->render('profile-page');
					break;
				case 'post':
					$token = $request->post('token');
					$first_name = $request->post('first_name');
					$last_name = $request->post('last_name');
					$email = $request->post('email');
					$nickname = $request->post('nickname');
					$password = $request->post('password');
					$confirm = $request->post('confirm');
					# Validate anti-csrf token
					if (! $site->csrf->checkToken($token) ) {
						$site->errorMessage('Invalid request data');
						exit;
					}
					# Validate fields
					$validator = Validator::newInstance()
						->addRule('first_name', $first_name)
						->addRule('email', $email, 'email')
						->addRule('nickname', $nickname)
						->addRule('password', $confirm, 'equal', $password)
						->validate();
					if (! $validator->isValid() ) {
						$site->errorMessage( 'The following fields are required: ' . implode( ',', $validator->getErrors() ) );
						exit;
					}
					# Save user fields
					$site->user->email = $email;
					$site->user->nickname = $nickname;
					$site->user->password = $password ? $password : $site->user->password;
					$site->user->save();
					# Update user meta
					$site->user->updateMeta('first_name', $first_name);
					$site->user->updateMeta('last_name', $last_name);
					# Redirect
					$site->redirectTo( $site->urlTo('/profile?msg=200') );
					break;
			}
		}

		function showAction($id) {
			global $site;
			$this->checkUser();
			$page = $id;
			$request = $site->mvc->getRequest();
			$response = $site->mvc->getResponse();
			$controller = ucfirst($id);
			$extra = isset( $request->parts[2] ) ? explode( '/', $request->parts[2] ) : array();
			$action = isset( $extra[0] ) ? $extra[0] : 'index';
			$id = isset( $request->parts[3] ) ? $request->parts[3] : '';
			$controllerClass = "Client{$controller}Controller";
			$controllerClass = str_replace(' ', '', ucwords(str_replace('-', ' ', $controllerClass)));
			$site->addBodyClass('client');
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
				$site->getPage($page);
			}
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
						$site->redirectTo( $site->urlTo('/dashboard') );
						exit;
					} else {
						$site->redirectTo( $site->urlTo('/login?err=100') );
						exit;
					}
					break;
			}
		}

	}

?>