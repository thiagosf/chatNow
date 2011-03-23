<?php

require('app/autoload.php');

// Check ip blocked
$ip_block_model = new IpBlockModel;
$ip_block_model->setCond('ip = "'.$_SERVER['REMOTE_ADDR'].'"');
$ip_block = $ip_block_model->load();

if (empty($ip_block)) {
	header('location: index.php');
	exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" /> 
	<title><?=TITLE?></title> 
	<link href="css/styles.css" rel="stylesheet" media="all" />
	<script type="text/javascript" language="javascript" src="<?=URL?>js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" language="javascript" src="<?=URL?>js/jquery.scrollTo-min.js"></script>
	<script type="text/javascript" language="javascript" src="<?=URL?>js/functions.js"></script>
</head>
<body>

<h2>Your IP has been blocked</h2>

	
</body>
</html>