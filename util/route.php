<?php

Class Route {
	private $routes = array(
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
		'draftadmin'  => array(
			'file'        => BASE_PATH . '/controllers/draftadmin.php',
			'controller'  => 'DraftAdminController'
		),
		'admin'      => array(
			'file'        => BASE_PATH . '/controllers/admin.php',
			'controller'  => 'AdminController'
		),
		'draft'      => array(
			'file'        => BASE_PATH . '/controllers/draft.php',
			'controller'  => 'DraftController'
		),
	);

	private $file;
	private $controller;

	__construct($route_string) {
		$this->file = $routes['route_string']['file'];
		$this->controller = $routes['route_string']['controller'];
		if (!$this->file || !$this->$controller) {
			throw new Exception('Route does not exist');
		}
	}

	public function getFile() {
		return $this->file;
	}

	public function getController() {
		return $this->controller;
	}
}
