<?php
class Ifrond_Controller_Action_Admin extends Ifrond_Controller_Action_User
{	
	protected $_requiredRoles = array('root', 'admin');
	protected $_layout = 'admin';
	
	public function setView() {
		$layout = $this->_helper->layout();	
		if ($this->getRequest()->isXmlHttpRequest()) {
			$layout->disableLayout();			
		} else {
			$layout->setLayoutPath(realpath(PATH_LIB . '/Ifrond/Layout/Scripts'));
			//$layout->setLayout('admin');			
		}	
		parent::setView();
	}
	
}