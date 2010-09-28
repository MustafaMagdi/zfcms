<?php
class Ifrond_Module_News_Form_Filter extends Zend_Form
{

	protected $_topics = array();
	
	public function getTopicsOptions() {
		$topics = array();
		$topics[0] = 'все';
		foreach ($this->_topics as $t) {
			$topics[$t->id] = $t->title;
		}
		return $topics;
	}
	
	public function setTopics($topics) {
		$this->_topics = $topics;
	}
	
	public function init()
	{
		$this->setAction('/news/admin/list/')
		->setMethod('post')
		->setAttrib('id', 'News_Form_Filter');

		$this->addElement('text', 'fnfTitle', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(array('StringLength', false, array(3, 900))),
            'required'   => false,
			'value' => '',
            'label'      => 'С текстом',		
			'attribs'  => array('class' => 'texter', 'style' => 'width:93%;'),	
			'decorators' => array( 'ViewHelper', 'Errors' )
		));
		
		$this->addElement('text', 'fnfStart', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(array('StringLength', false, array(10, 100))),
            'required'   => false,
			'value' => date('d.m.Y', strtotime('-1 week')),
            'label'      => 'c',		
			'attribs'  => array('class' => 'texter', 'style' => 'width:73px;'),	
			'decorators' => array( 'ViewHelper', 'Errors' )
		));
		
		$this->addElement('text', 'fnfEnd', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(array('StringLength', false, array(10, 100))),
            'required'   => false,
			'value' => date('d.m.Y'),
            'label'      => 'по',		
			'attribs'  => array('class' => 'texter', 'style' => 'width:73px;'),	
			'decorators' => array( 'ViewHelper', 'Errors' )
		));		
		
		$this->addElement('select', 'fnfPid', array(
            'filters'    => array('Int'),
            'required'   => false,
			'value' => 0,
            'label'      => 'Тема',
			'attribs'  => array('style' => 'width:93%;'),
			'decorators' => array( 'ViewHelper', 'Errors' )
		));	
		$this->getElement('fnfPid')->setMultiOptions($this->getTopicsOptions());		

		$this->addElement('hidden', 'fnfPage', array(
			'value' => 1,
			'filters'    => array('Int'), 
			'required'   => false,
			'decorators' => array( 'ViewHelper', 'Errors' )));
		
		
		$save = $this->addElement('submit', 'fnfSave', array(
            'required' => false,			        
            'label'    => 'Найти',
			'decorators' => array( 'ViewHelper', 'Errors' )		
		));

		$this->setDecorators(array(array('viewScript', array('viewScript' => '/admin/forms/filter.phtml'))));


	}
}