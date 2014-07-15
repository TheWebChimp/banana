<?php

	class ClientKeyringController extends Controller {

		public $view;

		function init() {
			//
		}

		function indexAction() {
			global $site;
			$request = $site->mvc->getRequest();
			$keyrings = Keyrings::all();
			$this->view->render('keyring/index-page', array('keyrings' => $keyrings));
		}

		function showAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			$keyring = Keyrings::get($id);
			if ($keyring) {
				$keys = KeyringKeys::where('keyring_id', $keyring->id);
				$this->view->render('keyring/show-page', array('keyring' => $keyring, 'keys' => $keys));
			} else {
				$site->getPage('404');
			}
		}

		function newAction() {
			global $site;
			$request = $site->mvc->getRequest();
			switch ($request->type) {
				case 'post':
					# Get parameters
					$token = $request->post('token');
					$name = $request->post('name');
					$type = $request->post('type');
					$description = $request->post('description');
					# Validate anti-csrf token
					if (! $site->csrf->checkToken($token) ) {
						$site->errorMessage('Invalid request data');
						exit;
					}
					# Validate fields
					$validator = Validator::newInstance()
						->addRule('name', $name)
						->addRule('type', $type)
						->validate();
					if (! $validator->isValid() ) {
						$site->errorMessage( 'The following fields are required: ' . implode( ',', $validator->getErrors() ) );
						exit;
					}
					# Create new keyring
					$keyring = new Keyring();
					$keyring->name = $name;
					$keyring->description = $description;
					$keyring->type = $type;
					$keyring->save();
					# And redirect
					$site->redirectTo( $site->urlTo("/keyring/{$keyring->id}") );
					exit;
					break;
			}
		}

		function addKeyAction() {
			global $site;
			$request = $site->mvc->getRequest();
			switch ($request->type) {
				case 'post':
					# Get parameters
					$token = $request->post('token');
					$name = $request->post('name');
					$keyring_id = $request->post('keyring_id');
					$description = $request->post('description');
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
					# Create new key
					$pass = $site->hashPassword('KEYRING_8:|NwvzafQKVq;pl1P3&');
					$cipher = new Cipher($pass);
					$key = new KeyringKey();
					$key->name = $name;
					$key->description = $cipher->encrypt($description);
					$key->keyring_id = $keyring_id;
					$key->save();
					# And redirect
					$site->redirectTo( $site->urlTo("/keyring/{$keyring_id}") );
					exit;
					break;
			}
		}

		// function replyAction() {
		// 	global $site;
		// 	$request = $site->mvc->getRequest();
		// 	switch ($request->type) {
		// 		case 'post':
		// 			# Get parameters
		// 			$token = $request->post('token');
		// 			$ticket_id = $request->post('ticket_id');
		// 			$details = $request->post('details');
		// 			$attachments = $request->post('attachments', array());
		// 			# Validate anti-csrf token
		// 			if (! $site->csrf->checkToken($token) ) {
		// 				$site->errorMessage('Invalid request data');
		// 				exit;
		// 			}
		// 			# Validate fields
		// 			$validator = Validator::newInstance()
		// 				->addRule('ticket_id', $ticket_id)
		// 				->addRule('details', $details)
		// 				->validate();
		// 			if (! $validator->isValid() ) {
		// 				$site->errorMessage( 'The following fields are required: ' . implode( ',', $validator->getErrors() ) );
		// 				exit;
		// 			}
		// 			# Create new reply
		// 			$user = Users::getCurrentUser();
		// 			$reply = new TicketReply();
		// 			$reply->user_id = $user->id;
		// 			$reply->ticket_id = $ticket_id;
		// 			$reply->details = $details;
		// 			$reply->attachments = serialize($attachments);
		// 			$reply->save();
		// 			# Update ticket
		// 			$ticket = Tickets::get($ticket_id);
		// 			$ticket->update();
		// 			# And redirect
		// 			$site->redirectTo( $site->urlTo("/tickets/{$ticket_id}") );
		// 			exit;
		// 			break;
		// 	}
		// }

		// function unreplyAction($id) {
		// 	global $site;
		// 	$request = $site->mvc->getRequest();
		// 	$reply = Tickets::getReply($id);
		// 	switch ($request->type) {
		// 		case 'get':
		// 			$this->view->render('tickets/unreply-page', array('reply' => $reply));
		// 			break;
		// 		case 'post':
		// 			$token = $request->post('token');
		// 			# Validate anti-csrf token
		// 			if (! $site->csrf->checkToken($token) ) {
		// 				$site->errorMessage('Invalid request data');
		// 				exit;
		// 			}
		// 			#
		// 			if ($reply) {
		// 				# Update ticket
		// 				$ticket = Tickets::get($reply->ticket_id);
		// 				$ticket->update();
		// 				//
		// 				$reply->delete();
		// 				$site->redirectTo( $site->urlTo("/tickets/{$reply->ticket_id}") );
		// 			}
		// 			break;
		// 	}
		// }

		// function deleteAction($id) {
		// 	global $site;
		// 	$request = $site->mvc->getRequest();
		// 	$ticket = Tickets::get($id);
		// 	switch ($request->type) {
		// 		case 'get':
		// 			$this->view->render('tickets/delete-page', array('ticket' => $ticket));
		// 			break;
		// 		case 'post':
		// 			$token = $request->post('token');
		// 			# Validate anti-csrf token
		// 			if (! $site->csrf->checkToken($token) ) {
		// 				$site->errorMessage('Invalid request data');
		// 				exit;
		// 			}
		// 			#
		// 			if ($ticket) {
		// 				$ticket->delete();
		// 				$site->redirectTo( $site->urlTo("/tickets") );
		// 			}
		// 			break;
		// 	}
		// }

		// function closeAction($id) {
		// 	global $site;
		// 	$request = $site->mvc->getRequest();
		// 	$ticket = Tickets::get($id);
		// 	switch ($request->type) {
		// 		case 'get':
		// 			$this->view->render('tickets/close-page', array('ticket' => $ticket));
		// 			break;
		// 		case 'post':
		// 			$token = $request->post('token');
		// 			# Validate anti-csrf token
		// 			if (! $site->csrf->checkToken($token) ) {
		// 				$site->errorMessage('Invalid request data');
		// 				exit;
		// 			}
		// 			#
		// 			if ($ticket) {
		// 				$ticket->close();
		// 				$site->redirectTo( $site->urlTo("/tickets/{$ticket->id}") );
		// 			}
		// 			break;
		// 	}
		// }

		// function openAction($id) {
		// 	global $site;
		// 	$request = $site->mvc->getRequest();
		// 	$ticket = Tickets::get($id);
		// 	switch ($request->type) {
		// 		case 'get':
		// 			$this->view->render('tickets/open-page', array('ticket' => $ticket));
		// 			break;
		// 		case 'post':
		// 			$token = $request->post('token');
		// 			# Validate anti-csrf token
		// 			if (! $site->csrf->checkToken($token) ) {
		// 				$site->errorMessage('Invalid request data');
		// 				exit;
		// 			}
		// 			#
		// 			if ($ticket) {
		// 				$ticket->open();
		// 				$site->redirectTo( $site->urlTo("/tickets/{$ticket->id}") );
		// 			}
		// 			break;
		// 	}
		// }

		// function uploadAction() {
		// 	global $site;
		// 	$request = $site->mvc->getRequest();
		// 	$ret = new AjaxResponse();
		// 	$file = $request->files('file');
		// 	if ( $file ) {
		// 		$attachment = Attachments::upload($file);
		// 		if ($attachment) {
		// 			$ret->attachment = $attachment;
		// 			$ret->result = 'success';
		// 		}
		// 	}
		// 	$ret->respond();
		// }

		// function detachAction() {
		// 	global $site;
		// 	$request = $site->mvc->getRequest();
		// 	$token = $request->post('token');
		// 	$id = $request->post('id');
		// 	$ret = new AjaxResponse();
		// 	# Validate anti-csrf token
		// 	if (! $site->csrf->checkToken($token) ) {
		// 		$ret->message = 'Invalid request data';
		// 	} else {
		// 		if ($id) {
		// 			$attachment = Attachments::get($id);
		// 			$attachment->delete();
		// 			$ret->result = 'success';
		// 		}
		// 	}
		// 	$ret->respond();
		// }

		// function labelAction($id) {
		// 	global $site;
		// 	$request = $site->mvc->getRequest();
		// 	$ret = new AjaxResponse();
		// 	$ret->respond();
		// }

		// function unlabelAction($id) {
		// 	global $site;
		// 	$request = $site->mvc->getRequest();
		// 	$ret = new AjaxResponse();
		// 	$ret->respond();
		// }

		// function addLabelAction() {
		// 	global $site;
		// 	$request = $site->mvc->getRequest();
		// 	$ret = new AjaxResponse();
		// 	$name = $request->post('label');
		// 	$color = $request->post('color');
		// 	$label = new TicketTag();
		// 	$label->name = $name;
		// 	$label->slug = $site->toAscii($name);
		// 	$label->description = $color;
		// 	$label->type = 'Label';
		// 	$label->save();
		// 	$ret->data = $label;
		// 	$ret->respond();
		// }

		// function removeLabelAction($id) {
		// 	global $site;
		// 	$request = $site->mvc->getRequest();
		// 	$ret = new AjaxResponse();
		// 	$ret->respond();
		// }

	}

?>