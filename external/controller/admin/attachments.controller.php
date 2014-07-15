<?php

	class AdminAttachmentsController extends Controller {

		public $view;

		function init() {
			global $site;
			# Load admin styles and scripts
			$site->enqueueStyle('admin');
			$site->enqueueScript('admin');
		}

		function indexAction() {
			global $site;
			$request = $site->mvc->getRequest();
			# Search
			$search = $request->param('search');
			# Pagination
			$page = $request->get('page', 1);
			$show = $request->get('show', 30);
			# Search & filters
			if ($search) {
				# Search and pagination
				$search = $site->getDatabase()->quote("%{$search}%");
				$conditions = "name LIKE {$search}";
				$total = Attachments::count($conditions);
				$attachments = Attachments::rawWhere($conditions, $page, $show, 'desc', 'created');
			} else {
				# Pagination only
				$total = Attachments::count();
				$attachments = Attachments::all($page, $show, 'desc', 'created');
			}
			$this->view->render('attachments/index-page',array('attachments' => $attachments, 'total' => $total));
		}

		function newAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			switch ($request->type) {
				case 'get':
					$this->view->render('attachments/new-page');
					break;
				case 'post':
					$file = $request->files('file');
					$attachment = Attachments::upload($file);
					$site->redirectTo( $site->urlTo('/admin/attachments') );
					break;
			}
		}

		function editAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			$id = $id ? $id : $request->post('id');
			$attachment = Attachments::get($id);
			switch ($request->type) {
				case 'get':
					$this->view->render('attachments/edit-page', array('attachment' => $attachment));
					break;
				case 'post':
					$attachment->name = $request->post('name');
					$attachment->save();
					$attachment->updateMeta('description', $request->post('description'));
					break;
			}
		}

		function deleteAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			$attachment = Attachments::get($id);
			switch ($request->type) {
				case 'get':
					$this->view->render('attachments/delete-page', array('attachment' => $attachment));
					break;
				case 'post':
					$attachment->delete();
					$site->redirectTo( $site->urlTo("/admin/attachments") );
					break;
			}
		}

		function showAction($id) {
			global $site;
			$request = $site->mvc->getRequest();
			$response = $site->mvc->getResponse();
			$attachment = Attachments::get($id);
			switch ($request->format) {
				case 'html':
					//
					break;
				case 'json':
					$response->setHeader('Content-Type', 'application/json');
					$response->setBody( json_encode($attachment) );
					break;
			}
		}

		function refreshAction() {
			$this->view->partial('attachments/picker');
		}
	}

?>