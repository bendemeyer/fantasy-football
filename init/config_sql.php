<?php
require_once('../util/constants.php');

$pdo = new PDO("mysql:host={$app['database']['host']};dbname={$app['database']['name']}", $app['database']['user'], $app['database']['password']);

// create users table
$stmt = $pdo->prepare("CREATE TABLE IF NOT EXISTS users (
						id INT UNSIGNED NOT NULL AUTO_INCREMENT,
						email VARCHAR(255), 
						password VARCHAR(255),
						access_level TINYINT,
						team TINYINT
						PRIMARY KEY (id)) ENGINE=InnoDB");
$stmt->execute();
unset($stmt);

// create admin user
$admin_user_email = 'admin@admin.admin';
$admin_user_password = 'password';
$admin_user_level = ACCESS_LEVEL_ADMIN;
$stmt = $pdo->prepare("INSERT INTO users 
								(email, password, access_level) VALUES 
								(:email, :password, :access_level)");
$stmt->bindParam(':email', $admin_user_email);
$stmt->bindParam(':password', password_hash($admin_user_password, PASSWORD_DEFAULT));
$stmt->bindParam(':access_level', $admin_user_level);
$stmt->execute();
unset($stmt);

// create invited_users table
$stmt = $pdo->prepare("CREATE TABLE IF NOT EXISTS invited_users (
						email VARCHAR(255), 
						code VARCHAR(255), 
						team TINYINT) ENGINE=InnoDB");
$stmt->execute();
unset($stmt);

// create drafts table
$stmt = $pdo->prepare("CREATE TABLE IF NOT EXISTS drafts (
						id INT UNSIGNED NOT NULL AUTO_INCREMENT,
						name VARCHAR(255), 
						type TINYINT,
						PRIMARY KEY (id),
						UNIQUE(name)) ENGINE=InnoDB");
$stmt->execute();
unset($stmt);

// create picks table
$stmt = $pdo->prepare("CREATE TABLE IF NOT EXISTS picks (
						pick SMALLINT UNSIGNED,
						rank SMALLINT UNSIGNED, 
						player_name TINYTEXT,
						player_team TINYTEXT, 
						player_position TINYINT, 
						team TINYINT,
						draft TINYINT) ENGINE=InnoDB");
$stmt->execute();
unset($stmt);

unset($pdo);