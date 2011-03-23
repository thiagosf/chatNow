<?php

// Info server
$server_name 	= $_SERVER['SERVER_NAME'];
$pathinfo 		= pathinfo($_SERVER['PHP_SELF']);
$dir_chat		= $pathinfo['dirname'].'/';
$url			= 'http://'.$server_name.$dir_chat;

// Constants

// URL of chat
define('URL', $url); 

// Default language
define('LANGUAGE', (isset($language) ? $language : 'pt_br')); 

// Title for chat
define('TITLE', 'chatNow'); 

// Time for delete user; in seconds
define('TIME_USER_IDLE', 20); 

// Time for delete message; in minutes
define('TIME_MESSAGE_OLD', 10); 

// Active emocticons
define('ACTIVE_EMOTICONS', true); 

// Message type: default|inline
define('MESSAGE_TYPE', 'inline'); 

// Active image inside message
define('ACTIVE_IMAGES', true); 

// Active video inside message
define('ACTIVE_VIDEOS', true); 

?>