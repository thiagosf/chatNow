<?php

session_start();

$pathinfo = pathinfo($_SERVER['PHP_SELF']);
$pagina = $pathinfo['basename'];

if (!isset($_SESSION['user_admin']) || !isset($_SESSION['password_admin'])) {
	if ($pagina != 'login.php') {
		header('Location: login.php');
		exit;
	}
}

?>