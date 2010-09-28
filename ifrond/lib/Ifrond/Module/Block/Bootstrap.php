<?php
class Ifrond_Module_Block_Bootstrap extends Zend_Application_Module_Bootstrap
{
   public function _initAutoload() {
		$loader = new Zend_Application_Module_Autoloader(array(
			'namespace' => 'Block',
			'basePath'  => APPLICATION_PATH . '/modules/block'));
	}
}