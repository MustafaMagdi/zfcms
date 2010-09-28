<?php
class Ifrond_Module_Map_Bootstrap extends Zend_Application_Module_Bootstrap
{
   public function _initAutoload() {
		$loader = new Zend_Application_Module_Autoloader(array(
			'namespace' => 'Map',
			'basePath'  => APPLICATION_PATH . '/modules/map'));
	}
}