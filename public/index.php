<?php

require_once __DIR__.'/../vendor/autoload.php';

$container = require __DIR__.'/../app/services.php';
$config = $container->get(App\Library\Configuration::class);
$basepath = $config->get('environment.basepath', '');

$request_uri = str_remove_prefix(rawurldecode(strtok($_SERVER['REQUEST_URI'], '?')), $basepath);

// Let the PHP webserver handle assets.
if (str_begins_with($request_uri, '/assets/')) {
	return false;
}

// Replace duplicate slashes at end.
if ($request_uri !== '/' && $request_uri[-1] === '/') {
	$request_uri = substr($request_uri, 0, -1);
}

$routes = require __DIR__.'/../app/routes.php';
$dispatcher = FastRoute\cachedDispatcher($routes, [
	'cacheFile' => __DIR__.'/../data/cache/routes',
	'cacheDisabled' => ($config->get('environment.phase') !== 'production')
]);

$routeinfo = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $request_uri);
if ($routeinfo[0] !== FastRoute\Dispatcher::FOUND) {
	$routeinfo[1] = 'NotFoundController';
	$routeinfo[2] = [];
}

$request = Symfony\Component\HttpFoundation\Request::createFromGlobals();
$controller_name = "App\\Controllers\\${routeinfo[1]}";
$controller = new $controller_name($request, $routeinfo[2], $container);

bootstrap_eloquent($config);

$controller->start()->send();
