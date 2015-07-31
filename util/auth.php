<?php
require_once '../util/constants.php';

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
	
	$pdo;
	
	__construct() {
		$this->pdo = new PDO('mysql:host=localhost;dbname=fantasyfootball', $user, $pass);
		$stmt = $this->pdo->prepare("CREATE TABLE IF NOT EXISTS users (
									  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
									  email VARCHAR(255), 
									  password VARCHAR(255),
									  access_level TINYINT,
									  team TINYINT
									  PRIMARY KEY (id)) ENGINE=InnoDB");
		$stmt->execute();
		unset($stmt);
	}
	
	__destruct() {
		$this->pdo = null;
	}
	
	function login($email, $password) {
		$stmt = $this->pdo->prepare("SELECT password FROM users WHERE email = :email");
		$stmt->bindParam(':email', $email);
		$stmt->execute();
		$user = $stmt->fetch();
		unset($stmt);
		
		if (!password_verify($password, $user['password'])) {
			return false;
		}
		unset($user['password']);
		$_SESSION['user'] = $user;
	}
	
	function createUser($email, $password, $team) {
		$stmt = $this->pdo->prepare("INSERT INTO users 
										(email, password, access_level, team) VALUES 
										(:email, :password, :access_level, :team)");
		$stmt->bindParam(':email', $email);
		$stmt->bindParam(':password', $password);
		$stmt->bindParam(':access_level', ACCESS_LEVEL_OWNER);
		$stmt->bindParam(':team', $team);
		$stmt->execute();
		unset($stmt);
	}
	
	function inviteUser($email, $team) {
		
	}
}