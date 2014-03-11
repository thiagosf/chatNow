<?php

require_once('app/autoload.php');

if (isset($_SESSION['chat_login']['id_user'])) {
	// Return
	$return = '';
	
	// Update dos users
	$users = new UsersModel;
	$users->setFields(array('timestamp'));
	$users->setData(array('timestamp' => date('Y-m-d H:i:s')));
	$users->update($_SESSION['chat_login']['id_user']);
	
	// Lendo as messages
	$users = new UsersModel;
	$users->setCond('id_room = '.$_SESSION['chat_login']['id_room']);
	$users->setCond('id != '.$_SESSION['chat_login']['id_user']);
	$users->setCond('active = 1');
	$users->setOrderBy('user asc');
	$load = $users->load_all();
	$json = array();
	
	foreach ($load as $line) {
		// Retorno
		$user = Filters::convert(htmlentities($line['user']));
		$return .= '<a href="#" id="user_'.$line['id'].'" rel="'.$line['id'].'">'.$user.'</a>';
		$json[] = '["'.$line['id'].'", "'.$user.'"]';
	}
	
	echo '['.implode(',', $json).']';
}
else {
	echo 'error';
}

?>
