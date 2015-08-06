<?php
require_once BASE_PATH . '/views/view.php';

Class BaseController {
	$access_level = 0;
	$user;

	function __construct() {
		$user = Auth::checkAuth($this->access_level);
		if (isset($user['error'])) {
			Utilities::errorPage('unauthorized', $user['error']);
		}
	}
}
