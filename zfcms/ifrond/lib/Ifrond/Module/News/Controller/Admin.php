<?php

class Ifrond_Module_News_Controller_Admin extends Ifrond_Controller_Action_Admin
{

	protected $_topicsRef = 1;
	protected $_galleryDir = '/images/atts/news/gallery';

	public function getModelMapperNews() {
		$d = new Ifrond_Module_News_Model_Mapper_News();
		return $d;
	}

	public function getModelNews() {
		$d = new Ifrond_Module_News_Model_News();
		return $d;
	}
	
	public function getModelMapperGallery() {
		$d = new Ifrond_Module_News_Model_Mapper_Gallery();
		return $d;
	}

	public function getModelTopic() {
		$d = new Ifrond_Module_Ref_Model_Mapper_Ref();
		return $d;
	}

	public function getTopics() {
		$topicMapper = $this->getModelTopic();
		$topics = $topicMapper->getAllByPid(array('value' => $this->_topicsRef, 'order' => 'rang ASC'));
		return $topics;
	}

	public function getFormEdit() {
		$d = new Ifrond_Module_News_Form_Edit(array('topics' => $this->getTopics()));
		return $d;
	}
	
	public function getFormFilter() {
		$d = new Ifrond_Module_News_Form_Filter(array('topics' => $this->getTopics()));
		return $d;
	}

	public function getFormGallery() {
		$d = new Ifrond_Module_News_Form_Fileupload(array('fileDestination' => $this->getGalleryDir()));
		return $d;
	}
	
	public function getFormGalleryTitle() {
		$d = new Ifrond_Module_News_Form_Gallery();
		return $d;
	}

	public function getGalleryDir() {
		return PATH_PUB.$this->_galleryDir;
	}

	public function indexAction()
	{
		$newsMapper = $this->getModelMapperNews();
		$this->view->pageCount = $newsMapper->getPagesCount();
		$this->view->pageCurrent = 1;
		$this->view->news = $newsMapper->getByFilter();
		$this->view->form = $this->getFormEdit();
		$this->view->formGallery = $this->getFormGallery();
	}

	public function getModelThumb($path) {
		$d = new Ifrond_Model_Thumb($path);
		return $d;
	}

	public function galleryAction()
	{
		$this->_helper->layout()->disableLayout();
		$result = array();
		$result['result'] = 'false';
		$result['msg'] = '';
		$result['html'] = '';
		if ($this->getRequest()->isPost()) {
			$form = $this->getFormGallery();
			$postData = $this->getRequest()->getPost();
			$valid = $form->isValid($postData);
			if (!$valid) {
				$result['result'] = 'false';
				$result['msg'] = implode(', ', $form->getMessages('fnfFile'));
			} else {
				$form->checkFilename($_POST);
				if (!$form->fnfFile->receive()) {
					$result['result'] = 'false';
					$result['msg'] = 'Ошибки при загрузке файла';
				}	
				$newfile = $form->fnfFile->getFileName();				
				$values = $form->getValues();				
				$galleries = $this->getModelMapperGallery();
				$row['title'] = $values['fnfTitle'];
				$row['pid'] = $values['fnfPid'];
				$row['module'] = 'news';
				$row['path'] = str_replace(PATH_WEBROOT, '', $newfile);
				$row['path'] = str_replace('\\', '/', $row['path']);
				$row['rang'] = $galleries->getNextRang($row['pid']);
				$gallery = $galleries->createNew($row);
				$gallery->save($row);
				
				$thumb = $this->getModelThumb($newfile);
				$thumb->adaptiveResize(80, 80)->saveQuick();				
				$result['result'] = 'true';
				$img = $thumb->getImageAsTag(array('alt' => $row['title']));
				$result['html'] = '<li id="gallery_'.$gallery->id.'" href="'.$row['path'].'">'.$img.'</li>';
			}
			$this->view->result = $result;
		}
	}
	
	public function galleryeditAction() {
		$form = $this->getFormGalleryTitle();
		$id = $this->_getParam('id', 0);
		$galleryMapper = $this->getModelMapperGallery();
		$gallery = $galleryMapper->find($id);
		$form->getElement('fngeId')->setValue($id);
		$form->getElement('fngeTitle')->setValue($gallery->title);
		$thumb = $this->getModelThumb(PATH_WEBROOT.$gallery->path);
		$thumb->adaptiveResize(500, 270)->saveQuick();				
		$this->view->img = $thumb->getImageAsTag(array('alt' => $gallery->path));
		$this->view->form = $form;		
	}
	
