<?php
require_once('../util/constants.php');

Class Auth {
	static function checkAuth() {
		if (isset($_SESSION['user'])) {
			return $SESSION['user'];
		}
		else {
			$dest = bin2hex($_SERVER['REQUEST_URI']);
			http_redirect("/login", array("dest" => $dest), false, HTTP_REDIRECT_FOUND);
			die;
		}
	}
}