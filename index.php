<?php

require('app/autoload.php');

if (!isset($_SESSION['chat_login']['id_user'],
	   $_SESSION['chat_login']['user'],
	   $_SESSION['chat_login']['id_room'],
	   $_SESSION['chat_login']['timestamp'])) {
	$_SESSION['chat_login'] = array();
	header('location: login.php');
	exit;
}

$id_user 	= $_SESSION['chat_login']['id_user'];
$user 		= Filters::convert($_SESSION['chat_login']['user']);
$id_room 	= $_SESSION['chat_login']['id_room'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" /> 
	<title>
		<?php 
		
		echo $_SESSION['chat_login']['room'].' - ';
		echo TITLE; 
		
		?>
	</title> 
	<link href="<?php echo URL;?>css/chat.styles.css" rel="stylesheet" media="all" type="text/css" />
	<script type="text/javascript" language="javascript" src="<?php echo URL;?>js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" language="javascript" src="<?php echo URL;?>js/jquery-ui-1.8.5.custom.min.js"></script>
	<script type="text/javascript" language="javascript" src="<?php echo URL;?>js/jquery.scrollTo-min.js"></script>
	<script type="text/javascript" language="javascript" src="<?php echo URL;?>js/jquery.sort.js"></script>
	<script type="text/javascript" language="javascript" src="<?php echo URL;?>js/functions.js"></script>
</head>
<body>

	<div id="header">
		<h1>chatNow</h1>
		<a href="logout.php" id="logout"><?=SiteLocale::setContent('Logout');?></a>
		<div id="room_name">
			<p><?=SiteLocale::setContent('Room:');?> <span><?php echo $_SESSION['chat_login']['room'];?></span></p>
		</div>
	</div>
	
	<div id="menu">
		<h4 id="users_online"><?=SiteLocale::setContent('Users online:');?></h4>
		<a href="#" id="user_0" rel="0" class="active_user_talk"><?=SiteLocale::setContent('All users');?></a>
		<?php
		
		echo '<span class="user_me"><a href="#" id="user_'.$_SESSION['chat_login']['id_user'].'" rel="'.$_SESSION['chat_login']['id_user'].'">'.Filters::convert($_SESSION['chat_login']['user']).'</a></span>';
		
		?>
		<div id="box_users">
			<?php
			
			$filters = array('all' => 'convert');
			$users_model = new UsersModel;
			$users_model->setCond('id_room = '.$id_room);
			$users_model->setCond('active = 1');
			$users_model->setCond('id <> '.$id_user);
			$users_model->setOrderBy('user asc');
			$users = $users_model->addFilter($users_model->load_all(), $filters);
			
			foreach ($users as $load) {
				echo '<a href="#" id="user_'.$load['id'].'" rel="'.$load['id'].'">'.$load['user'].'</a>';
			}
			
			?>
		</div>
	</div>
	
	<div id="content"></div>
	
	<div id="tools">
		<div id="box_talk"></div>
		<div id="box_tools">
			<form action="#" id="form_send" method="post">
				<div id="box_user_tools">
					<p id="name_user">
						<span><?php echo $user; ?></span>
					</p>
					<div id="box_settings">
						<div>
							<input type="checkbox" name="reserved" id="reserved" value="1" />  <label for="reserved"><?=SiteLocale::setContent('private message');?></label>
						</div>
						<div>
							<input type="checkbox" name="scroll_page" id="scroll_page" checked="checked" /> <label for="scroll_page"><?=SiteLocale::setContent('automatic scroll');?></label>
						</div>
					</div>
				</div>
				<div id="box_message_tools">
					<input type="hidden" name="id_user" id="id_user" value="<?php echo $id_user; ?>" /> 
					<input type="hidden" name="to_user" id="to_user" value="0" /> 
					<span id="message_box">
						<input type="text" size="10" maxlength="255" name="message" id="message" /> 
						<button type="submit" value="<?=SiteLocale::setContent('Send');?>"><?=SiteLocale::setContent('Send');?></button>
					</span>
					<div class="box_emoticons">
						<?php
						
						echo Emoticons::getLinks();
						
						?>
					</div>
				</div>
			</form>
		</div>
	</div>
	
	<div id="box_alert">
		<p class="center"><?=SiteLocale::setContent('You sure?');?></p>
		<p class="center">
			<a href="logout.php"><?=SiteLocale::setContent('Yes');?></a> 
			<a href="#" id="remove_box"><?=SiteLocale::setContent('No');?></a>
		</p>
	</div>

</body>
</html>
