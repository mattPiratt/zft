<?php
class IndexController extends Album_Controller_Action{

	public function init() {
		parent::init();
		$this->view->baseUrl = $this->_request->getBaseUrl();
	}

	function indexAction() {
		$this->view->title = "My Albums";

		$album = new Album();
		$this->view->albums = $album->fetchAll();
	}

	function addAction() {
		$this->view->title = "Add New Album";

		if ($this->_request->isPost()) {
			$filter = new Zend_Filter_StripTags();

			$artist = $filter->filter($this->_request->getPost('artist'));
			$artist = trim($artist);
			$title = trim($filter->filter(
			$this->_request->getPost('title')));

			if ($artist != '' && $title != '') {
				$data = array(
					'artist' => $artist,
					'title' => $title,
				);
				$album = new Album();
				$album->insert($data);
				$this->_redirect('/');
				return;
			}
		}
		// set up an "empty" album
		$this->view->album = new stdClass();
		$this->view->album->id = null;
		$this->view->album->artist = '';
		$this->view->album->title = '';

		// additional view fields required by form
		$this->view->action = 'add';
		$this->view->buttonText = 'Add';
	}

	function editAction() {
		$this->view->title = "Edit Album";
		$album = new Album();

		if ($this->_request->isPost()) {
			$filter = new Zend_Filter_StripTags();

			$id = (int)$this->_request->getPost('id');
			$artist = $filter->filter($this->_request->getPost('artist'));
			$artist = trim($artist);
			$title = trim($filter->filter(
			$this->_request->getPost('title')));

			if ($id !== false) {
				if ($artist != '' && $title != '') {
					$data = array(
						'artist' => $artist,
						'title' => $title,
					);
					$where = 'id = ' . $id;
					$album->update($data, $where);

					$this->_redirect('/');
					return;
				} else {
					$this->view->album = $album->fetchRow('id='.$id);
				}
			}
		} else {
			// album id should be $params['id']
			$id = (int)$this->_request->getParam('id', 0);
			if ($id > 0) {
				$this->view->album = $album->fetchRow('id='.$id);
			}
		}
		// additional view fields required by form
		$this->view->action = 'edit';
		$this->view->buttonText = 'Update';
	} 

	function deleteAction() {
		$this->view->title = "Delete Album";
		$album = new Album();

		if ($this->_request->isPost()) {
			$filter = new Zend_Filter_Alpha();

			$id = (int)$this->_request->getPost('id');
			$del = $filter->filter($this->_request->getPost('del'));
			if ($del == 'Yes' && $id > 0) {
				$where = 'id = ' . $id;
				$rows_affected = $album->delete($where);
			}
		} else {
			$id = (int)$this->_request->getParam('id');
			if ($id > 0) {
				// only render if we have an id and can find the album.
				$this->view->album = $album->fetchRow('id='.$id);
				if ($this->view->album->id > 0) {
					// render template automatically
					return;
				}
			}
		}
		// redirect back to the album list unless we have rendered the view
		$this->_redirect('/');
	}
}
?>