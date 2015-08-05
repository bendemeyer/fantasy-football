<?php

$routes = array(
	'login' => array(
		'file'        => BASE_PATH . '/controllers/login.php',
		'controller'  => 'LoginController'
	),
	'error'      => array(
		'file'        => BASE_PATH . '/controllers/error.php',
		'controller'  => 'ErrorController'
	),
	'landing'    => array(
		'file'        => BASE_PATH . '/controllers/landing.php',
		'controller'  => 'LandingController'
	),
	'mockdraft'  => array(
		'file'        => BASE_PATH . '/controllers/mockdraft.php',
		'controller'  => 'MockDraftController'
	),
	'admin'      => array(
		'file'        => BASE_PATH . '/controllers/admin.php',
		'controller'  => 'AdminController'
	),
);