<?php

	class ClientBitesController extends Controller {

		public $view;

		function init() {
			//
		}

		function indexAction() {
			global $site;
			$request = $site->mvc->getRequest();
			# Get parameters
			$page = $request->get('page', '1');
			$show = $request->get('show', '15');
			$user = $request->get('user', null);
			# Sanitize
			$page = is_numeric($page) ? $page : 1;
			$show = is_numeric($show) ? $show : 15;
			$offset = $show * ($page - 1);
			# Set pagination options
			Pagination::$show = $show;
			# Get filtered? and paged bites
			if ( $user ) {
				$bites = Bites::rawWhere("type = 'public' AND user_id = {$user}", $offset, $show);
				$total = Bites::count("type = 'public' AND user_id = {$user}");
			} else {
				$bites = Bites::rawWhere("type = 'public' OR user_id = {$site->user->id}", $offset, $show);
				$total = Bites::count("type = 'public' OR user_id = {$site->user->id}");
			}
			# Render paged bites
			$this->view->render('bites/index-page', array('bites' => $bites, 'total' => $total, 'user' => $user));
		}

		function newAction() {
			global $site;
			$request = $site->mvc->getRequest();
			switch ($request->type) {
				case 'get':
						$this->view->render('bites/new-page');
					break;
				case 'post':
					# Get parameters
					$token = $request->post('token');
					$name = $request->post('name');
					$content = $request->post('content');
					$status = $request->post('status');
					$type = $request->post('type');
					$permissions = $request->post('permissions');
					$syntax = $request->post('syntax');
					$description = $request->post('description');
					# Validate anti-csrf token
					if (! $site->csrf->checkToken($token) ) {
						$site->errorMessage('Invalid request data');
						exit;
					}
					# Validate fields
					$validator = Validator::newInstance()
						->addRule('name', $name)
						->addRule('content', $content)
						->validate();
					if (! $validator->isValid() ) {
						$site->errorMessage( 'The following fields are required: ' . implode( ',', $validator->getErrors() ) );
						exit;
					}
					# Create new bite
					$bite = new Bite();
					$bite->user_id = $site->user->id;
					$bite->status = $status;
					$bite->type = $type;
					$bite->permissions = $permissions;
					$bite->syntax = $syntax;
					$bite->name = $name;
					$bite->content = htmlspecialchars($content);
					$bite->save();
					# Save metas
					$bite->updateMeta('description', $description);
					# And redirect
					$site->redirectTo( $site->urlTo("/bites/{$bite->id}") );
					exit;
					break;
			}
		}

		function editAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			$bite = Bites::get($id);
			switch ($request->type) {
				case 'get':
						$this->view->render('bites/edit-page', array('bite' => $bite));
					break;
				case 'post':
					# Get parameters
					$token = $request->post('token');
					$name = $request->post('name');
					$content = $request->post('content');
					$status = $request->post('status');
					$type = $request->post('type');
					$permissions = $request->post('permissions');
					$syntax = $request->post('syntax');
					$description = $request->post('description');
					# Validate anti-csrf token
					if (! $site->csrf->checkToken($token) ) {
						$site->errorMessage('Invalid request data');
						exit;
					}
					# Validate fields
					$validator = Validator::newInstance()
						->addRule('name', $name)
						->addRule('content', $content)
						->validate();
					if (! $validator->isValid() ) {
						$site->errorMessage( 'The following fields are required: ' . implode( ',', $validator->getErrors() ) );
						exit;
					}
					# Update bite
					$bite->status = $status;
					$bite->type = $type;
					$bite->permissions = $permissions;
					$bite->syntax = $syntax;
					$bite->name = $name;
					$bite->content = htmlspecialchars($content);
					$bite->save();
					# Save metas
					$bite->updateMeta('description', $description);
					# And redirect
					$site->redirectTo( $site->urlTo("/bites/{$bite->id}") );
					exit;
			}
		}

		function showAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			$bite = Bites::get($id);
			switch ($request->type) {
				case 'get':
						$this->view->render('bites/show-page', array('bite' => $bite));
					break;
			}
		}

		function embedAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			$bite = Bites::get($id);
			switch ($request->type) {
				case 'get':
						$this->view->render('bites/embed-page', array('bite' => $bite));
					break;
			}
		}

	}

?>