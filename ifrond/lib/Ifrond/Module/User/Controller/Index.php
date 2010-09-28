<?php
class Ifrond_Module_User_Controller_Index extends Ifrond_Controller_Action_Public
{	
	
	
	public function postInit() {
		parent::postInit();		
		$this->getModelMapperUser();
		//$this->_helper->layout()->setLayout('inside');		
	}
	
	public function indexAction ()
	{
			
	}
	public function getFormRegister() {
		$form = new Ifrond_Module_User_Form_Register();
		return $form;
	}
	public function registerAction ()
	{
		if ($this->_userMapper->isAuthed()) {
			$this->_forward('index');
		} else {
			$form = $this->getFormRegister();
			/* Проверяем,если были отправлены POST данные */
			if( $this->_request->isPost()) {
				/* Проверяем валидность данных формы */
				if ( $form->isValid($this->_getAllParams()) )
				{
					$row = array();
					$row['username'] = $form->getValue('login');
					$row['password'] = $form->getValue('pwd');
					$row['email'] = $form->getValue('email');
					$user = $this->_userMapper->createNew();
					if ($user->checkUnique($row)) {
						$user->register($row);
						$values = $user->getRow();
						$values['password'] = $row['password'];
						$this->view->values = $values;
						$mt = $this->view->render('index/registermail.phtml');
						//_p($mt);
						$user->sendConfirmation($mt, $values);
						$this->view->registered = true;
												
					} else {
						$this->view->registerForm = $form;
						$this->view->registered = false;
						$this->view->error = 'Пользователь с указанным именем или электронной почтой уже существует';
					}						
				}
			} else {
				$this->view->registerForm = $form;
			}
		}
	}
	public function confirmAction ()
	{
		$code = $this->_getParam('code');
		$user = $this->_userMapper->createNew();
		$confirm = $user->cofirm($code);
		if ($confirm == false) {
			$this->view->error = 'Недействительный код активации';
		}
	}	
}