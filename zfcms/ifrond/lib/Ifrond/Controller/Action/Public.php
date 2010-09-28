<?php
class Ifrond_Controller_Action_Public extends Zend_Controller_Action
{

	protected $_isFinal = false;
	/*
	 * @var Ifrond_Module_User_Model_Mapper_User
	 */
	protected $_userMapper = null;
	protected $_layout = 'inside';

	public function init() {
		$this->preInit();
		$this->setView();
		$this->postInit();
	}

	public function setView() {
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$viewRenderer->initView();
		$curScriptPath = $viewRenderer->view->getScriptPaths();
		$libScriptPath = realpath(
			PATH_LIB . '/Ifrond/Module/'
			. ucfirst($this->getRequest()->getModuleName())
			. '/Views/Scripts');		
		if (!in_array($libScriptPath.'/', $curScriptPath)) {
			$scriptPaths = array();
			$scriptPaths[] = $libScriptPath;
			rsort($curScriptPath);
			foreach ($curScriptPath as $t) {
				$scriptPaths[] = realpath($t);
			}
			//$scriptPaths = array_unique($scriptPaths);
			$viewRenderer->view->setScriptPath($scriptPaths);			
		}		
	}
	public function preInit() {
		if ($this->_layout != '') $this->_helper->layout()->setLayout($this->_layout);
	}

	public function postInit() {
		
	}

	public function getModelMapperUser() {
		if ($this->_userMapper == null) {
			$this->_userMapper = new Ifrond_Module_User_Model_Mapper_User();
		}
		return $this->_userMapper;
	}
}