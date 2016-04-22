<?php

require_once 'classes/Database.php';
require_once 'classes/ErrorHandler.php';
require_once 'classes/Validator.php';

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

$db = new Database;
$errorHandler = new ErrorHandler;

if(!empty($_POST))
{
	$validator = new Validator($db, $errorHandler);

	$validation = $validator->check($_POST, [
		'username' => [
			'required' => true,
			'maxlength' => 20,
			'minlength' => 6,
			'alnum' => true,
			'unique' => 'users'
		],
		'email' => [
			'required' => true,
			'maxlength' => 255,
			'email' => true,
			'unique' => 'users'
		],
		'password' => [
			'required' => true,
			'minlength' => 6
		],
		're_password' => [
			'match' => 'password'
		]
	]);

	if($validation->fails())
	{
		echo '<pre>', print_r($validation->errors()->all()), '</pre>';
	}
}

?>

<!doctype html>
<html>
<head>
	<meta charset="utf8">
	<title>Validator Lasse</title>
</head>
	<body>
		<form action="index.php" method="post">
		<div>
			Username: <input type="text" name="username" autocomplete="off">
		</div>
		<div>
			Email: <input type="text" name="email" autocomplete="off">
		</div>
		<div>
			Password: <input type="password" name="password" autocomplete="off">
		</div>
		<div>
			Retype Password: <input type="password" name="re_password" autocomplete="off">
		</div>
		<div>
			<input type="submit">
		</div>
		</form>
	</body>
</html>