<?php
class Ifrond_Module_Tag_Bootstrap extends Zend_Application_Module_Bootstrap
{
   public function _initAutoload() {
		$loader = new Zend_Application_Module_Autoloader(array(
			'namespace' => 'Tag',
			'basePath'  => APPLICATION_PATH . '/modules/tag'));
	}
}