	public function gallerysaveAction() {
		$form = $this->getFormGalleryTitle();
		$result = array();
		$result['result'] = 'false';
		$result['msg'] = '';
		$result['html'] = '';
		if ($this->getRequest()->isPost()) {
			$this->_helper->layout()->disableLayout();
			$postData = $this->getRequest()->getPost();
			$valid = $form->isValid($postData);
			if (!$valid) {
				$result['result'] = 'false';
				$result['msg'] = implode(', ', $form->getMessages('fnfFile'));
			} else {				
				$values = $form->getValues();				
				$galleries = $this->getModelMapperGallery();
				$row['title'] = $values['fngeTitle'];
				$row['id'] = $values['fngeId'];				
				$gallery = $galleries->createNew($row);
				$gallery->save($row);
				$galleryResult = array();
				$galleryResult['fngeTitle'] = $row['title'];
				$galleryResult['fngeId'] = $row['id'];
				$result['result'] = 'true';				
				$result['gallery'] = $galleryResult;
			}			
		} else {
			$result['msg'] = 'Данные должны быть отправлены методом POST';
		}
		$this->view->result = $result;
	}
	
	public function deleteAction() {		
		$result = array();
		$result['result'] = false;		
		$result['msg'] = '';
		$id = intval($this->_getParam('id', 0));
		$result['id'] = $id;
		if ($id > 0) {			
			$newsMapper = $this->getModelMapperNews();
			$news = $newsMapper->find($id);						
			if ($news) {
				$galleryList = $news->getGallery();
				if (sizeof($galleryList) > 0) {
					foreach ($galleryList as $t) {				
						$image = PATH_WEBROOT.$t->path;
						if (!unlink($image)) $result['msg'] = 'Не удалось удалить файл '.$t->path;
						$t->delete();	
					}
				}
				$result['result'] = true;
				$news->delete();	
			} else {
				$result['msg'] = 'Указанной новости №'.$id.' не существует ';	
			}			
		} else {
			$result['msg'] = 'Укажите какую новость нужно удалить';
		}
		$this->view->result = $result;
		
	}
	
	public function filterAction() {
		$form = $this->getFormFilter();
		$this->view->form = $form;
	}
	
	public function listAction()
	{
		$newsMapper = $this->getModelMapperNews();
		$form = $this->getFormFilter();
		if ($form->isValid($_POST)) {
			$values = $form->getValues();
			$filter = array();
			if ($values['fnfStart'] != '') {
				$filter['date_start'] = $values['fnfStart'];
				$filter['date_start'] = date('Y-m-d 00:00:01', strtotime($filter['date_start']));
				if ($filter['date_start'] < '1971-00-00 00:00:01') unset($filter['date_start']);
			}
			if ($values['fnfEnd'] != '') {
				$filter['date_end'] = $values['fnfEnd'];
				$filter['date_end'] = date('Y-m-d 00:00:01', strtotime($filter['date_end']));
				if ($filter['date_end'] < '1971-00-00 00:00:01') unset($filter['date_end']);
			}			
			if ($values['fnfPid'] > 0) $filter['pid'] = $values['fnfPid'];
			$page = $values['fnfPage'];
			if ($page < 1) $page = 1;
			if ($values['fnfTitle'] != '') $filter['title'] = $values['fnfTitle'];		
			$newsList = $newsMapper->getByFilter($filter, $page);	
			$this->view->pageCurrent = $page;
			$this->view->pageCount = $newsMapper->getPagesNum();
			if (sizeof($newsList) > 0) {
				$this->view->msg = '';
			} else {
				$this->view->msg = 'Поиск не дал результатов';
			}			
			$this->view->news = $newsList;
		} else {
			$errors = $form->getMessages();
			$result['result'] = false;
			$result['errors'] = array();
			foreach ($errors as $k => $t) {
				if (sizeof($t) > 0) {
					$result['errors'][$k] = implode(', ', $t);
				}
			}
			$result['msg'] = implode(', ', $result['errors']);
		}
	}
	
	public function gallerydeleteAction() {		
		$result = array();
		$result['result'] = false;		
		$result['msg'] = '';
		$id = intval($this->_getParam('id', 0));
		$result['id'] = $id;
		if ($id > 0) {			
			$galleryMapper = $this->getModelMapperGallery();
			$gallery = $galleryMapper->find($id);			
			if ($gallery) {
				$result['result'] = true;
				$image = PATH_WEBROOT.$gallery->path;
				if (!unlink($image)) $result['msg'] = 'Не удалось удалить файл '.$gallery->path;
				$gallery->delete();	
			} else {
				$result['msg'] = 'Указанной фотографии №'.$id.' не существует ';	
			}			
		} else {
			$result['msg'] = 'Укажите какую фотографию нужно удалить';
		}
		$this->view->result = $result;
		
	}
	
