<?php
class Ifrond_Module_Ref_Bootstrap extends Zend_Application_Module_Bootstrap
{
   public function _initAutoload() {
		$loader = new Zend_Application_Module_Autoloader(array(
			'namespace' => 'Ref',
			'basePath'  => APPLICATION_PATH . '/modules/ref'));
	}
}