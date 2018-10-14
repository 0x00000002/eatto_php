<?php


require dirname(__DIR__).'/vendor/autoload.php';
require dirname(__DIR__).'/vendor/Twig/lib/Twig/Autoloader.php';

Twig_Autoloader::register();

spl_autoload_register(function ($class) {
	$root = dirname(__DIR__);
	$file = $root . '/' . str_replace('\\','/',$class).'.php';
	if (is_readable($file)) {
		require $root.'/'.str_replace('\\','/',$class).'.php';
	}
});

error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

session_start();

$config = new App\Config();
$config->init();

$router = new Core\Router();

$router->setRoutes('', ['controller' => 'Home', 'action' => 'index'] );
$router->setRoutes('logout', ['controller' => 'login','action' => 'logout']);
// $router->setRoutes('{controller}/{action}/{city:[a-z\s]+\,[a-z\s]*}');
//$router->setRoutes('{controller}/{pid:[0-9a-z\s]+}', ['action' => 'index'] );
$router->setRoutes('{controller}/{action}/{token:[\da-f]+}');
$router->setRoutes('{controller}/{action}');
$router->setRoutes('{controller}/{action}/');
$router->setRoutes('{controller}',['action' => 'index']);


/*
 * If you need something like /posts/123/edit
 */

$router->setRoutes('{controller}/{id:\d+}/{action}');



$router->dispatch($_SERVER['QUERY_STRING']);


