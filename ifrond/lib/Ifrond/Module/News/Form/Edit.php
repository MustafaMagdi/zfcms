<?php
class Ifrond_Module_News_Form_Edit extends Zend_Form
{

	protected $_topics = array();
	
	public function getTopicsOptions() {
		$topics = array();
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
		$this->setAction('/news/admin/save/')
		->setMethod('post')
		->setAttrib('id', 'News_Form_Edit');

		$this->addElement('text', 'fneTitle', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(array('StringLength', false, array(3, 900))),
            'required'   => true,
			'value' => 'Новая новость',
            'label'      => 'Заголовок',		
			'attribs'  => array('class' => 'texter', 'style' => 'width:93%;font-size:18px;'),	
			'decorators' => array( 'ViewHelper', 'Errors' )
		));
		
		$this->addElement('text', 'fneDate', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(array('StringLength', false, array(3, 900))),
            'required'   => true,
			'value' => date('d.m.Y'),
            'label'      => 'Дата',		
			'attribs'  => array('class' => 'texter', 'style' => 'width:73px;'),	
			'decorators' => array( 'ViewHelper', 'Errors' )
		));
		
		$this->addElement('text', 'fneTags', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(array('StringLength', false, array(3, 900))),
            'required'   => false,
			'value' => '',
            'label'      => 'Тэги',		
			'attribs'  => array('class' => 'texter', 'style' => 'width:190px;'),	
			'decorators' => array( 'ViewHelper', 'Errors' )
		));
		
		$this->addElement('text', 'fneTime', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(array('StringLength', false, array(3, 900))),
            'required'   => true,
			'value' => date('H:i'),
            'label'      => 'Время',		
			'attribs'  => array('class' => 'texter', 'style' => 'width:38px;'),	
			'decorators' => array( 'ViewHelper', 'Errors' )
		));
		
		$this->addElement('select', 'fnePid', array(
            'filters'    => array('Int'),
            'required'   => true,
            'label'      => 'Тема',
			'attribs'  => array('style' => 'width:130px;'),
			'decorators' => array( 'ViewHelper', 'Errors' )
		));	
		$this->getElement('fnePid')->setMultiOptions($this->getTopicsOptions());
		
		
		$this->addElement('textarea', 'fneSubtitle', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(array('StringLength', false, array(3, 1500))),
            'required'   => false,
            'label'      => 'Анонс',
			'attribs'  => array('class' => 'texter', 'style' => 'width:93%;height:57px;'),
			'decorators' => array( 'ViewHelper', 'Errors' )
		));		
		
		$this->addElement('textarea', 'fneText', array(
		    'label'      => 'Текст',
		    'required'   => false,
			'attribs'  => array('class' => 'wysiwyg', 'style' => 'width:93%;height:200px;'),
			'decorators' => array( 'ViewHelper', 'Errors')
		));
		

		$this->addElement('hidden', 'fneId', array(
			'value' => 0,
			'filters'    => array('Int'), 
			'required'   => true,
			'decorators' => array( 'ViewHelper', 'Errors' )));
		
		$this->addElement('hidden', 'fneRang', array(
			'value' => -1,
			'filters'    => array('Int'), 
			'required'   => true,
			'decorators' => array( 'ViewHelper', 'Errors' )));		
		
		
		$save = $this->addElement('submit', 'fneSave', array(
            'required' => false,   
			'attribs'  => array('style' => 'display:none;'),         
            'label'    => 'Сохранить',
			'decorators' => array( 'ViewHelper', 'Errors' )		
		));

		$this->setDecorators(array(array('viewScript', array('viewScript' => '/admin/forms/edit.phtml'))));




	}
}