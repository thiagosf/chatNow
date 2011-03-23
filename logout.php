<?php

require('app/autoload.php');

if (isset($_SESSION['chat_login']['id_user'])) {

	// Avisa a exit do user
	AlertModel::addExit($_SESSION['chat_login']['user'], $_SESSION['chat_login']['id_user'], $_SESSION['chat_login']['id_room']);
	
	// Deleta user
	$users_model = new UsersModel;
	$users_model->delete($_SESSION['chat_login']['id_user']);
	
}

$_SESSION['chat_login'] = array();
header('location: login.php');
exit;

?>