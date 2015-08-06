<?php
define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/util/constants.php';
require_once BASE_PATH . '/util/route.php';
require_once BASE_PATH . '/util/auth.php';
require_once BASE_PATH . '/util/utilities.php';
require_once BASE_PATH . '/controllers/base.php';

if (!isset($_SESSION['csrf_token'])) {
	$_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && (!isset $_POST['csrf_token'] || $_POST['csrf_token'] != $_SESSION['csrf_token'])) {
	Utilities::errorPage('unauthorized', UNAUTHORIZED_CSRF_MISMATCH);
}

$request = Utilities::getRequestParts($_SERVER['REQUEST_URI']);

try {
	$route = new Route($request->route);
}
catch (Exception $e) {
	Utilities::errorPage('notFound', NOT_FOUND_NO_ROUTE);
}
require_once $route->getFile();
$controller = new {$route->getController()};
if (method_exists($controller, $request->method)) {
	$controller->{$request->method}($request->params);
} else {
	Utilities::errorPage('notFound', NOT_FOUND_NO_METHOD);
}
