<?php

require('app/inc/autoload.php');

unset($_SESSION['user_admin'], $_SESSION['password_admin']);

header('location: login.php');
exit;

?>