<?php

	include ABSPATH . '/external/model/users.model.php';

	class UsersView extends View {

		function init() {
			global $site;
			$this->setTemplatesDir( $site->baseDir('/pages/users') );
			$this->addTemplate('index', 'index-page');
			$this->addTemplate('login', 'login-page');
			$this->addTemplate('create', 'create-page');
			$this->addTemplate('edit', 'edit-page');
			$this->addTemplate('show', 'show-page');
		}

	}

	class UsersController extends Controller {
		protected $view;

		function init() {
			$this->view = new UsersView();
		}

		function index($id, $format, $type) {
			Users::checkSession();
			$this->view->render('index');
		}

		function login($id, $format, $type) {
			global $site;
			switch ($type) {
				case 'get':
					$this->view->render('login');
					break;
				case 'post':
					$usuario = isset($_POST['user']) ? $_POST['user'] : '';
					$password = isset($_POST['password']) ? $_POST['password'] : '';
					$recordar = isset($_POST['remember']) ? $_POST['remember'] : '';
					$ret = Users::login($usuario, $password, $recordar);
					if ($ret) {
						$site->redirectTo( $site->urlTo('/dashboard') );
					} else {
						$site->redirectTo( $site->urlTo('/users/login?msg=100') );
					}
					break;
			}
		}

		function logout($id, $format, $type) {
			//
		}

		function show($id, $format, $type) {
			//
		}

		function create($id, $format, $type) {
			Users::checkSession();
			$this->view->render('create');
		}

		function edit($id, $format, $type) {
			global $site;
			Users::checkSession();
			switch ($type) {
				case 'get':
					$this->view->render('edit', array('id' => $id));
					break;
				case 'post':
					$id = isset($_POST['id']) ? $_POST['id'] : 0;
					$token = isset($_POST['token']) ? $_POST['token'] : '';
					$email = isset($_POST['email']) ? $_POST['email'] : '';
					$first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
					$last_name = isset($_POST['last_name']) ? $_POST['last_name'] : '';
					$password = isset($_POST['password']) ? $_POST['password'] : '';
					$confirm = isset($_POST['confirm']) ? $_POST['confirm'] : '';
					$role = isset($_POST['role']) ? $_POST['role'] : '';
					$check = $site->hashToken( $_SESSION['csrf_secret'] );
					if ($token != $check) {
						$site->errorMessage('Invalid form data.');
					}
					if (! empty($password) && $password != $confirm ) {
						$site->redirectTo( $site->urlTo("/users/edit/{$id}?msg=110") );
						exit;
					}
					$data =  array(
						'email' => $email,
						'password' => $password,
						'role' => $role
					);
					# If the password is not set, you can't touch this (Hammer time!)
					if ($password == '') {
						unset( $data['password'] );
					}
					$ret = Users::update($id, $data);
					if ($ret) {
						Users::setMeta($id, 'first_name', $first_name);
						Users::setMeta($id, 'larst_name', $last_name);
						$site->redirectTo( $site->urlTo("/users/edit/{$id}?msg=200") );
						exit;
					}
					break;
			}
		}

		function delete($id, $format, $type) {
			Users::checkSession();
			die("Eliminar usuario {$id}");
		}

	}

	# Inicializamos las variables de la clase Users
	Users::init();

	# Finalmente, instanciamos el controlador
	$site->mvc->addController('users', 'UsersController');

?>