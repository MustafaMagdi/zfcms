<?php
class Ifrond_Module_News_Form_Subscribe extends Zend_Form
{
	
	public function init()
	{
		$this->setAction('/news/subscribe/do/')
		->setMethod('post')
		->setAttrib('id', 'News_Form_Subscribe');

		$this->addElement('text', 'email', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(array('StringLength', false, array(3, 900)), 'EmailAddress'),
            'required'   => true,
			'value' => '',
            'label'      => 'Электронная почта',		
			'attribs'  => array('class' => 'texter', 'style' => 'width:93%;'),	
			'decorators' => array( 'ViewHelper', 'Errors' )
		));	
		
		$this->addElement('select', 'act', array(
            'filters'    => array('Int'),
            'required'   => true,
			'value' => 1,
            'label'      => 'Действие',
			'attribs'  => array('style' => 'width:93%;'),
			'decorators' => array( 'ViewHelper', 'Errors' ),
			'multiOptions' => array(
				'1' => 'подписаться',
				'2' => 'отписаться'
			)
		));			
		
		$save = $this->addElement('submit', 'do', array(
            'required' => false,			        
            'label'    => 'Принять',
			'value' => '&nbsp;',
			'decorators' => array( 'ViewHelper', 'Errors' )		
		));

		$this->setDecorators(array(array('viewScript', array('viewScript' => '/subscribe/forms/subscribe.phtml'))));


	}
}