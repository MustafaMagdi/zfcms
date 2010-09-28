<?php
class Ifrond_Module_Search_Bootstrap extends Zend_Application_Module_Bootstrap
{
   public function _initAutoload() {
		$loader = new Zend_Application_Module_Autoloader(array(
			'namespace' => 'Search',
			'basePath'  => APPLICATION_PATH . '/modules/search'));
	}
}