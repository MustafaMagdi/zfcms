<?php

class Ifrond_Module_News_Controller_Map extends Ifrond_Controller_Action_Public
{

	public function getModelMapperNews() {
		$d = new Ifrond_Module_News_Model_Mapper_News();
		return $d;
	}

	public function getModelMapperRef() {
		$d = new Ifrond_Module_Ref_Model_Mapper_Ref();
		return $d;
	}

	public function indexAction()
	{
		$refMapper = $this->getModelMapperRef();
		$refs = $refMapper->getAllByPid(array('value' => 1, 'order' => 'rang ASC'));		
		$this->view->topics = $refs;
	}
}