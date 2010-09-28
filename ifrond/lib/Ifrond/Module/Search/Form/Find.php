<?php
class Ifrond_Module_Search_Form_Find extends Zend_Form
{	
	
	protected $_modules= array();

	public function setModules($modules) {
		$modulesArr = array();	
		$modulesArr[0] = 'весь сайт';
		foreach ($modules as $k => $t) {
			$modulesArr[$k] = $t;
		}
		$this->_modules = $modulesArr;
	}	
	
	public function init()
	{
		$this->setAction('/search/')
		->setMethod('get')
		->setAttrib('id', 'searcherFull');
		
		$fields = $this->_fields;
			
		$this->addElement('text', 'query', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(array('StringLength', false, array(3, 900))),
            'required'   => true,
			'value' => '',
            'label'      => 'Введите запрос',		
			'attribs'  => array('class' => 'texter'),	
			'decorators' => array( 'ViewHelper', 'Errors' )
		));
		
		$this->addElement('select', 'module', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'required'   => false,
			'value' => 0,
            'label'      => 'Место поиска',
			'attribs'  => array('class' => 'texter'),
			'multiOptions' => $this->_modules,
			'decorators' => array( 'ViewHelper', 'Errors' )
		));	
		
		$this->addElement('select', 'period', array(
            'filters'    => array('Int'),
            'required'   => false,
            'label'      => 'Период поиска',
			'value' => 0,
			'attribs'  => array('class' => 'texter'),
			'multiOptions' => array(
									0 => 'все время',
									7 => 'за неделю',
									31 => 'за месяц',
									92 => 'за квартал',
									365 => 'за год'
								),
			'decorators' => array( 'ViewHelper', 'Errors' )
		));	
		
		$this->addElement('submit', 'find', array(
            'required' => false,            
            'label'    => 'Найти',				
			'decorators' => array( 'ViewHelper', 'Errors' )
		));
		
		$this->setDecorators(array(array('viewScript', array('viewScript' => '/index/forms/find.phtml'))));



	}
}