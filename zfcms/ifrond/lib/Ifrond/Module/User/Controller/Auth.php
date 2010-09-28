<?php
class Ifrond_Module_User_Controller_Auth extends Ifrond_Controller_Action_Public
{		
	
	public function postInit() {
		parent::postInit();		
		$this->getModelMapperUser();
		//$this->_helper->layout()->setLayout('inside');		
	}
	
	public function getFormLogin() {
		$form = new Ifrond_Module_User_Form_Login();
		return $form;
	}
	
	/**
	 * Аутентификация пользователей
	 */
	public function indexAction()
	{		
		if (!$this->_userMapper->isAuthed()) {
			/* Form_Login - это наследник Zend_From, который создает форму для авторизации */
			$loginForm = $this->getFormLogin();
			/* Проверяем,если были отправлены POST данные */
			if( $this->_request->isPost() )
			{
				/* Проверяем валидность данных формы */
				if ( $loginForm->isValid($this->_getAllParams()) )
				{
					$login = $loginForm->getValue('username');
					$pwd = $loginForm->getValue('password');
					if ($this->_userMapper->auth($login, $pwd)) {
						$prev = $loginForm->getValue('return');
						$this->_redirect($prev);
					} else {
						$this->view->authError = 'Неверный логин / пароль';
					}
				}
			}
			/* Если данные не передавались, то Выводим пользователю форму для авторизации */
			$this->view->loginForm = $loginForm;
		} else {
			$this->_redirect('/');
		}
	}

	/**
	 * "Выход" пользователя
	 */
	public function logoutAction ()
	{
		/* "Очищаем" данные об идентификации пользоваля */
		Zend_Auth::getInstance()->clearIdentity();
		/**
		 * Перебрасываем его на главную
		 * Желательно еще как-то оповестить пользователя о том, что он вышел
		 */
		$this->_redirect('/');
	}
}