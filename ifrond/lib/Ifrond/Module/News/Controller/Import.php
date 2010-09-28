<?php

class Ifrond_Module_News_Controller_Import extends Ifrond_Controller_Action_Admin
{


	public function getModelOldUlgov() {
		$d = new Ifrond_Module_News_Model_OldUlgov();
		return $d;
	}

	public function getModelMapperOldUlgov() {
		$d = new Ifrond_Module_News_Model_Mapper_OldUlgov();
		return $d;
	}

	public function indexAction()
	{
		$oldUlgovMapper = $this->getModelMapperOldUlgov();
		$page = $this->_getParam('page', 1);
		$result = $oldUlgovMapper->convertAll($page);
		//$result = true;
		if ($result) {
			$nextPage = $page + 1;
			$url = '/news/import/index/page/'.$nextPage.'/';
			$this->view->url = $url;		
			$this->view->page = $nextPage;
			$redirector = $this->_helper->getHelper('Redirector');
			$redirector->gotoUrl($url);
		} else {
			_p('вроде все');
		}
	}
}