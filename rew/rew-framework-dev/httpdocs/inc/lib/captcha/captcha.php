<?php

// Start Session
session_start();

// Generate Captcha Secret
$secret = '';
$symbols = array('2', '3', '4', '5', '6', '7', '8', '9', 'A', 'C', 'E', 'G', 'H', 'K', 'M', 'N', 'P', 'R', 'S', 'U', 'V', 'W', 'Z', 'Y', 'Z');
for ($i = 0; $i <= 4; $i++) {
	$secret .= $symbols[mt_rand(0, 24)];
}
$secret = strtoupper($secret);

// Store in Session
$_SESSION['captcha'] = md5($secret);

// Let's create an image
$captcha_image = imagecreate(200, 40);

// Random background and color scheme. Can be red, green or blue
$captcha_backgrounds = array('FF0000', '00FF00', '0000FF');
$captcha_color_scheme = $captcha_backgrounds[mt_rand(0, 2)];
$captcha_colors = array(
	hexdec('0x' . $captcha_color_scheme{0} . $captcha_color_scheme{1}),
	hexdec('0x' . $captcha_color_scheme{2} . $captcha_color_scheme{3}),
	hexdec('0x' . $captcha_color_scheme{4} . $captcha_color_scheme{5})
);
$captcha_image_bgcolor = imagecolorallocate($captcha_image, $captcha_colors[0], $captcha_colors[1], $captcha_colors[2]);

// Let's make some lighter and darker colors
if ($captcha_color_scheme == 'FF0000') {
	$captcha_image_lcolor[] = imagecolorallocate($captcha_image, $captcha_colors[0], $captcha_colors[1] + mt_rand(230, 240), $captcha_colors[2] + mt_rand(230, 240));
	$captcha_image_lcolor[] = imagecolorallocate($captcha_image, $captcha_colors[0], $captcha_colors[1] + mt_rand(230, 240), $captcha_colors[2] + mt_rand(230, 240));
	$captcha_image_lcolor[] = imagecolorallocate($captcha_image, $captcha_colors[0], $captcha_colors[1] + mt_rand(160, 220), $captcha_colors[2] + mt_rand(160, 220));
	$captcha_image_dcolor[] = imagecolorallocate($captcha_image, $captcha_colors[0] - mt_rand(50, 100), $captcha_colors[1]+mt_rand(0, 50), $captcha_colors[2]+mt_rand(0, 50));
	$captcha_image_dcolor[] = imagecolorallocate($captcha_image, $captcha_colors[0] - mt_rand(50, 100), $captcha_colors[1]+mt_rand(0, 50), $captcha_colors[2]+mt_rand(0, 50));
	$captcha_image_dcolor[] = imagecolorallocate($captcha_image, $captcha_colors[0] - mt_rand(50, 100), $captcha_colors[1]+mt_rand(0, 50), $captcha_colors[2]+mt_rand(0, 50));
} elseif ($captcha_color_scheme == '00FF00') {
	$captcha_image_lcolor[] = imagecolorallocate($captcha_image, $captcha_colors[0] + mt_rand(230, 240), $captcha_colors[1], $captcha_colors[2] + mt_rand(230, 240));
	$captcha_image_lcolor[] = imagecolorallocate($captcha_image, $captcha_colors[0] + mt_rand(230, 240), $captcha_colors[1], $captcha_colors[2] + mt_rand(230, 240));
	$captcha_image_lcolor[] = imagecolorallocate($captcha_image, $captcha_colors[0] + mt_rand(150, 190), $captcha_colors[1], $captcha_colors[2] + mt_rand(150, 190));
	$captcha_image_dcolor[] = imagecolorallocate($captcha_image, $captcha_colors[0] + mt_rand(0, 130), $captcha_colors[1] - mt_rand(50, 100), $captcha_colors[2] + mt_rand(0, 130));
	$captcha_image_dcolor[] = imagecolorallocate($captcha_image, $captcha_colors[0] + mt_rand(0, 130), $captcha_colors[1] - mt_rand(50, 100), $captcha_colors[2] + mt_rand(0, 130));
	$captcha_image_dcolor[] = imagecolorallocate($captcha_image, $captcha_colors[0] + mt_rand(0, 130), $captcha_colors[1] - mt_rand(50, 100), $captcha_colors[2] + mt_rand(0, 130));
} else {
	$captcha_image_lcolor[] = imagecolorallocate($captcha_image, $captcha_colors[0] + mt_rand(210, 230), $captcha_colors[1] + mt_rand(210, 230), $captcha_colors[2]);
	$captcha_image_lcolor[] = imagecolorallocate($captcha_image, $captcha_colors[0] + mt_rand(210, 230), $captcha_colors[1] + mt_rand(210, 230), $captcha_colors[2]);
	$captcha_image_lcolor[] = imagecolorallocate($captcha_image, $captcha_colors[0] + mt_rand(180, 200), $captcha_colors[1] + mt_rand(180, 200), $captcha_colors[2]);
	$captcha_image_dcolor[] = imagecolorallocate($captcha_image, $captcha_colors[0] + mt_rand(0, 100), $captcha_colors[1] + mt_rand(0, 100), $captcha_colors[2] - mt_rand(70, 150));
	$captcha_image_dcolor[] = imagecolorallocate($captcha_image, $captcha_colors[0] + mt_rand(0, 100), $captcha_colors[1] + mt_rand(0, 100), $captcha_colors[2] - mt_rand(70, 150));
	$captcha_image_dcolor[] = imagecolorallocate($captcha_image, $captcha_colors[0] + mt_rand(0, 100), $captcha_colors[1] + mt_rand(0, 100), $captcha_colors[2] - mt_rand(70, 150));
}

// Background
for ($i = 0; $i <= 10; $i++) {
	imagefilledrectangle($captcha_image, $i * 20 + mt_rand(4, 26), mt_rand(0, 39), $i * 20 - mt_rand(4, 26), mt_rand(0, 39), $captcha_image_dcolor[mt_rand(0, 2)]);
}

// Let's place the word. Each letter will have random position, size, angle and font
for ($i = 0; $i <= 4; $i++) {
	imagettftext($captcha_image, mt_rand(24, 28), mt_rand(-20, 20), $i * mt_rand(30, 36) + mt_rand(2,4), mt_rand(32, 36), $captcha_image_lcolor[mt_rand(0, 1)], mt_rand(1, 4) . '.ttf', $secret{$i});
}

// Server Image
header('Content-type: image/png');
header('Expires: Sun, 1 Jan 2000 12:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . 'GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
imagepng($captcha_image);
imagedestroy($captcha_image);

// Exit
die;