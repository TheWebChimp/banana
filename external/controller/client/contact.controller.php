<?php 

	class ClientContactController extends Controller {


		public $view;

		function init() {
			//
		}

		function indexAction() {
			global $site;
			$request = $site->mvc->getRequest();
			$contacts = Contacts::all();
			$this->view->render('contact/index-page', array('contacts' => $contacts));
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
	}




 ?>