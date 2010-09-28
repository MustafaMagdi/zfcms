<?php

function initZFPack() {
	require PATH_APP.'/zfpacks/pack.php';
	require PATH_APP.'/zfpacks/__autoload.php';
	define('__AUTOLOAD_CACHE_DIR', PATH_APP.'/zfpacks/classes');
	define('__AUTOLOAD_MUTEX_FILE', PATH_APP.'/zfpacks/__is_autoload');
	spl_autoload_register('__autoload');
	register_shutdown_function('__autoload');
}

defined('APPLICATION_ENV')
|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

if (APPLICATION_ENV != 'production') {
	ini_set("display_errors","1");
	error_reporting(E_ALL);
}

date_default_timezone_set('Europe/Moscow');

defined('PATH_WEBROOT') || define('PATH_WEBROOT', realpath(dirname(__FILE__)));
defined('PATH_PUB') || define('PATH_PUB', realpath(PATH_WEBROOT . '/pub'));
defined('PATH_IFROND') || define('PATH_IFROND', realpath(PATH_WEBROOT . '/../ifrond'));
defined('PATH_APP') || define('PATH_APP', realpath(PATH_IFROND . '/app'));
defined('PATH_TEMP') || define('PATH_TEMP', realpath(PATH_IFROND . '/tmp'));
defined('PATH_LIB') || define('PATH_LIB', realpath(PATH_IFROND . '/lib'));

defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(PATH_APP));

set_include_path(implode(PATH_SEPARATOR, array(
	PATH_LIB,
	PATH_APP,
	realpath(PATH_APP . '/modules/'),
	get_include_path(),
)));

//initZFPack();

/** Zend_Application */
require_once 'Zend/Application.php';
require_once 'Zend/Loader/Autoloader.php';

$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Ifrond_');

// Create application, bootstrap, and run
$application = new Zend_Application(
	APPLICATION_ENV,
	PATH_APP . '/configs/application.ini'
);
$application->bootstrap()->run();