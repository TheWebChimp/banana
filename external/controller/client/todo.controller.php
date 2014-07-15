<?php

	class ClientTodoController extends Controller {

		public $view;

		function init() {
			//
		}

		function indexAction() {
			global $site;
			$request = $site->mvc->getRequest();
			$cat = ToDoTags::get(1);
			$todos = $cat->all();
			$this->view->render('todo/index-page', array('todos' => $todos, 'cat' => $cat->slug));
		}

		function showAction($id) {
			$cat = ToDoTags::get($id);
			$cat = $cat ? $cat : ToDoTags::get(1);
			$todos = $cat->all();
			$this->view->render('todo/index-page', array('todos' => $todos, 'cat' => $cat->slug));
		}

		function newAction() {
			global $site;
			$request = $site->mvc->getRequest();
			# Check user permissions
			if (! Users::currentUserCan('manage_options') ) {
				$site->redirectTo( $site->urlTo('/todo') );
			}
			switch ($request->type) {
				case 'get':
					$this->view->render('todo/new-page');
					break;
				case 'post':
					# Get parameters
					$token = $request->post('token');
					$name = $request->post('name');
					$category = $request->post('category');
					$details = $request->post('details');
					$project_id = $request->post('project_id');
					$client_id = $request->post('client_id');
					$status = $request->post('status');
					$priority = $request->post('priority');
					$deadline = $request->post('deadline');
					$attachments = $request->post('attachments', array());
					# Validate anti-csrf token
					if (! $site->csrf->checkToken($token) ) {
						$site->errorMessage('Invalid request data');
						exit;
					}
					# Validate fields
					$validator = Validator::newInstance()
						->addRule('name', $name)
						->validate();
					if (! $validator->isValid() ) {
						$site->errorMessage( 'The following fields are required: ' . implode( ',', $validator->getErrors() ) );
						exit;
					}
					# Create new task
					$user = Users::getCurrentUser();
					$todo = new ToDo();
					$todo->user_id = $user->id;
					$todo->project_id = $project_id;
					$todo->client_id = 0;
					$todo->name = $name;
					$todo->details = $details;
					$todo->attachments = serialize($attachments);
					$todo->status = 'Pending';
					$todo->priority = $priority;
					$todo->deadline = $deadline;
					$todo->save();
					# Set category
					TodoTags::setRelation($category, $todo->id);
					# And redirect
					$category = ToDoTags::get($category);
					$site->redirectTo( $site->urlTo("/todo/{$category->slug}") );
					exit;
					break;
			}
		}

		function editAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			# Check user permissions
			if (! Users::currentUserCan('manage_options') ) {
				$site->redirectTo( $site->urlTo('/todo') );
			}
			$todo = ToDos::get($id);
			switch ($request->type) {
				case 'get':
					$this->view->render('todo/edit-page', array('todo' => $todo));
					break;
				case 'post':
					# Get parameters
					$token = $request->post('token');
					$name = $request->post('name');
					$category = $request->post('category');
					$details = $request->post('details');
					$project_id = $request->post('project_id');
					$client_id = $request->post('client_id');
					$status = $request->post('status');
					$priority = $request->post('priority');
					$deadline = $request->post('deadline');
					$attachments = $request->post('attachments', array());
					# Validate anti-csrf token
					if (! $site->csrf->checkToken($token) ) {
						$site->errorMessage('Invalid request data');
						exit;
					}
					# Validate fields
					$validator = Validator::newInstance()
						->addRule('name', $name)
						->validate();
					if (! $validator->isValid() ) {
						$site->errorMessage( 'The following fields are required: ' . implode( ',', $validator->getErrors() ) );
						exit;
					}
					# Edit task
					$todo->project_id = $project_id;
					// $todo->client_id = 0;
					$todo->name = $name;
					$todo->details = $details;
					// $todo->attachments = serialize($attachments);
					$todo->status = $status;
					// $todo->priority = $priority;
					// $todo->deadline = $deadline;
					$todo->save();
					# Set category
					TodoTags::clearRelations($todo->id);
					TodoTags::setRelation($category, $todo->id);
					# And redirect
					$category = ToDoTags::get($category);
					$site->redirectTo( $site->urlTo("/todo/{$category->slug}") );
					exit;
					break;
			}
		}

		function deleteAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			# Check user permissions
			if (! Users::currentUserCan('manage_options') ) {
				$site->redirectTo( $site->urlTo('/todo') );
			}
			$todo = ToDos::get($id);
			switch ($request->type) {
				case 'get':
					$this->view->render('todo/delete-page', array('todo' => $todo));
					break;
				case 'post':
					$token = $request->post('token');
					# Validate anti-csrf token
					if (! $site->csrf->checkToken($token) ) {
						$site->errorMessage('Invalid request data');
						exit;
					}
					#
					if ($todo) {
						$category = $todo->getCategory();
						TodoTags::clearRelations($todo->id);
						$todo->delete();
						$site->redirectTo( $site->urlTo("/todo/{$category->slug}") );
					}
					break;
			}
		}

		function addCategoryAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			$token = $request->post('token');
			$name = $request->post('name');
			# Validate anti-csrf token
			if (! $site->csrf->checkToken($token) ) {
				$site->errorMessage('Invalid request data');
				exit;
			}
			#
			$category = new ToDoTag();
			$category->name = $name;
			$category->description = $name;
			$category->type = 'Category';
			$category->slug = $site->toAscii($name);
			$category->save();
			#
			$site->redirectTo( $site->urlTo("/todo/{$category->slug}") );
		}

		function deleteCategoryAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			switch ($request->type) {
				case 'post':
					$token = $request->post('token');
					# Validate anti-csrf token
					if (! $site->csrf->checkToken($token) ) {
						$site->errorMessage('Invalid request data');
						exit;
					}
					#
					$category = ToDoTags::get($id);
					if ($category) {
						# Move all to uncategorized
						ToDoTags::moveRelations($category->id, 1);
						$category->delete();
						if ( $site->isAjaxRequest() ) {
							echo json_encode( array('result' => 'success') );
							exit;
						}
					}
					break;
			}
			#
			$site->redirectTo( $site->urlTo("/todo") );
		}

	}

?>