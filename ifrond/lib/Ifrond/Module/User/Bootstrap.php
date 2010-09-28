<?php
class Ifrond_Module_User_Bootstrap extends Zend_Application_Module_Bootstrap
{
	public function _initAutoload() {
		$loader = new Zend_Application_Module_Autoloader(array(
			'namespace' => 'User',
			'basePath'  => APPLICATION_PATH . '/modules/user'));
	}
}