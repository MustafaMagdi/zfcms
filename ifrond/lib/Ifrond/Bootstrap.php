<?php

require_once 'Ifrond/Utils.php';

class Ifrond_Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initModuleAutoloader() {
		$loader = new Zend_Application_Module_Autoloader(array(
			'namespace' => '' ,
			'basePath' => APPLICATION_PATH.'/modules'));
		$loader = new Zend_Application_Module_Autoloader(array(
			'namespace' => 'Geo_',
			'basePath'  => APPLICATION_PATH . '/modules/geo'));
		$loader = new Zend_Application_Module_Autoloader(array(
			'namespace' => 'User_',
			'basePath'  => APPLICATION_PATH . '/modules/user'));
		$loader = new Zend_Application_Module_Autoloader(array(
			'namespace' => 'Page_',
			'basePath'  => APPLICATION_PATH . '/modules/page'));
		$loader = new Zend_Application_Module_Autoloader(array(
			'namespace' => 'Tag_',
			'basePath'  => APPLICATION_PATH . '/modules/tag'));	
		$loader = new Zend_Application_Module_Autoloader(array(
			'namespace' => 'Ref_',
			'basePath'  => APPLICATION_PATH . '/modules/ref'));
		$loader = new Zend_Application_Module_Autoloader(array(
			'namespace' => 'News_',
			'basePath'  => APPLICATION_PATH . '/modules/news'));	
		$loader = new Zend_Application_Module_Autoloader(array(
			'namespace' => 'Block_',
			'basePath'  => APPLICATION_PATH . '/modules/block'));		
		$loader = new Zend_Application_Module_Autoloader(array(
			'namespace' => 'Search_',
			'basePath'  => APPLICATION_PATH . '/modules/search'));
		$loader = new Zend_Application_Module_Autoloader(array(
			'namespace' => 'Map_',
			'basePath'  => APPLICATION_PATH . '/modules/map'));	
		$loader = new Zend_Application_Module_Autoloader(array(
			'namespace' => 'Feedback_',
			'basePath'  => APPLICATION_PATH . '/modules/feedback'));	
	}

	protected function _initRequest ()
	{
		$this->bootstrap('FrontController');
		$front = $this->getResource('FrontController');
		$front->setParam('prefixDefaultModule', true);
		$front->setParam('useDefaultControllerAlways', true);
	}

	protected function _initAuth ()
	{
		$this->bootstrap('FrontController');
		$front = $this->getResource('FrontController');
		$auth = Zend_Auth::getInstance();
		$acl  = $this->getModelUserAcl();
		$front->registerPlugin(new Ifrond_Controller_Plugin_Auth($auth, $acl));
	}
	public function getModelUserAcl() {
		$acl  = new Ifrond_Module_User_Model_Acl();
		return $acl;
	}
	protected function _initHeaders ()
	{
		$options = $this->getOptions();
		$this->bootstrap('View');
		$view = $this->getResource('View');
		$view->doctype($options['head']['doctype']);
		$view->headTitle($options['head']['title'], 'SET');
		$view->headLink(array('rel' => 'favicon' , 'href' => '/favicon.ico'), 'SET');
		foreach ($options['tpl']['css'] as $k => $t) {
			$view->headLink()->appendStylesheet('/pub/css/' . $t, $k);
		}
		foreach ($options['head']['meta'] as $k => $t) {
			$view->headMeta()->setName($k, $t);
		}
		if (isset($options['tpl']['js']) && sizeof($options['tpl']['js']) > 0) {
			foreach ($options['tpl']['js'] as $t) {
				if (strpos($t, 'http://') !== 0) $t = '/pub/js/'.$t;
				$view->headScript()->appendFile($t);
			}
		}
		$view->headMeta()->setName('generator', 'IfrondZF');
		$view->headMeta()->setHttpEquiv('Content-Type', 'text/html; charset=UTF-8')->setHttpEquiv('Content-Language', $options['head']['lang']);
		$view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");
	}
	protected function _initDbPrefix ()
	{
		$options = $this->getOptions();
		if ($options['db']['prefix'] != '') {
			define('_DBTABLE_PREFIX', $options['db']['prefix']);
		}
	}
	protected function _initMyDebug ()
	{

	}
	protected function _initTranslate ()
	{
		date_default_timezone_set('Europe/Moscow');
		$translate = new Zend_Translate(array(
			'adapter' => 'array',        
			'content' => PATH_LIB.'/Ifrond/Lang/Ru/Form.php',        
			'locale'  => 'ru'    
			));
			Zend_Form::setDefaultTranslator($translate);
	}
}

