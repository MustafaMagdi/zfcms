<?php
class Ifrond_Module_Page_Form_Seo extends Zend_Form
{
	public function init()
	{
		$this->setAction('/page/admin/seo/')
		->setMethod('post')
		->setAttrib('id', 'Page_Form_Seo');

		$this->addElement('textarea', 'fpsTitle', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(array('StringLength', false, array(0, 2000))),
            'required'   => false,
			'value' => '',
            'label'      => 'Заголовок',
			'attribs'  => array('style' => 'height:60px;width:98%;'),
			'decorators' => array( 'ViewHelper', 'Errors' )
		));
		
		$this->addElement('textarea', 'fpsDescription', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(array('StringLength', false, array(0, 2000))),
            'required'   => false,
			'value' => '',
            'label'      => 'Описание',
			'attribs'  => array('style' => 'height:60px;width:98%;'),
			'decorators' => array( 'ViewHelper', 'Errors' )
		));
		
		$this->addElement('textarea', 'fpsKeywords', array(
            'filters'    => array('StripTags', 'StringTrim'),
            'validators' => array(array('StringLength', false, array(0, 2000))),
            'required'   => false,
			'value' => '',
            'label'      => 'Ключевые слова',
			'attribs'  => array('style' => 'height:60px;width:98%;'),
			'decorators' => array( 'ViewHelper', 'Errors' )
		));
		
		$this->addElement('submit', 'fpsSubmit', array(
			'label' => 'Принять',
			'filters'    => array('Int'), 
			'required'   => false,
			'decorators' => array( 'ViewHelper', 'Errors' )));

		$this->setDecorators(
		array(
		array('viewScript', array('viewScript' => 'admin/forms/seo.phtml'))
		));




	}
}