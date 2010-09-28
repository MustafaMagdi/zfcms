<?php

/**
 * @look Zend_Acl
 * Contain roles and right for ACL processing
 */
class Ifrond_Module_User_Model_Acl extends Zend_Acl
{
	public function __construct()
	{
		/* Роль по умолчанию */
		$roleGuest = new Zend_Acl_Role('guest');

		/* Создаем "пользователя" и "админа" использую наследование ролей */
		$this->addRole(new Zend_Acl_Role('guest'))
		->addRole(new Zend_Acl_Role('user'), 'guest')
		->addRole(new Zend_Acl_Role('admin'), 'user');

		/* Добавляем новые ресурсы */
		$this->add(new Zend_Acl_Resource('user'))
		->add(new Zend_Acl_Resource('cp'))
		->add(new Zend_Acl_Resource('block'))
		->add(new Zend_Acl_Resource('geo'))
		->add(new Zend_Acl_Resource('ref'))
		->add(new Zend_Acl_Resource('tag'))
		->add(new Zend_Acl_Resource('page'))
		->add(new Zend_Acl_Resource('map'))
		->add(new Zend_Acl_Resource('search'))
		->add(new Zend_Acl_Resource('feedback'))
		->add(new Zend_Acl_Resource('news'));

		/* Определяем права доступа с учетом порядка наследования ролей */
		$this->allow(null, null, null);
		$this->deny(null, null, 'admin');
		$this->deny(null, null, 'user');
		$this->allow('admin', null, 'admin');
		$this->allow('admin', 'cp', null);
		$this->allow('user', null, 'user');
		
	}
	
}