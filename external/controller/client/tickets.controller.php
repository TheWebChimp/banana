<?php

	class ClientTicketsController extends Controller {

		public $view;

		function init() {
			//
		}

		function indexAction() {
			global $site;
			$request = $site->mvc->getRequest();
			$dbh = $site->getDatabase();
			//
			$filter = $request->param('filter', 'open');
			$sort = $request->param('sort', 'newest');
			$page = $request->param('page', '1');
			$show = $request->param('show', '15');
			$search = $request->param('search');
			$client_id = $request->param('client_id');
			$project_id = $request->param('project_id');
			//
			$search_str = $search ? $dbh->quote("%{$search}%") : '';
			//
			$page = is_numeric($page) ? $page : 1;
			$show = is_numeric($show) ? $show : 15;
			//
			$sort_opts = array(
				'newest' => 'created DESC',
				'oldest' => 'created ASC',
				'recently-updated' => 'modified DESC',
				'least-updated' => 'modified ASC',
				'most-replies' => 'replies DESC',
				'least-replies' => 'replies ASC'
			);
			//
			$sort = isset( $sort_opts[$sort] ) ? $sort : 'newest';
			$sort_order = get_item($sort_opts, $sort, 'created DESC');
			$params = explode(' ', $sort_order);
			//
			if ( Users::currentUserCan('manage_options') ) {
				$conditions = "status = '{$filter}'";
				$conditions .= $search_str ? " AND subject LIKE {$search_str}" : '';
				$conditions .= is_numeric($client_id) ? " AND client_id = {$client_id}" : '';
				$conditions .= is_numeric($project_id) ? " AND project_id = {$project_id}" : '';
				//
				$tickets = Tickets::rawWhere($conditions, $page, $show, $params[1], $params[0]);
				$total = Tickets::count($conditions);
			} else {
				$uid = Users::getCurrentUserId();
				$conditions = "status = '{$filter}' AND user_id = {$uid}";
				$conditions .= $search_str ? " AND subject LIKE {$search_str}" : '';
				$conditions .= is_numeric($client_id) ? " AND client_id = {$client_id}" : '';
				$conditions .= is_numeric($project_id) ? " AND project_id = {$project_id}" : '';
				//
				$tickets = Tickets::rawWhere($conditions, $page, $show, $params[1], $params[0]);
				$total = Tickets::count($conditions);
			}
			$this->view->render('tickets/index-page', array('tickets' => $tickets, 'client_id' => $client_id, 'project_id' => $project_id, 'search' => $search, 'page' => $page, 'show' => $show, 'filter' => $filter, 'sort' => $sort, 'total' => $total));
		}

		function calendarAction() {

			global $site;
			$request = $site->mvc->getRequest();
			// $dbh = $site->getDatabase();
			// //
			// $filter = $request->param('filter', 'open');
			// $sort = $request->param('sort', 'newest');
			// $page = $request->param('page', '1');
			// $show = $request->param('show', '15');
			// $search = $request->param('search');
			// $client_id = $request->param('client_id');
			// $project_id = $request->param('project_id');
			// //
			// $search_str = $search ? $dbh->quote("%{$search}%") : '';
			// //
			// $page = is_numeric($page) ? $page : 1;
			// $show = is_numeric($show) ? $show : 15;
			// //
			// $sort_opts = array(
			// 	'newest' => 'created DESC',
			// 	'oldest' => 'created ASC',
			// 	'recently-updated' => 'modified DESC',
			// 	'least-updated' => 'modified ASC',
			// 	'most-replies' => 'replies DESC',
			// 	'least-replies' => 'replies ASC'
			// );
			// //
			// $sort = isset( $sort_opts[$sort] ) ? $sort : 'newest';
			// $sort_order = get_item($sort_opts, $sort, 'created DESC');
			// $params = explode(' ', $sort_order);
			// //
			// if ( Users::currentUserCan('manage_options') ) {
			// 	$conditions = "status = '{$filter}'";
			// 	$conditions .= $search_str ? " AND subject LIKE {$search_str}" : '';
			// 	$conditions .= is_numeric($client_id) ? " AND client_id = {$client_id}" : '';
			// 	$conditions .= is_numeric($project_id) ? " AND project_id = {$project_id}" : '';
			// 	//
			// 	$tickets = Tickets::rawWhere($conditions, $page, $show, $params[1], $params[0]);
			// 	$total = Tickets::count($conditions);
			// } else {
			// 	$uid = Users::getCurrentUserId();
			// 	$conditions = "status = '{$filter}' AND user_id = {$uid}";
			// 	$conditions .= $search_str ? " AND subject LIKE {$search_str}" : '';
			// 	$conditions .= is_numeric($client_id) ? " AND client_id = {$client_id}" : '';
			// 	$conditions .= is_numeric($project_id) ? " AND project_id = {$project_id}" : '';
			// 	//
			// 	$tickets = Tickets::rawWhere($conditions, $page, $show, $params[1], $params[0]);
			// 	$total = Tickets::count($conditions);
			// }
			// $this->view->render('tickets/calendar-page', array('tickets' => $tickets, 'client_id' => $client_id, 'project_id' => $project_id, 'search' => $search, 'page' => $page, 'show' => $show, 'filter' => $filter, 'sort' => $sort, 'total' => $total));
			$tickets = Tickets::all();
			$events = array();
			foreach ($tickets as $ticket) {
				$start = $ticket->start ? $ticket->start : $ticket->created;
				$end = $ticket->due ? $ticket->due : $ticket->created;
				$events[] = array(
					'id' => $ticket->id,
					'title' => $ticket->subject,
					'url' => $site->urlTo("/tickets/{$ticket->id}"),
					'class' => 'event-important',
					'start' => strtotime($start) * 1000,
					'end' => strtotime($end) * 1000
				);
			}
			$site->addScriptVar('calendarEvents', $events);
			$this->view->render('tickets/calendar-page');
		}

		function showAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			$ticket = Tickets::get($id);
			if ($ticket) {
				$this->view->render('tickets/show-page', array('ticket' => $ticket));
			} else {
				$site->getPage('404');
			}
		}

		function editAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			$ticket = Tickets::get($id);
			switch ($request->type) {
				case 'get':
					$this->view->render('tickets/edit-page', array('ticket' => $ticket));
					break;
				case 'post':
					# Get parameters
					$token = $request->post('token');
					$subject = $request->post('subject');
					$details = $request->post('details');
					$start = $request->post('start');
					$due = $request->post('due');
					$client_id = $request->post('client_id');
					$project_id = $request->post('project_id');
					$attachments = $request->post('attachments', array());
					# Validate anti-csrf token
					if (! $site->csrf->checkToken($token) ) {
						$site->errorMessage('Invalid request data');
						exit;
					}
					# Validate fields
					$validator = Validator::newInstance()
						->addRule('subject', $subject)
						->addRule('details', $details)
						->validate();
					if (! $validator->isValid() ) {
						$site->errorMessage( 'The following fields are required: ' . implode( ',', $validator->getErrors() ) );
						exit;
					}
					# Update ticket
					$ticket->project_id = $project_id;
					$ticket->client_id = $client_id;
					$ticket->start = date( 'Y-m-d', strtotime( str_replace('/', '-', $start) ) );
					$ticket->due = date( 'Y-m-d', strtotime( str_replace('/', '-', $due) ) );
					$ticket->subject = $subject;
					$ticket->details = $details;
					$ticket->attachments = serialize($attachments);
					$ticket->save();
					# And redirect
					$site->redirectTo( $site->urlTo("/tickets/{$ticket->id}") );
					exit;
					break;
			}
		}

		function newAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			switch ($request->type) {
				case 'get':
					$this->view->render('tickets/new-page', array('ticket' => null));
					break;
				case 'post':
					# Get parameters
					$token = $request->post('token');
					$subject = $request->post('subject');
					$details = $request->post('details');
					$project_id = $request->post('project_id');
					$client_id = $request->post('client_id');
					$start = $request->post('start');
					$due = $request->post('due');
					$attachments = $request->post('attachments', array());
					# Validate anti-csrf token
					if (! $site->csrf->checkToken($token) ) {
						$site->errorMessage('Invalid request data');
						exit;
					}
					# Validate fields
					$validator = Validator::newInstance()
						->addRule('subject', $subject)
						->addRule('details', $details)
						->validate();
					if (! $validator->isValid() ) {
						$site->errorMessage( 'The following fields are required: ' . implode( ',', $validator->getErrors() ) );
						exit;
					}
					# Update ticket
					$ticket = new Ticket();
					$user = Users::getCurrentUser();
					$ticket->user_id =		$user->id;
					$ticket->project_id =	$project_id;
					$ticket->client_id =	$client_id;
					$ticket->start =		date( 'Y-m-d', strtotime( str_replace('/', '-', $start) ) );
					$ticket->due =			date( 'Y-m-d', strtotime( str_replace('/', '-', $due) ) );
					$ticket->subject =		$subject;
					$ticket->details =		$details;
					$ticket->attachments =	serialize($attachments);
					$ticket->status =		'Open';

					$ticket->save();
					# And redirect
					$site->redirectTo( $site->urlTo("/tickets/{$ticket->id}") );
					exit;
					break;
			}
		}

		function replyAction() {
			global $site;
			$request = $site->mvc->getRequest();
			switch ($request->type) {
				case 'post':
					# Get parameters
					$token = $request->post('token');
					$ticket_id = $request->post('ticket_id');
					$details = $request->post('details');
					$attachments = $request->post('attachments', array());
					# Validate anti-csrf token
					if (! $site->csrf->checkToken($token) ) {
						$site->errorMessage('Invalid request data');
						exit;
					}
					# Validate fields
					$validator = Validator::newInstance()
						->addRule('ticket_id', $ticket_id)
						->addRule('details', $details)
						->validate();
					if (! $validator->isValid() ) {
						$site->errorMessage( 'The following fields are required: ' . implode( ',', $validator->getErrors() ) );
						exit;
					}
					# Create new reply
					$user = Users::getCurrentUser();
					$reply = new TicketReply();
					$reply->user_id = $user->id;
					$reply->ticket_id = $ticket_id;
					$reply->details = $details;
					$reply->attachments = serialize($attachments);
					$reply->save();
					# Update ticket
					$ticket = Tickets::get($ticket_id);
					$ticket->update();
					# And redirect
					$site->redirectTo( $site->urlTo("/tickets/{$ticket_id}") );
					exit;
					break;
			}
		}

		function editReplyAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			$reply = Tickets::getReply($id);
			$ticket = Tickets::get($id);
			switch ($request->type) {
				case 'get':
					$this->view->render('tickets/edit-reply-page', array('ticket' => $ticket, 'reply' => $reply));
					break;
				case 'post':
					# Get parameters
					$token = $request->post('token');
					$ticket_id = $request->post('ticket_id');
					$details = $request->post('details');
					$attachments = $request->post('attachments', array());
					# Validate anti-csrf token
					if (! $site->csrf->checkToken($token) ) {
						$site->errorMessage('Invalid request data');
						exit;
					}
					# Validate fields
					$validator = Validator::newInstance()
						->addRule('ticket_id', $ticket_id)
						->addRule('details', $details)
						->validate();
					if (! $validator->isValid() ) {
						$site->errorMessage( 'The following fields are required: ' . implode( ',', $validator->getErrors() ) );
						exit;
					}
					# Create new reply
					$user = Users::getCurrentUser();
					$reply->details = $details;
					$reply->attachments = serialize($attachments);
					$reply->save();
					# Update ticket
					$ticket = Tickets::get($ticket_id);
					$ticket->update();
					# And redirect
					$site->redirectTo( $site->urlTo("/tickets/{$ticket_id}") );
					exit;
					break;
			}
		}

		function unreplyAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			$reply = Tickets::getReply($id);
			switch ($request->type) {
				case 'get':
					$this->view->render('tickets/unreply-page', array('reply' => $reply));
					break;
				case 'post':
					$token = $request->post('token');
					# Validate anti-csrf token
					if (! $site->csrf->checkToken($token) ) {
						$site->errorMessage('Invalid request data');
						exit;
					}
					#
					if ($reply) {
						# Update ticket
						$ticket = Tickets::get($reply->ticket_id);
						$ticket->update();
						//
						$reply->delete();
						$site->redirectTo( $site->urlTo("/tickets/{$reply->ticket_id}") );
					}
					break;
			}
		}

		function deleteAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			$ticket = Tickets::get($id);
			switch ($request->type) {
				case 'get':
					$this->view->render('tickets/delete-page', array('ticket' => $ticket));
					break;
				case 'post':
					$token = $request->post('token');
					# Validate anti-csrf token
					if (! $site->csrf->checkToken($token) ) {
						$site->errorMessage('Invalid request data');
						exit;
					}
					#
					if ($ticket) {
						$ticket->delete();
						$site->redirectTo( $site->urlTo("/tickets") );
					}
					break;
			}
		}

		function closeAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			$ticket = Tickets::get($id);
			switch ($request->type) {
				case 'get':
					$this->view->render('tickets/close-page', array('ticket' => $ticket));
					break;
				case 'post':
					$token = $request->post('token');
					# Validate anti-csrf token
					if (! $site->csrf->checkToken($token) ) {
						$site->errorMessage('Invalid request data');
						exit;
					}
					#
					if ($ticket) {
						$ticket->close();
						$site->redirectTo( $site->urlTo("/tickets/{$ticket->id}") );
					}
					break;
			}
		}

		function openAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			$ticket = Tickets::get($id);
			switch ($request->type) {
				case 'get':
					$this->view->render('tickets/open-page', array('ticket' => $ticket));
					break;
				case 'post':
					$token = $request->post('token');
					# Validate anti-csrf token
					if (! $site->csrf->checkToken($token) ) {
						$site->errorMessage('Invalid request data');
						exit;
					}
					#
					if ($ticket) {
						$ticket->open();
						$site->redirectTo( $site->urlTo("/tickets/{$ticket->id}") );
					}
					break;
			}
		}

		function uploadAction() {
			global $site;
			$request = $site->mvc->getRequest();
			$ret = new AjaxResponse();
			$file = $request->files('file');
			if ( $file ) {
				$attachment = Attachments::upload($file);
				if ($attachment) {
					$ret->attachment = $attachment;
					$ret->result = 'success';
				}
			}
			$ret->respond();
		}

		function detachAction() {
			global $site;
			$request = $site->mvc->getRequest();
			$token = $request->post('token');
			$id = $request->post('id');
			$ret = new AjaxResponse();
			# Validate anti-csrf token
			if (! $site->csrf->checkToken($token) ) {
				$ret->message = 'Invalid request data';
			} else {
				if ($id) {
					$attachment = Attachments::get($id);
					$attachment->delete();
					$ret->result = 'success';
				}
			}
			$ret->respond();
		}

		function labelAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			$ret = new AjaxResponse();
			$ret->respond();
		}

		function unlabelAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			$ret = new AjaxResponse();
			$ret->respond();
		}

		function addLabelAction() {
			global $site;
			$request = $site->mvc->getRequest();
			$ret = new AjaxResponse();
			$name = $request->post('label');
			$color = $request->post('color');
			$label = new TicketTag();
			$label->name = $name;
			$label->slug = $site->toAscii($name);
			$label->description = $color;
			$label->type = 'Label';
			$label->save();
			$ret->data = $label;
			$ret->respond();
		}

		function removeLabelAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			$ret = new AjaxResponse();
			$ret->respond();
		}

		function projectsAction() {
			global $site;
			$request = $site->mvc->getRequest();
			$dbh = $site->getDatabase();
			$client = $request->get('client');
			$client = is_numeric($client) ? $client : 0;
			$conditions = $client ? "client_id = {$client}" : 1;
			$ret = new AjaxResponse();
			//
			try {
				$sql = "SELECT id, slug, name FROM banana_project WHERE {$conditions}";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$rows = $stmt->fetchAll();
				$ret->result = 'success';
				$ret->data = $rows;
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			//
			$ret->respond();
		}

		function clientsAction() {
			global $site;
			$request = $site->mvc->getRequest();
			$dbh = $site->getDatabase();
			$conditions = 1;
			$ret = new AjaxResponse();
			//
			try {
				$sql = "SELECT id, slug, name FROM banana_client WHERE {$conditions}";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$rows = $stmt->fetchAll();
				$ret->result = 'success';
				$ret->data = $rows;
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			//
			$ret->respond();
		}

	}

?>