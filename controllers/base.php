<?php
require_once BASE_PATH . '/views/view.php';

Class BaseController {
	$access_level = 0;
	$user;
	
	function __construct() {
		$user = Auth::checkAuth($this->access_level);
		if (isset($user['error'])) {
			require_once $routes['error']['file'];
			$controller = new $routes['error']['controller'];
			$controller->unauthorized($user['error']);
		}
	}
}