<?php

	class AdminClientsController extends Controller {

		public $view;

		function init() {
			//
		}

		function indexAction() {
			global $site;
			$request = $site->mvc->getRequest();
			$clients = Clients::all();
			$this->view->render('clients/index-page', array('clients' => $clients));
		}

		function newAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			switch ($request->type) {
				case 'get':
					$this->view->render('clients/new-page', array('client' => null));
					break;
				case 'post':
					# Get parameters
					$token = $request->post('token');
					$name = $request->post('name');
					$notes = $request->post('notes');
					$type = $request->post('type');
					$status = $request->post('status');
					# Validate anti-csrf token
					if (! $site->csrf->checkToken($token) ) {
						$site->errorMessage('Invalid request data');
						exit;
					}
					# Validate fields
					$validator = Validator::newInstance()
						->addRule('name', $name)
						// ->addRule('type', $type)
						->addRule('status', $status)
						->validate();
					if (! $validator->isValid() ) {
						$site->errorMessage( 'The following fields are required: ' . implode( ',', $validator->getErrors() ) );
						exit;
					}
					# Create new client
					$client = new Client();
					$client->name = $name;
					$client->notes = $notes;
					$client->slug = $site->toAscii($name);
					$client->type = $type;
					$client->status = $status;
					$client->save();
					# And redirect
					$site->redirectTo( $site->urlTo("/admin/clients/edit/{$client->id}") );
					exit;
					break;
			}
		}

		function editAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			$client = Clients::get($id);
			if (! $client ) {
				$site->errorMessage('The specified client does not exist');
				exit;
			}
			switch ($request->type) {
				case 'get':
					$this->view->render('clients/edit-page', array('client' => $client));
					break;
				case 'post':
					# Get parameters
					$token = $request->post('token');
					$name = $request->post('name');
					$notes = $request->post('notes');
					$type = $request->post('type');
					$status = $request->post('status');
					# Validate anti-csrf token
					if (! $site->csrf->checkToken($token) ) {
						$site->errorMessage('Invalid request data');
						exit;
					}
					# Validate fields
					$validator = Validator::newInstance()
						->addRule('name', $name)
						// ->addRule('type', $type)
						->addRule('status', $status)
						->validate();
					if (! $validator->isValid() ) {
						$site->errorMessage( 'The following fields are required: ' . implode( ',', $validator->getErrors() ) );
						exit;
					}
					# Update client
					$client->name = $name;
					$client->notes = $notes;
					$client->type = $type;
					$client->status = $status;
					$client->save();
					# And redirect
					$site->redirectTo( $site->urlTo("/admin/clients/edit/{$client->id}") );
					exit;
					break;
			}
		}

		function deleteAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			$this->view->render('clients/delete-page');
		}

	}

?>