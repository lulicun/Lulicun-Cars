<?php

define('BASE_PATH', realpath(dirname(__FILE__) . '/../'));
define('APPLICATION_PATH', BASE_PATH . '/application');

// Include path
set_include_path(
    BASE_PATH . '/library' .
    PATH_SEPARATOR . BASE_PATH . '/vendor' .
    PATH_SEPARATOR . BASE_PATH . '/application/controllers' .
    PATH_SEPARATOR . get_include_path()
);

//define('APPLICATION_ENV', 'production');

// Define application environment
defined('APPLICATION_ENV')
 || define('APPLICATION_ENV',
    (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV')
        : 'local'));

// Autoload composer installed libs
require_once BASE_PATH . '/vendor/autoload.php';

// Zend_Application
require_once 'Zend/Application.php';

date_default_timezone_set('UTC');

// Save temp data
global $memcache;
$memcache = new Memcached();
$memcache->addServer('localhost', 11211);

if (!$config = $memcache->get(APPLICATION_ENV . '-config')) {
    $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
    $memcache->set(APPLICATION_ENV . '-config', $config);
}

$application = new Zend_Application(
    APPLICATION_ENV,
    $config
);

$application->bootstrap();