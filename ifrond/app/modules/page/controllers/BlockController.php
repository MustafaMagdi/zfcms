<?php
class Page_BlockController extends Ifrond_Module_Page_Controller_Block
{
	
	public function getModelMapperPage() {
		$d = new Ifrond_Module_Page_Model_Mapper_Page();
		return $d;
	}
	
	public function analiticsAction()
	{
		$pages = $this->getModelMapperPage();
		$page = $pages->find(681);
		$this->view->childs = $page->getChilds(array('limit' => 3));
	}
	
	public function lastsAction()
	{
		$limit = intval($this->_getParam('limit', 5));
		$pages = $this->getModelMapperPage();
		$pagesList = $pages->getLasts($limit);
		$this->view->pages = $pagesList;
	}
}