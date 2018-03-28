<?php

require('app/autoload.php');

// Check ip blocked
$ip_block_model = new IpBlockModel;
$ip_block_model->setCond('ip = "'.$_SERVER['REMOTE_ADDR'].'"');
$ip_block = $ip_block_model->load();

// IP block check
if (!empty($ip_block)) {
	header('location: ip_block_message.php');
	exit;
}

// Desativa users ociosos
UsersModel::userDeleteIdle();

// Dados enviados
if (count($_POST)) {
	$user = substr($_POST['user'], 0, 20);
	$user = Filters::convert($user);
	
	$captcha = $_POST['captcha'];
	$id_room = (int) $_POST['id_room'];
	
	if ($_SESSION['captcha_code'] == $captcha) {
	
		// Rooms
		$rooms_model = new RoomsModel;
		$room = $rooms_model->load($id_room);
		
		if (count($room)) {
		
			// Users online
			$users_model = new UsersModel;
			$users_model->setFieldsSelect(array('user'));
			$users_model->setCond('id_room = '.$id_room);
			$users_model->setOrderBy('chat_users.id');
			$users = $users_model->load_all();
			$total = $users_model->count();
			
			if ($room['capacity'] > $total) {
			
				// Verifica se tem user com o mesmo nome
				$users_model = new UsersModel;
				$users_model->setCond('user = "'.$user.'"');
				$users = $users_model->load_all();
				
				// IP's bloqueados 
				$ip_model = new IpBlockModel;
				$ip_model->setCond('ip = "'.$_SERVER['REMOTE_ADDR'].'"');
				$ip = $ip_model->load_all();
				
				if (count($ip)) {
					$message = 'You are blocked from accessing chat.';
				}
				else if (count($users) == 0) {
					$data = new StdClass;
					$data->user 		= $user;
					$data->id_room 		= $id_room;
					$data->ip 			= $_SERVER['REMOTE_ADDR'];
					$data->timestamp 	= date('Y-m-d H:i:s', time());
					
					$fields = array_keys((array) $data);
					
					$users_model = new UsersModel;
					$users_model->setFields($fields);
					$users_model->setData($data);
					
					if ($users_model->insert()) {
						$id_user 								= $users_model->getId();
						$_SESSION['chat_login']['id_user'] 		= $id_user;
						$_SESSION['chat_login']['user'] 		= $user;
						$_SESSION['chat_login']['id_room'] 		= $id_room;
						$_SESSION['chat_login']['room'] 		= $room['room'];
						$_SESSION['chat_login']['timestamp'] 	= $data->timestamp;
						
						$_SESSION['chat_login']['id_alert'] 	= array();
						$_SESSION['chat_login']['last'] 		= time();
						
						// Aviso da entry do user
						AlertModel::addEntry($user, $id_user, $id_room);
					}
				}
				else {
					$message = 'The chosen username is already in use, choose another to enter.';
				}
				
			}
			else {
				$message = 'Room capacity was exhausted! Choose another room to enter.';
			}
		}
		else {
			$message = 'The room does not exists.';
		}
	}
	else {
		$message = 'Error code';
	}
}

// Se estiver logado redireciona para a index
if (isset($_SESSION['chat_login']['id_user']) && isset($_SESSION['chat_login']['user']) 
		&& isset($_SESSION['chat_login']['id_room']) 
		&& isset($_SESSION['chat_login']['timestamp'])) {
	header('location: index.php');
	exit;
}

// Code
$text = (string) ((rand() % 8999) + 1000);
 
// Save code in session
$_SESSION['captcha_code'] = $text;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" /> 
	<title><?=TITLE?> - Open Source Chat PHP &amp; MySQL</title> 
	
	<meta name="description" content="Chat Open Source desenvolvido com PHP e MySQL. Com suporte para envio de videos do Youtube e imagens. Construido com jQuery." />
	<meta name="keywords" content="chat, bate papo, php, mysql, open source, emoticons, youtube, imagens, jquery" />
	<meta name="content-language" content="pt-BR" />
	<meta name="language" content="pt-BR" />

	<link href="css/chat.styles.css" rel="stylesheet" media="all" type="text/css" />
	<link href="css/styles.css" rel="stylesheet" media="all" type="text/css" />
	<script type="text/javascript" language="javascript" src="<?=URL?>js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" language="javascript" src="<?=URL?>js/jquery.scrollTo-min.js"></script>
	<script type="text/javascript" language="javascript">
	$(document).ready(function() {
		$('#box_login form').submit(function() {
			if ($('#user').val() == '') {
				$('#user').focus();
				return false;
			}
			else if ($('#captcha').val() == '') {
				$('#captcha').focus();
				return false;
			}
			
		});
	});
	</script>
</head>
<body>

<div id="box_login">
	<?php

	if (isset($message)) {
		echo '<p class="message_error">'.SiteLocale::setContent($message).'</p>';
	}

	?>
	<h2>chatNow</h2>
	<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
		<div class="block_field">
			<label for="user">Nick</label>
			<input type="text" name="user" id="user" maxlength="20" /> 
		</div>
		<div class="block_field">
			<label for="id_room"><?=SiteLocale::setContent('Room');?></label>
			<select name="id_room" id="id_room">
			<?php
			
			$rooms_model = new RoomsModel;
			$rooms_model->setOrderBy('room');
			$rooms = $rooms_model->load_all();
			
			foreach ($rooms as $room) {
				$users_model = new UsersModel;
				$users_model->setCond('active = 1');
				$users_model->setCond('id_room = '.$room['id']);
				$users = $users_model->load_all();
				$total = $users_model->count();
				
				echo '<option value="'.$room['id'].'">'.$room['room'].' ('.$total.')</option>';
			}
			
			?>
			</select>
		</div>
		<div class="block_field">
			<img src="captcha.php" />
			<input type="text" name="captcha" id="captcha" maxlength="4" />
		</div>
		<div class="block_field block_submit">
			<button type="submit" value="<?=SiteLocale::setContent('Enter');?>"><?=SiteLocale::setContent('Enter');?></button>
		</div>
	</form>
</div>

<!-- 
<div id="update_log">
	<h2>Update log</h2>
	<h3>20/10/2010 - Version 1.0</h3>
	<ul>
		<li>Simple language support</li>
		<li>Images support</li>
		<li>Videos support</li>
		<li>Admin</li>
	</ul>
</div>

<div id="box_download">
	<a href="source/chatNow-21-10-2010.zip">Download</a>
</div>

<div id="footer">
	<a href="http://thiagosf.net">Thiago S.F.</a>
</div>
 -->
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-1966000-13']);
  _gaq.push(['_trackPageview']);

  (function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
	
</body>
</html>