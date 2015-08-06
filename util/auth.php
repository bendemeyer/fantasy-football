<?php

Class Auth {
	static function checkAuth($access_level) {
		if (isset($_SESSION['user']) && $_SESSION['user']['access_level'] >= $access_level) {
			return $_SESSION['user'];
		} else if (!isset($_SESSION['user']) && $access_level == 0) {
			return array();
		} else {
			if (isset($_SESSION['user']) {
				$error_code = RESTRICTED_ACCESS;
			} else {
				$error_code = NOT_LOGGED_IN;
			}
			return array(
				'error' => $error_code;
			);
		}
	}

	$pdo;

	__construct() {
		$this->pdo = new PDO("mysql:host={$app['database']['host']};dbname={$app['database']['name']}", $app['database']['user'], $app['database']['password']);
	}

	__destruct() {
		$this->pdo = null;
	}

	function login($email, $password) {
		$stmt = $this->pdo->prepare("SELECT password FROM users WHERE email = :email");
		$stmt->bindParam(':email', $email);
		$stmt->execute();
		$user = $stmt->fetch(PDO::FETCH_ASSOC);
		unset($stmt);

		if (empty($user) || !password_verify($password, $user['password'])) {
			return false;
		}
		unset($user['password']);
		return $user;
	}

	function createUser($email, $password, $team) {
		$stmt = $this->pdo->prepare("INSERT INTO users
										(email, password, access_level, team) VALUES
										(:email, :password, :access_level, :team)");
		$stmt->bindParam(':email', $email);
		$stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT));
		$stmt->bindParam(':access_level', ACCESS_LEVEL_OWNER);
		$stmt->bindParam(':team', $team);
		$stmt->execute();
		unset($stmt);
	}

	function inviteUser($email, $team) {
		$stmt = $this->pdo->prepare("CREATE TABLE IF NOT EXISTS invited_users (
									  email VARCHAR(255),
									  code VARCHAR(255),
									  team TINYINT) ENGINE=InnoDB");
		$stmt->execute();
		$code = bin2hex(openssl_random_pseudo_bytes(8));
		$stmt = $this->pdo->prepare("INSERT INTO invited_users
										(email, code, team) VALUES
										(:email, :code, :team)");
		$stmt->bindParam(':email', $email);
		$stmt->bindParam(':code', $code);
		$stmt->bindParam(':team', $team);
		$stmt->execute();
		unset($stmt);

		$subject = "You've been invited to play fantasy football!";
		$message = "<html><body><p>You have been invited to manage a fantasy football team with " . $app['name'] .
				   ". Please click the link below to accept the invitation and create your account.</p>" .
				   "<p><a href='" . $app['location'] . "/register?email=$email&code=$code'>Accept your invitation!</a></p></body></html>";

		$headers = "From: " . $app['email']['no-reply'] . "\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

		mail($email, $subject, $message, $headers);

		return json_encode(array('user' => $email));
	}

	function checkInvitation($email, $code) {
		$stmt = $this->pdo->prepare("SELECT team FROM invited_users WHERE email = :email AND code = :code");
		$stmt->bindParam(':email', $email);
		$stmt->bindParam(':code', $code);
		$stmt->execute();
		$result = $stmt->fetch();

		if (empty($result)) {
			return false;
		}
		else {
			return $result['team'];
		}
	}

	function registerUser($email, $password, $code, $team) {
		$invited = $this->checkInvitation($email, $code);
		if (!$invited || $invited != $team) {
			return false;
		}
		$this->createUser($email, $password, $team);
	}
}
