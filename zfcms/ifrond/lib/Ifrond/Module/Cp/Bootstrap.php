<?php
class Ifrond_Module_Cp_Bootstrap extends Zend_Application_Module_Bootstrap
{
   public function _initAutoload() {
		$loader = new Zend_Application_Module_Autoloader(array(
			'namespace' => 'Cp',
			'basePath'  => APPLICATION_PATH . '/modules/cp'));
	}
}