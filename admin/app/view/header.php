<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" /> 
	<title>Admin</title> 
	<link href="css/styles.css" rel="stylesheet" media="all" />
	<script type="text/javascript" language="javascript" src="js/jquery-1.4.2.min.js"></script>
</head>
<body>

	<div id="header">
		<h1>Admin chat</h1>
	</div>
	
	<?php
	
	if (isset($_SESSION['user_admin'])) {
		
	?>
	<div id="nav">
		<a href="index.php">Users</a> | 
		<a href="rooms.php">Rooms</a> | 
		<a href="blocked.php">IP Blocked</a> |
		<a href="admin_users.php">Admin Users</a> | 
		<a href="logout.php">Logout</a>
	</div>
	<?php
	
	}
	
	?>