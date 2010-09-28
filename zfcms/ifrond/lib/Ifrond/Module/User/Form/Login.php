<?php
class Ifrond_Module_User_Form_Login extends Zend_Form
{
	public function init()
	{
		$this->setAction('/user/auth/');

		$this->addElement('hidden', 'return', array(    
        	'value' => Zend_Controller_Front::getInstance()->getRequest()->getParam('return', '/'),                       
		));		
		
		$username = $this->addElement('text', 'username', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'Alpha',
		array('StringLength', false, array(3, 20)),
		),
            'required'   => true,
            'label'      => 'Логин:',
		));

		$password = $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'Alnum',
		array('StringLength', false, array(6, 20)),
		),
            'required'   => true,
            'label'      => 'Пароль:',
		));

		$login = $this->addElement('submit', 'login', array(
            'required' => false,
            'ignore'   => true,
            'label'    => 'Войти',
		));

		// We want to display a 'failed authentication' message if necessary;
		// we'll do that with the form 'description', so we need to add that
		// decorator.
		$this->setDecorators(array(
            'FormElements',
		array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
		array('Description', array('placement' => 'prepend')),
            'Form'
            ));
	}
}