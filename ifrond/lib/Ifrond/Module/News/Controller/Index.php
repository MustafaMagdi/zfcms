<?php

class Ifrond_Module_News_Controller_Index extends Ifrond_Controller_Action_Public
{

	public function getModelMapperNews() {
		$d = new Ifrond_Module_News_Model_Mapper_News();
		return $d;
	}

	public function getModelMapperRef() {
		$d = new Ifrond_Module_Ref_Model_Mapper_Ref();
		return $d;
	}

	public function getModelThumb($path) {
		$d = new Ifrond_Model_Thumb($path);
		return $d;
	}

	public function permlinkAction() {
		$id = intval($this->_getParam('id', 0));
		$util = $this->_getParam('util', '');
		if ($util === 'print') {
			$this->_helper->layout()->setLayout('print');
			$this->view->print = true;
		} else {
			$this->view->print = false;
		}
		if ($id > 0) {
			$newsMapper = $this->getModelMapperNews();
			$news = $newsMapper->find($id);
			if ($news != false) {
				$news->extend();
				$gallery = $news->getGallery();
				$this->view->news = $news;
			} else {
				$this->_helper->viewRenderer('404');
			}
		} else {
			$this->_helper->viewRenderer('404');
		}
	}

	public function oldsupportAction() {
		$topicShortcut = clearStr($this->_getParam('topic', ''));
		$newsShortcut = clearStr($this->_getParam('shortcut', ''));
		if ($topicShortcut == '' || $newsShortcut == '') {
			$this->_helper->viewRenderer('404');
			return true;
		}
		$newsMapper = $this->getModelMapperNews();
		$news = $newsMapper->getByOldTopicAndShortcut($topicShortcut, $newsShortcut);
		if ($news != false) {
			$news->extend();
			$news->getGallery();
			$this->view->news = $news;
			$this->_helper->viewRenderer('permlink');
		} else {
			$this->_helper->viewRenderer('404');
		}
	}

	public function topicAction() {
		$topicShortcut = clearStr($this->_getParam('topic', ''));
		$page = intval($this->_getParam('page', '1'));
		if ($topicShortcut == '') {
			$this->_helper->viewRenderer('404');
			return true;
		}
		$refMapper = $this->getModelMapperRef();
		$topic = $refMapper->getAllByExtra1($topicShortcut);
		if ($topic == false) {
			$this->_helper->viewRenderer('404');
			return true;
		}
		$newsMapper = $this->getModelMapperNews();
		$news = $newsMapper->getByTopic($topic[0]->id, $page);
		$this->view->newsList = $news;
		$this->view->topic = $topic[0];
		$this->view->page = $page;
		$this->view->pageAll = $newsMapper->getPagesNum();
	}

	public function dateAction() {
		$date = clearStr($this->_getParam('x', 0));
		//_p($date);
		$page = intval($this->_getParam('page', '1'));
		if ($date == 0) {
			$date = date('Y-m-d');
		} else {
			$date = toFormatDate($date, 'Y-m-d');
		}
		$newsMapper = $this->getModelMapperNews();
		$newsMapper->setLimit(200);
		$news = $newsMapper->getByDate($date, $page);
		$this->view->newsList = $news;
		$this->view->date = $date;
		$this->view->page = $page;
		$this->view->pageAll = $newsMapper->getPagesNum();
	}

	public function allAction() {
		$page = intval($this->_getParam('page', '1'));
		$newsMapper = $this->getModelMapperNews();
		$news = $newsMapper->getAll($page);
		$this->view->newsList = $news;
		$this->view->page = $page;
		$this->view->pageAll = $newsMapper->getPagesNum();
	}

	public function indexAction()
	{
		$url = analyseUrl($this->getRequest()->getRequestUri());
		$urlSize = sizeof($url);
		switch ($urlSize) {
				
			case 2:
				$this->_forward('topic', 'index', 'news', array('topic' => $url[1]));
				break;
					
			case 3:
				if ($url[1] == 'date') {
					$this->_forward('date', 'index', 'news', array('date' => $url[2]));
				} else {
					$this->_forward('oldsupport', 'index', 'news', array('shortcut' => $url[2], 'topic' => $url[1]));
				}
				break;
					
			case 4:
				$id = intval($url[3]);
				$this->_forward('permlink', 'index', 'news', array('id' => $id));
				break;
					
			default:
				$this->_forward('all', 'index', 'news');
				break;
		}


	}
}