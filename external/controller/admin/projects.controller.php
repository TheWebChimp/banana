<?php

	class AdminProjectsController extends Controller {

		public $view;

		function init() {
			//
		}

		function indexAction() {
			global $site;
			$request = $site->mvc->getRequest();
			$projects = Projects::all();
			$this->view->render('projects/index-page', array('projects' => $projects));
		}

		function newAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			switch ($request->type) {
				case 'get':
					$this->view->render('projects/new-page', array('project' => null));
					break;
				case 'post':
					# Get parameters
					$token = $request->post('token');
					$name = $request->post('name');
					$notes = $request->post('notes');
					$type = $request->post('type');
					$status = $request->post('status');
					$clients = $request->post('clients');
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
					# Create new project
					$project = new Project();
					$project->name = $name;
					$project->notes = $notes;
					$project->slug = $site->toAscii($name);
					$project->type = $type;
					$project->status = $status;
					$project->save();
					# Update metas
					$project->updateMeta('clients', $clients);
					# And redirect
					$site->redirectTo( $site->urlTo("/admin/projects/edit/{$project->id}") );
					exit;
					break;
			}
		}

		function editAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			$project = Projects::get($id);
			if (! $project ) {
				$site->errorMessage('The specified project does not exist');
				exit;
			}
			switch ($request->type) {
				case 'get':
					$this->view->render('projects/edit-page', array('project' => $project));
					break;
				case 'post':
					# Get parameters
					$token = $request->post('token');
					$name = $request->post('name');
					$notes = $request->post('notes');
					$type = $request->post('type');
					$status = $request->post('status');
					$clients = $request->post('clients');
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
					# Update project
					$project->name = $name;
					$project->notes = $notes;
					$project->type = $type;
					$project->status = $status;
					$project->save();
					# Update metas
					$project->updateMeta('clients', $clients);
					# And redirect
					$site->redirectTo( $site->urlTo("/admin/projects/edit/{$project->id}") );
					exit;
					break;
			}
		}

		function deleteAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			$this->view->render('projects/delete-page');
		}

	}

?>