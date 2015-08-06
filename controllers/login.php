<?php
require_once BASE_PATH . '/util/auth.php';

Class LoginController extends BaseController {

	function index($params) {

	}

	function login($params) {
		$auth = new Auth();
		$user = $auth->login($params['post']['email'], $params['post']['password']);
		if ($user) {
			$_SESSION['user'] = $user;
			$redirect_url = $app['location'] . '/landing';
			if (isset($params['post']['dest'])) {
				$redirect_url = hex2bin($params['post']['dest']));
			}
			echo json_encode(array('redirect' => $redirect_url));
			return;
		} else {
			echo json_encode(array('error' => 'Invalid email address or password'));
			return;
		}
	}

	function requestInvitation($params) {

	}

	function forgotPassword($params) {

	}
}
