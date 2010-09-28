<?php
class Ifrond_Module_Page_Bootstrap extends Zend_Application_Module_Bootstrap
{
public function _initAutoload() {
		$loader = new Zend_Application_Module_Autoloader(array(
			'namespace' => 'Page',
			'basePath'  => APPLICATION_PATH . '/modules/page'));
	}
}