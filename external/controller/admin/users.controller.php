<?php

	class AdminUsersController extends Controller {

		public $view;

		function init() {
			//
		}

		function indexAction() {
			global $site;
			$request = $site->mvc->getRequest();
			$users = Users::all();
			$this->view->render('users/index-page', array('users' => $users));
		}

		function newAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			switch ($request->type) {
				case 'get':
					$this->view->render('users/new-page', array('user' => null));
					break;
				case 'post':
					# Get parameters
					$token = $request->post('token');
					$status = $request->post('status');
					$role = $request->post('role');
					$first_name = $request->post('first_name');
					$last_name = $request->post('last_name');
					$email = $request->post('email');
					$nickname = $request->post('nickname');
					$password = $request->post('password');
					$confirm = $request->post('confirm');
					$clients = $request->post('clients');
					$projects = $request->post('projects');
					# Validate anti-csrf token
					if (! $site->csrf->checkToken($token) ) {
						$site->errorMessage('Invalid request data');
						exit;
					}
					# Validate fields
					$validator = Validator::newInstance()
						->addRule('status', $status)
						->addRule('role', $role)
						->addRule('first_name', $first_name)
						->addRule('last_name', $last_name)
						->addRule('email', $email, 'email')
						->addRule('nickname', $nickname)
						->addRule('password', $password)
						->addRule('confirm', $confirm, 'equal', $password)
						->validate();
					if (! $validator->isValid() ) {
						$site->errorMessage( 'The following fields are required: ' . implode( ',', $validator->getErrors() ) );
						exit;
					}
					# Create new user
					$user = new User();
					$user->login = $email;
					$user->email = $email;
					$user->status = $status;
					$user->password = $password;
					$user->nickname = $nickname;
					$user->save();
					# Set capabilities according to role
					switch ($role) {
						case 'Administrator':
							$capabilities = array('manage_options', 'edit_users', 'delete_users', 'edit_data', 'delete_data');
							break;
						case 'User':
							$capabilities = array('edit_data', 'delete_data');
							break;
					}
					# Set metas
					$user->updateMeta('capabilities', $capabilities);
					$user->updateMeta('first_name', $first_name);
					$user->updateMeta('last_name', $last_name);
					$user->updateMeta('role', $role);
					$user->updateMeta('clients', $clients);
					$user->updateMeta('projects', $projects);
					# And redirect
					$site->redirectTo( $site->urlTo("/admin/users/edit/{$user->id}") );
					exit;
					break;
			}
		}

		function editAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			$user = Users::get($id);
			if (! $user ) {
				$site->errorMessage('The specified user does not exist');
				exit;
			}
			switch ($request->type) {
				case 'get':
					$this->view->render('users/edit-page', array('user' => $user));
					break;
				case 'post':
					# Get parameters
					$token = $request->post('token');
					$status = $request->post('status');
					$role = $request->post('role');
					$first_name = $request->post('first_name');
					$last_name = $request->post('last_name');
					$email = $request->post('email');
					$nickname = $request->post('nickname');
					$password = $request->post('password');
					$confirm = $request->post('confirm');
					$clients = $request->post('clients');
					$projects = $request->post('projects');
					# Validate anti-csrf token
					if (! $site->csrf->checkToken($token) ) {
						$site->errorMessage('Invalid request data');
						exit;
					}
					# Validate fields
					$validator = Validator::newInstance()
						->addRule('status', $status)
						->addRule('role', $role)
						->addRule('first_name', $first_name)
						->addRule('email', $email, 'email')
						->addRule('nickname', $nickname)
						->addRule('confirm', $confirm, 'equal', $password)
						->validate();
					if (! $validator->isValid() ) {
						$site->errorMessage( 'The following fields are required: ' . implode( ',', $validator->getErrors() ) );
						exit;
					}
					# Update user
					$user->email = $email;
					$user->status = $status;
					$user->password = $password ? $password : $user->password;
					$user->nickname = $nickname;
					$user->save();
					# Set capabilities according to role
					switch ($role) {
						case 'Administrator':
							$capabilities = array('manage_options', 'edit_users', 'delete_users', 'edit_data', 'delete_data');
							break;
						case 'User':
							$capabilities = array('edit_data', 'delete_data');
							break;
					}
					# Set metas
					$user->updateMeta('capabilities', $capabilities);
					$user->updateMeta('first_name', $first_name);
					$user->updateMeta('last_name', $last_name);
					$user->updateMeta('role', $role);
					$user->updateMeta('clients', $clients);
					$user->updateMeta('projects', $projects);
					# And redirect
					$site->redirectTo( $site->urlTo("/admin/users/edit/{$user->id}") );
					exit;
					break;
			}
		}

		function deleteAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			$this->view->render('users/delete-page');
		}

		function showAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
		}

	}

?>