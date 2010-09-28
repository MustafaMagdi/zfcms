<?php

class Ifrond_Module_User_Model_Mapper_User extends Ifrond_Model_Mapper 
{
	/*
	 * @var Zend_Auth
	 */
	protected $_auth;
	protected $_info;
	protected $_dbTableName = 'user';
	
	
	public function init() {    	
    	$this->_auth = Zend_Auth::getInstance();    	
    }
	
	public function auth($username, $password) {
		/* "Получаем" соединение с БД из Mapper */
		$db = $this->getDbTable()->getDefaultAdapter();
		$dbTableName = $this->getDbTable()->info('name');
		/* Создаем адаптер в виде базы данных */
		$authAdapter = new Zend_Auth_Adapter_DbTable($db);
		
		/**
		 * Настраиваем правила выборки пользователей из БД
		 * Соответственно, имя таблицы,
		 * название поля с идентификатором пользователя,
		 * название поля для сверки "пароля"
		 */
		$authAdapter->setTableName($dbTableName)
		->setIdentityColumn('username')
		->setCredentialColumn('password')
		->setCredentialTreatment("MD5(?) AND `is_active` = '1'");
		
		

		/* Передаем в адаптер данные пользователя. MD5 выбрано совершенно случайно :) */
		$authAdapter->setIdentity($username);
		$authAdapter->setCredential($password);

		/* Собственно, процесс аутентификация */
		$resultAuth = $this->_auth->authenticate($authAdapter);
		/* Проверяем валидность результата */
		if( $resultAuth->isValid() )
		{
			/* Пишем в сессию необходимые нам данные (пароль обнуляем, он нам в сессии не нужен :) */
			$data = $authAdapter->getResultRowObject(null, 'password');
			$this->_auth->getStorage()->write($data);
			return true;

			/* Тут можно сообщить пользователю о том, что он вошел */
		} else {
			return false;
		}
	}
		
	public function getActive() {	
		if ($this->isAuthed()) {	
			$row = $this->_auth->getIdentity();		
			$user = $this->createNew((array) $row);
		} else {
			return false;
		}
		return $user;
	}
	
	public function isAuthed() {		
		return $this->_auth->hasIdentity();		
	}
}