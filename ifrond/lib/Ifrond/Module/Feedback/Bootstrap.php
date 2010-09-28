<?php
class Ifrond_Module_Feedback_Bootstrap extends Zend_Application_Module_Bootstrap
{
   public function _initAutoload() {
		$loader = new Zend_Application_Module_Autoloader(array(
			'namespace' => 'Feedback',
			'basePath'  => APPLICATION_PATH . '/modules/feedback'));
	}
}