<?php

/**
 * SimpleCaptcha
 */
 
// Start session
session_start();

// Header image
header("Content-type: image/png");
 
// Create image
$image = 'images/captcha.jpg';
if (is_file($image)) {
	$image_back = imagecreatefromjpeg($image);
}
else {
	$image_back = imagecreatetruecolor(100, 50);
	imagecolorallocate($image_back, 50, 50, 250);
}

// Code
$text = $_SESSION['captcha_code'];

// Font ttf free
$font = 'images/Prociono-Regular.otf';

$x = 0;
for ($i = 0; $i < strlen($text); $i++) {
	// Color letter
	$color_letter = imagecolorallocatealpha(
		$image_back, 
		(rand() % 155) + 100, 
		(rand() % 155) + 100, 
		(rand() % 155) + 100, 
		(rand() % 30) + 30
	);
	
	$rand_angle = (int) (rand() % -40) - 20;
	imagettftext($image_back, (rand() % 15) + 25, $rand_angle, 7 + $x, 36, $color_letter, $font, $text{$i});
	$x += 22;
}
unset($x);
 
// Show image
imagepng($image_back);
 
// Clear memory
imagedestroy($image_back);
 
?>