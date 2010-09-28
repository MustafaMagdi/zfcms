<?php

class Ifrond_Module_Search_Controller_Index extends Ifrond_Controller_Action_Public
{

	public function getModelMapperSearch() {
		$d = new Ifrond_Module_Search_Model_Mapper_Search();
		return $d;
	}
	
	public function getSearchableModules() {
		$modules = array(
			'news' => 'Новости',
			'page' => 'Разделы сайта'
		);
		return $modules;
	}
	
	public function getFormFind() {
		$options = array();
		$options['modules'] = $this->getSearchableModules();		
		$d = new Ifrond_Module_Search_Form_Find($options);
		return $d;
	}

	public function indexAction() {
		$searchMapper = $this->getModelMapperSearch();
		$form = $this->getFormFind();
		$this->view->showResults = false;
		$queryStr = clearStr($this->_getParam('query', ''));
		if ($queryStr != '') {
			$query = $this->getRequest()->getQuery();
			$valid = $form->isValid($query);
			if ($valid) {
				//_p($query);
				$values = $form->getValues();
				$values['query'] = clearStr($values['query']);
				$values['module'] = clearStr($values['module']);
				$values['period'] = intval($values['period']);
				if ($values['module'] == '') $values['module'] = 0;
				if ($values['period'] == '') $values['period'] = 0;
				$page = intval($this->_getParam('page', 1));
				$form->getElement('query')->setValue($values['query']);
				$form->getElement('module')->setValue($values['module']);
				$form->getElement('period')->setValue($values['period']);
				//_p($values['module']);
				$availableModules = array_keys($this->getSearchableModules());					
				if (in_array($values['module'], $availableModules)) {					
					$searchMapper->setModule($values['module']);	
				}
				$startDate = new Zend_Date();
				$endDate = new Zend_Date();
				$startDate->sub($values['period'], Zend_Date::DAY);
				if ($values['period'] > 0) $searchMapper->setPeriod($startDate, $endDate);				
				$results = $searchMapper->find(urldecode($values['query']), $page);
				$this->view->showResults = true;
				$this->view->results = $results;
				$this->view->query = $query;
				$this->view->currentPage = $page;
				$this->view->pages = $searchMapper->getPageNum();
			} else {
				$errors = $form->getMessages();				
			}
		} 
		$this->view->form = $form;		
	}
}