<?php
/**
 * Not permanent fix to apache headers rename
 */
$env = getenv('APP_ENV') ? : getenv('REDIRECT_APP_ENV') ? : 'production';

if(isset($_SERVER["REDIRECT_HTTP_AUTHORIZATION"])){
    $_SERVER["HTTP_AUTHORIZATION"] = $_SERVER["REDIRECT_HTTP_AUTHORIZATION"];
}
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
$appDir = '';
if($env == 'production'){
    $appDir = '/columnisexpress/current';
}

chdir(dirname(__DIR__).$appDir);
// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

// Setup autoloading
require 'init_autoloader.php';

if (!defined('APPLICATION_PATH')) {
    define('APPLICATION_PATH', realpath(__DIR__ . '/..'.$appDir.'/'));
}
$appConfig = include APPLICATION_PATH . '/config/application.config.php';
if (file_exists(APPLICATION_PATH . '/config/development.config.php')) {
    $appConfig = Zend\Stdlib\ArrayUtils::merge($appConfig, include APPLICATION_PATH . '/config/development.config.php');
}
// Run the application!
Zend\Mvc\Application::init($appConfig)->run();
