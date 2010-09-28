<?php
class Ifrond_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract {
	/**
	 * Переменные для хранения сущностей Аутентификации и Управления правами
	 */

	private $_auth;
	/*
	 * @var User_Model_Acl
	 */
	private $_acl;

	/**
	 * Определение переходов при недопустимости текущей роли и/или аутентификации
	 * естественно, такие экшены у вас должны быть созданы и работать :)
	 */
	protected $_noAuth = array(
 		'module'     => 'user',
 		'controller' => 'error',
 		'action'     => 'noauth');
	protected $_noAcl  = array(
 		'module'     => 'user',
 		'controller' => 'error',
 		'action'     => 'noacl');

	/**
	 * @param resource (Zend_Auth) Объект аутентификации
	 * @param resource (App_Acl) Объект управления правами
	 * @return void
	 */
	public function __construct($auth, $acl)
	{
		$this->_auth = $auth; /* @var $auth User_Model_Acl */
		$this->_acl  = $acl;
	}

	/**
	 * Перехват функции preDispatch(  )
	 */
	public function preDispatch( Zend_Controller_Request_Abstract $request)
	{

		/**
		 * Если пользователь авторизирован, то получаем его "роль"
		 * (хранится в БД вместе с другой инфой и переноситься в сессию при аутентификации (см. выше)
		 * если нет, то он "гость"
		 */
		if ($this->_auth->hasIdentity()) {
			$role = $this->_auth->getIdentity()->role;
		} else {
			$role = 'guest';
		}


		/* Определяем параметры запрос */
		$controller  = $request->controller;
		$action      = $request->action;
		$module     = $request->module;
		$resource   = $request->module;

		/* Проверяем уровень доступа пользователя к ресурсу */
		if( $this->_acl->has($resource) )
		$resource = null;

		/* Если доступ не разрешен - выясняем по какой причине (туда и отсылаем :) */
		if( !$this->_acl->isAllowed($role, $module, $controller) ) {
			list($module, $controller, $action)  =( !$this->_auth->hasIdentity() ) ?  array_values($this->_noAuth) : array_values($this->_noAcl);

			/* Определяем новые данные запроса */
			$request->setModuleName($module);
			$request->setControllerName($controller);
			$request->setActionName($action);
		}
	}
}