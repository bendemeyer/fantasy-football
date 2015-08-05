<?php
define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/util/constants.php';
require_once BASE_PATH . '/util/routes.php';
require_once BASE_PATH . '/util/auth.php';
require_once BASE_PATH . '/util/utilities.php';

if (!isset($_SESSION['csrf_token'])) {
	$_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && (!isset $_POST['csrf_token'] || $_POST['csrf_token'] != $_SESSION['csrf_token'])) {
	require_once $routes['error']['file'];
	$controller = new $routes['error']['controller'];
	$controller->unauthorized(CSRF_MISMATCH);
	exit;
}

$request = Utilities::getRequestParts($_SERVER['REQUEST_URI']);
if (isset($routes[$request->route])) {
	require_once $routes[$request->route]['file'];
	$controller = new $routes[$request->route]['controller'];
	if (method_exists($controller, $request->method)) {
		$controller->{$request->method}($request->params);
	}
}

require_once $routes['error']['file'];
$controller = new $routes['error']['controller'];
$controller->notFound();