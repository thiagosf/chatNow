<?php

require('app/inc/autoload.php');

if (isset($_POST) && !empty($_POST)) {
	$user = Filters::anti_sql_injection($_POST['user']);
	$password = md5($_POST['password']);
	
	$admin_model = new AdminModel;
	$admin_model->setCond('user = "'.$user.'"');
	$admin_model->setCond('password = "'.$password.'"');
	$load = $admin_model->load();
	
	if (!empty($load)) {
		$_SESSION['id_admin'] = $load[0]['id'];
		$_SESSION['user_admin'] = $load[0]['user'];
		$_SESSION['password_admin'] = $load[0]['password'];
	}
}

// Check login
if (isset($_SESSION['user_admin']) && isset($_SESSION['password_admin'])) {
	header('location: index.php');
	exit;
}

Template::getHeader();

?>
	<div id="content">
		<form method="post" name="login" action="login.php">
			<div class="block_field">
				<label for="user">User<label>
				<input type="text" name="user" id="user" size="30" />
			</div>
			<div class="block_field">
				<label for="password">Password<label>
				<input type="password" name="password" id="password" size="30" />
			</div>
			<div class="block_field">
				<button type="submit" value="Login">Login</button>
			</div>
		</form>
	</div>
<?php
Template::getFooter();
?>