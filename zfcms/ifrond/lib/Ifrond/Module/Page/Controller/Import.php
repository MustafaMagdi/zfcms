<?php

class Ifrond_Module_Page_Controller_Import extends Ifrond_Controller_Action_Admin
{


	public function getModelOldUlgov() {
		$d = new Ifrond_Module_Page_Model_OldUlgov();
		return $d;
	}

	public function getModelMapperOldUlgov() {
		$d = new Ifrond_Module_Page_Model_Mapper_OldUlgov();
		return $d;
	}

	public function indexAction()
	{
		$oldUlgovMapper = $this->getModelMapperOldUlgov();
		$result = $oldUlgovMapper->convertAll();		
		_p('вроде все');
	}
}