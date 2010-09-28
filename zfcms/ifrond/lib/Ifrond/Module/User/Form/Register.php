<?php
class Ifrond_Module_User_Form_Register extends Zend_Form
{
	public function init()
	{
		$this->setAction('/user/index/register/')
		->setMethod('post')
		->setAttrib('id', 'User_Form_Register')
		->setAttrib('title', ' Регистрация');

		$login = $this->addElement('text', 'login', array(
            'filters'    => array('StripTags','StringTrim'),
            'validators' => array(
		array('StringLength', false, array(3, 500)),
		),
            'required'   => true,
            'label'      => 'Имя',
		'attribs'  => array('class' => 'texter'),
		'decorators' => array( 'ViewHelper', 'Errors' )
		));

		$email = $this->addElement('text', 'email', array(
            'filters'    => array('StripTags','StringTrim'),
            'validators' => array('EmailAddress'),
            'required'   => true,
            'label'      => 'Электронная почта',
		'attribs'  => array('class' => 'texter'),
		'decorators' => array( 'ViewHelper', 'Errors' )
		));

		$pwd = $this->addElement('password', 'pwd', array(
            'filters'    => array('StripTags','StringTrim'),
            'validators' => array(
		array('StringLength', false, array(3, 250)),
		),
            'required'   => true,
            'label'      => 'Пароль',
		'attribs'  => array('class' => 'texter'),
		'decorators' => array( 'ViewHelper', 'Errors' )
		));

		$pwd2 = $this->addElement('password', 'pwd2', array(
            'filters'    => array('StripTags','StringTrim'),
            'validators' => array('Identical'),
            'required'   => true,
            'label'      => 'Повторите пароль',
		'attribs'  => array('class' => 'texter'),
		'decorators' => array( 'ViewHelper', 'Errors' )
		));


		$save = $this->addElement('submit', 'save', array(
            'required' => false,
            'ignore'   => true,
            'label'    => 'Сохранить',
		'attribs'  => array('class' => 'submitter'),
		'decorators' => array( 'ViewHelper', 'Errors' )		
		));

		$id = $this->addElement('hidden', 'id', array(
			'value' => 0,
			'filters'    => array('Int'), 
			'decorators' => array( 'ViewHelper', 'Errors' )));
		$topic = $this->addElement('hidden', 'topic', array('value' => 'news', 'decorators' => array( 'ViewHelper', 'Errors' )));
		$lat = $this->addElement('hidden', 'lat', array('decorators' => array( 'ViewHelper', 'Errors' )));
		$lng = $this->addElement('hidden', 'lng', array('decorators' => array( 'ViewHelper', 'Errors' )));

		$this->setDecorators(
		array(
		array('viewScript', array('viewScript' => 'user/forms/register.phtml'))
		));




	}

	public function isValid($data) {
		$pwd2 = $this->getElement('pwd2');
		$pwd2->getValidator('Identical')->setToken($data['pwd']);
		return parent::isValid($data);
	}


}