<?php

class Ifrond_Module_Block_Controller_Index extends Ifrond_Controller_Action_Public
{
	protected $_layout = '';
	
	public function getModelMapperBlock() {
		$d = new Ifrond_Module_Block_Model_Mapper_Block();
		return $d;
	}
	public function indexAction() {
		$id = $this->_getParam($id, 0);
		$blockMapper = $this->getModelMapperBlock();
		$block = $blockMapper->find($id);		
		$this->view->block = $block->value;
	}
	public function getoneAction() {
		$shortcut = $this->_getParam('name', 0);
		$shortcut = clearStr($shortcut);
		$blockMapper = $this->getModelMapperBlock();
		$block = $blockMapper->getByShortcut($shortcut);
		if ($block != false) {
			$this->_helper->viewRenderer($shortcut);
		}
		$this->view->block = unserialize($block->value);
	}
	public function includeAction() {		
		//$this->_helper->viewRenderer->setRender('error404');
		$script = $this->_getParam('script', 0);
		$script = clearStr($script);			
		$this->_helper->viewRenderer($script);
	}


}