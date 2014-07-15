<?php

	class ClientTicketsController extends Controller {

		public $view;

		function init() {
			//
		}

		function indexAction() {
			global $site;
			$request = $site->mvc->getRequest();
			//
			$filter = $request->get('filter', 'open');
			$sort = $request->get('sort', 'newest');
			$page = $request->get('page', '1');
			$show = $request->get('show', '15');
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
				$tickets = Tickets::rawWhere($conditions, $page, $show, $params[1], $params[0]);
				$total = Tickets::count($conditions);
			} else {
				$uid = Users::getCurrentUserId();
				$conditions = "status = '{$filter}' AND user_id = {$uid}";
				$tickets = Tickets::rawWhere($conditions, $page, $show, $params[1], $params[0]);
				$total = Tickets::count($conditions);
			}
			$this->view->render('tickets/index-page', array('tickets' => $tickets, 'page' => $page, 'show' => $show, 'filter' => $filter, 'sort' => $sort, 'total' => $total));
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

		function submitAction() {
			global $site;
			$request = $site->mvc->getRequest();
			switch ($request->type) {
				case 'post':
					# Get parameters
					$token = $request->post('token');
					$subject = $request->post('subject');
					$details = $request->post('details');
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
					# Create new ticket
					$user = Users::getCurrentUser();
					$ticket = new Ticket();
					$ticket->user_id = $user->id;
					$ticket->project_id = $project_id;
					$ticket->client_id = 0;
					$ticket->subject = $subject;
					$ticket->details = $details;
					$ticket->attachments = serialize($attachments);
					$ticket->status = 'Open';
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

	}

?>