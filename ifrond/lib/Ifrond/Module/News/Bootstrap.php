<?php
class Ifrond_Module_News_Bootstrap extends Zend_Application_Module_Bootstrap
{
   public function _initAutoload() {
		$loader = new Zend_Application_Module_Autoloader(array(
			'namespace' => 'News',
			'basePath'  => APPLICATION_PATH . '/modules/news'));
	}
}