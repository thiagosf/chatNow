<?php

require_once('app/autoload.php');

if (IpBlockModel::isBlocked()) {
	header('location: logout.php');
	exit;
}

if (isset($_POST['id_user']) && $_SESSION['chat_login']['id_user'] == $_POST['id_user'] && strlen(trim($_POST['message'])) >= 1) {

	// Post data
	$id_user 	= (int) $_POST['id_user'];
	$to_user 	= (int) $_POST['to_user'];
	$message 	= $_POST['message'];
	$message 	= strip_tags($message);
	$message 	= substr($message, 0, 255);
	$reserved 	= ($_POST['reserved'] == 'true') ? 1 : 0;
	
	// Check user active
	$users = new UsersModel;
	$users->setCond('id_room = '.$_SESSION['chat_login']['id_room']);
	$users->setCond('id = '.$id_user);
	$users->setCond('active = 1');
	$load = $users->load_all();
	
	if (empty($load)) {
		header('location: logout.php');
		exit;
	}
	
	// To user
	$users = new UsersModel;
	$users->setCond('id_room = '.$_SESSION['chat_login']['id_room']);
	$users->setCond('id = '.$to_user);
	$load = $users->load_all();
	
	// Checks if user belongs to the same room
	if (isset($load[0]) && count($load[0])) {
		$data = new StdClass;
		$data->timestamp = date('Y-m-d H:i:s');
		$fields = array_keys((array) $data);
		
		// Update timestamp of user
		$users = new UsersModel;
		$users->setFields($fields);
		$users->setData($data);
		$users->update($id_user);
		
		$send = true;
	}
	else if ($to_user == 0) {
		$send = true;
	}
	else {
		$to_user = 0;
		$send = true;
	}
	
	// Send message
	if (isset($send) && 
			(!isset($_SESSION['chat_login']['last_send']) || 
			(isset($_SESSION['chat_login']['last_send']) && time() >= ($_SESSION['chat_login']['last_send']) ))) {
	
		// Last message sent
		$_SESSION['chat_login']['last_send'] = $_SERVER['REQUEST_TIME'];
		
		// Delete old messages
		$messages = new MessagesModel;
		$messages->setCond('timestamp < "'.(date('Y-m-d H:i:s', strtotime('-'.TIME_MESSAGE_OLD.' minutes'))).'"');
		$messages->delete();
	
		// Data
		$data 				= new StdClass;
		$data->user 		= Filters::convert($_SESSION['chat_login']['user']);
		$data->id_user 		= $_SESSION['chat_login']['id_user'];
		$data->message 		= Filters::convert($message);
		$data->to_user 		= $to_user;
		$data->id_room 		= $_SESSION['chat_login']['id_room'];
		$data->reserved 	= $reserved;
		$data->timestamp 	= date('Y-m-d H:i:s');
		$fields 			= array_keys((array) $data);
		
		// Insert message
		$messages = new MessagesModel;
		$messages->setFields($fields);
		$messages->setData($data);
		$messages->insert();
		
	}
}

?>