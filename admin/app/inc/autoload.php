<?php

// Header
header('Content-type: text/html; charset=UFT-8', true);

// Default date
date_default_timezone_set('America/Sao_Paulo');

set_time_limit(0);

// Ob start
ob_start();

// Includes path
set_include_path(
	'app/'.PATH_SEPARATOR.
	'app/ado/'.PATH_SEPARATOR.
	'app/control/'.PATH_SEPARATOR.
	'app/model/'.PATH_SEPARATOR.
	'app/inc/'.PATH_SEPARATOR.
	'app/config/'.PATH_SEPARATOR.
	get_include_path()
);

/**
 * Autoload
 * --------------------------------------------
 */
function __autoload ($file) {
	$paths = array(
		'app/', 
		'app/ado/', 
		'app/control/', 
		'app/model/', 
		'app/inc/', 
		'app/config/',
	);
	
	foreach ($paths as $path) {
		if (is_file($path.$file.'.class.php')) {
			require($path.$file.'.class.php');
			break;
		}
	}
}

// Security
require('session_login.php');

// Functions 
function pr ($array) {
	echo '<div style="border:2px solid #000;padding:10px;background:#e1e1e1;margin:10px;">';
	echo '<pre>';
	print_r($array);
	echo '</pre>';
	echo '</div>';
}

// Get custom
$get = array();
if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING']) {
	$qs = $_SERVER['QUERY_STRING'];
	parse_str($qs, $get);
}

// Open con
DB::open();

?>