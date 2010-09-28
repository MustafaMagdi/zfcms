<?php
class Ifrond_Controller_Action_User extends Ifrond_Controller_Action_Public
{
	/* @var pages Ifrond_Module_User_Model_Mapper_User */
	protected $_user;
	protected $_requiredRoles = array('root', 'admin', 'user');

	public function preInit() {
		$this->_user = $this->setUser();
		parent::preInit();
	}
	
	public function setUser() {
		$user = $this->getModelMapperUser();
		$user = $user->getActive();
		if ($user) {
			if (!in_array($user->role, $this->_requiredRoles)) {
				$this->_redirect('/user/auth/norole/');
			}
		} else {
			$returnUrl = urlencode(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri());
			return $this->_redirect('/user/auth/?return='.$returnUrl);
		}	
		return $user;	
	}


	
}