	public function rangAction()
	{
		$galleries = $this->getModelMapperGallery();
		$galleryOrder = $this->_getParam('gallery');		
		$result = array();
		$result['result'] = true;
		$result['msg'] = array();
		foreach ($galleryOrder as $k => $t) {
			if ($t > 0) {
				$t = intval($t);
				$k = intval($k);
				$row = array('id' => $t, 'rang' => $k);				
				$p = $galleries->createNew($row);
				if ($p) {
					$p->save();
				} else {
					$result['result'] = false;
					$result['msg'][] = 'ошибка при сохранении фотографии id#'.$t;
				}
			}
		}
		if (sizeof($result) > 0) {
			$result['msg'] = implode(', ', $result['msg']);
		} else {
			unset($result['msg']);
		}		
		$this->view->result = $result;
	}

	public function cleanTags($tagsStr) {
		$tagsList = explode(',', $tagsStr);
		foreach ($tagsList as $k => $t) {
			$t = clearStr($t);
			if ($t != '') {
				$tagsList[$k] = clearStr($t);
			}
		}
		return $tagsList;
	}

	public function getoneAction()
	{
		$id = intval($this->_getParam('id', 0));
		$result = array();
		$result['result'] = true;
		if ($id > 0) {
			$neweses = $this->getModelMapperNews();
			$news = $neweses->find($id);			
			if ($news) {
				$news->extend();
				$galleryList = $news->getGallery();	
							
				$result['news'] = $this->prepareNewsRow($news);
				$gallery = array();
				
				if (sizeof($galleryList) > 0) {
					foreach ($galleryList as $t) {				
						$thumb = $this->getModelThumb(PATH_WEBROOT.$t->path);
						$thumb->adaptiveResize(80, 80)->saveQuick();				
						$result['result'] = 'true';
						$tRow = array();
						$tRow['id'] = $t->id;
						$tRow['path'] = $t->path;
						$tRow['thumb'] = $thumb->getImageAsTag(array('alt' => $t->title));
						$gallery[] = $tRow;
					}
				}				
				$this->view->gallery = $gallery;
			} else {
				$result['result'] = false;
				$result['msg'] = 'Запрошенной страницы не существует';
			}
		} else {
			$result['result'] = false;
			$result['msg'] = 'Неверный формат запроса';
		}
		$this->view->result = $result;
	}

	public function saveAction()
	{
		$form = $this->getFormEdit();
		$news = $this->getModelMapperNews();
		$result = array();
		$result['result'] = true;
		if ($form->isValid($_POST)) {
			$values = $form->getValues();
			$row = array();
			$id = $values['fneId'];
			$row['id'] = $id;
			$row['pid'] = $values['fnePid'];
			$row['is_active'] = 1;
			$row['title'] = $values['fneTitle'];
			$row['subtitle'] = $values['fneSubtitle'];
			$row['text'] = $values['fneText'];
			if ($id == 0) {
				$row['author'] = $this->_user->id;
				$row['date_add'] = date('Y-m-d H:i:s');
			}
			$row['editor'] = $this->_user->id;
			$row['date_edit'] = date('Y-m-d H:i:s');
			$row['date_event'] = date('Y-m-d H:i:s', strtotime($values['fneDate'] . ' ' . $values['fneTime']));

			$theNews = $news->createNew($row);
			$theNews->save();
			$tags = $this->cleanTags($values['fneTags']);
			if (sizeof($tags) > 0) $theNews->setTags($tags);
			$result['news'] = $this->prepareNewsRow($theNews);
			
			$this->view->news = $theNews;
		} else {
			$errors = $form->getMessages();
			$result['result'] = false;
			$result['errors'] = array();
			foreach ($errors as $k => $t) {
				if (sizeof($t) > 0) {
					$result['errors'][$k] = implode(', ', $t);
				}
			}
			$result['msg'] = implode(', ', $result['errors']);
		}
		$this->view->result = $result;
	}

	public function prepareNewsRow($news)
	{
		$newsRow = $news->getRow();
		$tags = array();
		if (isset($newsRow['tags']) && sizeof($newsRow['tags']) > 0) {
			foreach ($newsRow['tags'] as $t) {
				$tags[] = $t['title'];
			}
			$newsRow['tags'] = implode(', ', $tags);
		} else {
			$newsRow['tags'] = '';
		}
		$newsResult = array();
		foreach ($newsRow as $k => $t) {
			$k = 'fne'.ucfirst(str_replace('_', '', $k));
			$newsResult[$k] = $t;
		}
		$newsResult['fneDate'] = date('d.m.Y', strtotime($newsRow['date_event']));
		$newsResult['fneTime'] = date('H:i', strtotime($newsRow['date_event']));
		return $newsResult;
	}

}