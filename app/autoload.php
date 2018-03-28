<?php

// Start session
session_start();

// Default date
date_default_timezone_set('America/Sao_Paulo');

// URL fopen
ini_set('allow_url_fopen', true);

// Info page
$pathinfo = pathinfo($_SERVER['PHP_SELF']);
$basename = $pathinfo['basename'];

// Header for charset UTF-8
// This is used because of bug IE ajax
if ($basename == 'index.php') {
	header('Content-type: text/html; charset=UFT-8', true);
}

// Show errors
error_reporting(E_ALL);

// Includes path
set_include_path(
	'app/'.PATH_SEPARATOR.
	'admin/app/'.PATH_SEPARATOR.
	'admin/app/ado/'.PATH_SEPARATOR.
	'admin/app/control/'.PATH_SEPARATOR.
	'admin/app/model/'.PATH_SEPARATOR.
	'admin/app/inc/'.PATH_SEPARATOR.
	'admin/app/config/'.PATH_SEPARATOR.
	get_include_path()
);

// Autload classes
function __autoload ($file) {
	$paths = array(	
		'app/', 
		'admin/app/', 
		'admin/app/ado/', 
		'admin/app/control/', 
		'admin/app/model/', 
		'admin/app/inc/', 
		'admin/app/config/'
	);
	
	foreach ($paths as $path) {
		if (is_file($path.$file.'.class.php')) {
			require($path.$file.'.class.php');
			break;
		}
	}
}

// Debug
function pr ($array) {
	echo '<div style="border:2px solid #000;padding:10px;background:#e1e1e1;margin:10px;">';
	echo '<pre>';
	print_r($array);
	echo '</pre>';
	echo '</div>';
}

// Language
$language = SiteLocale::getLanguage();

// Configuration
require_once('config.php');

?>