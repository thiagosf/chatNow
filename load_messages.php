<?php

require_once('app/autoload.php');

// Checks if is logged user
if (isset($_SESSION['chat_login']['id_user'])) {
	// Return
	$return = '';
	
	// Disables users stranded
	UsersModel::userDeleteIdle();

	// Condition
	$condition  = '(to_user = 0 OR id_user = '.$_SESSION['chat_login']['id_user'].' ';
	$condition .= 'OR to_user = '.$_SESSION['chat_login']['id_user'].' ';
	$condition .= 'OR (to_user != '.$_SESSION['chat_login']['id_user'].' AND reserved = 0) ';
	$condition .= 'OR (id_user = 0 AND to_user = 0)) ';

	// Load messages
	$messages = new MessagesModel;
	$messages->setCond($condition);
	$messages->setCond('id_room = '.$_SESSION['chat_login']['id_room']);
	$messages->setCond('timestamp >= "'.date('Y-m-d H:i:s', $_SESSION['chat_login']['last']).'"');
	$messages->setOrderBy('timestamp ASC');
	$load = $messages->load_all();
	
	$_SESSION['chat_login']['last'] = $_SERVER['REQUEST_TIME'];
	$_SESSION['chat_login']['messages'] = isset($_SESSION['chat_login']['messages']) ? $_SESSION['chat_login']['messages'] : array();
	$_SESSION['chat_login']['last_message'] = isset($_SESSION['chat_login']['last_message']) ? $_SESSION['chat_login']['last_message'] : $_SERVER['REQUEST_TIME'];
		
	foreach ($load as $line) {
	
		if (in_array($line['id'], $_SESSION['chat_login']['messages'])) {
			continue;
		}
		else {
			$_SESSION['chat_login']['messages'][] = $line['id'];
			if ($_SERVER['REQUEST_TIME'] > ($_SESSION['chat_login']['last_message'] + 120)) {
				$_SESSION['chat_login']['last_message'] = $_SERVER['REQUEST_TIME'];
				$_SESSION['chat_login']['messages'] = array();
			}
		}
		
		$id_user 		= $line['id_user'];
		$to_user 		= $line['to_user'];
		$user_name 		= Filters::convert(htmlentities($line['user']));
		
		// Images active condition
		if (ACTIVE_IMAGES) {
			$message 		= $line['message'];
			$image 			= $message;
			$imagesize		= @getimagesize($image);
			$accepts		= array('image/jpeg', 'image/jpg', 'image/gif', 'image/png');
			if (preg_match('/^(http:\/\/)(.*)(\.gif|\.png|\.jpg|\.jpeg)$/', $message) && isset($imagesize['mime']) && in_array($imagesize['mime'], $accepts)) {
				$message = '<div class="image_message"><a href="'.$image.'" class="zoom_image"><img src="'.$image.'" style="width:40px;" /></a></div>';
				$image_message = true;
			}
		}		
		
		// Video youtube active condition
		if (ACTIVE_VIDEOS) {
			if (preg_match('/^(http:\/\/\w\w\w\.youtube\.com\/\watch\?v=)[A-Z0-9]{10,}$/i', $message)) {
				$explode  = explode('v=', $message);
				$youtube  = $explode[1];
				$message  = '<div class="video_youtube">';
					$message .= '<object width="640" height="385">';
						$message .= '<param name="movie" value="http://www.youtube.com/v/'.$youtube.'?fs=1&amp;hl=pt_BR"></param>';
						$message .= '<param name="allowFullScreen" value="true"></param>';
						$message .= '<param name="allowscriptaccess" value="always"></param>';
						$message .= '<embed src="http://www.youtube.com/v/'.$youtube.'?fs=1&amp;hl=pt_BR" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="640" height="385"></embed>';
					$message .= '</object>';
				$message .= '<div>';
				$video_message = true;
			}
		}
		
		// Message commom
		if (!isset($image_message) && !isset($video_message)) {
			$message 		= Filters::convert(htmlentities($line['message']));
			$message		= Emoticons::transform($message);
		}
		
		// Class box
		$class_box 		= (!$line['id_user'] && !$line['to_user']) ? $line['type'] : 'box_msg';
		
		// Alert message: entry or exit
		if ($line['type']) {
			$message = SiteLocale::setContent($message);
			
			$class_box = 'box_msg_'.$line['type'];
			
			if (MESSAGE_TYPE == 'inline') {
				$class_box .= ' box_msg_inline';
			}
			
			$return .= '<div class="'.$class_box.'">';
				$return .= '<span class="head_msg">';
					$return .= '<span class="time_msg">'.date('H:i:s', strtotime($line['timestamp'])).'</span> ';
					$return .= '<span class="user_name" rel="'.$id_user.'">'.$user_name.'</span> ';
				$return .= '</span> ';
				$return .= '<span class="message_user_'.$line['type'].'">'.$message.'</span>';
			$return .= '</div>';
		}
		// Common message
		else {
			// Whose behalf he is receiving
			$name_to = SiteLocale::setContent('All users');
			$users = new UsersModel;
			$users->setCond('active = 1');
			$users->setCond('id = '.$line['to_user']);
			$user = $users->load_all();
			
			if (isset($user[0]) && count($user[0])) {
				$name_to = Filters::convert(htmlentities($user[0]['user']));
			}
			
			$class_box .= ($line['reserved'] ? ' box_msg_reserved inline_green' : '');
			$class_box .= ($line['to_user'] == $_SESSION['chat_login']['id_user'] ? ' to_you_message' : '');
			$class_box .= ($line['id_user'] == $_SESSION['chat_login']['id_user'] ? ' for_you_message' : '');
			
			if (MESSAGE_TYPE == 'inline') {
				$class_box .= ' box_msg_inline';
			}
			
			$return .= '<div class="'.$class_box.'">';
				$return .= '<span class="head_msg">';
					$return .= '<span class="time_msg">'.date('H:i:s', strtotime($line['timestamp'])).'</span> ';
					$return .= '<span class="user_name" rel="'.$id_user.'">'.$user_name.'</span> ';
					$return .= (($line['reserved']) ? '<b>('.SiteLocale::setContent('reservedly').')</b> ' : '').' '.SiteLocale::setContent('tells').' ';
					$return .= '<span class="name_to" rel="'.$to_user.'">'.$name_to.'</span>: ';
					$return .= '<span class="remove_message">'.SiteLocale::setContent('Hide message').'</span> ';
				$return .= '</span>';
				$return .= '<span class="message_sent">'.$message.'</span>';
			$return .= '</div>';
		}
	}
	
	echo $return;
	
}
else {
	echo 'error';
}

?>
