<?php

// Dont Display Errors
ini_set('display_errors', false);
ini_set('display_startup_errors', false);

// Set Memory Limit
ini_set('memory_limit', '64M');

// Require phpThumb Library
require_once 'phpthumb.class.php';

// Require Settings Class
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../boot/app.php');

// basic error handling
function error ($error) {
	header("HTTP/1.0 404 Not Found");
	echo '<h1>Not Found</h1>';
	echo '<p>The image you requested could not be found.</p>';
	echo "<p>An error was triggered: <b>$error</b></p>";
	exit;
}

// Recursive dir function
function mkpath ($path, $mode) {
    is_dir(dirname($path)) || mkpath(dirname($path), $mode);
    return is_dir($path) || @mkdir($path, 0777, $mode);
}

// Parse Request URI
preg_match('#/thumbs/([0-9]{1,4})?x([0-9]{1,4})?/(f/|r/)?(q[0-9]{1,3}/)?(ssl/)?(.*)#', $_SERVER['REQUEST_URI'], $matches);
if (empty($matches)) {
	error('Invalid URL');
}

// Image Details
$width	= $matches['1'];
$height	= $matches['2'];
if (!empty($matches['3'])) {
	$force = ($matches['3'] === 'f/');
	$nozc = ($matches['3'] === 'r/');
}

if (!empty($matches['4'])) {
    $quality = (int) $matches['4'];
    if ($quality > 100 || $quality < 0) {
        error('Quality Setting Is Out Of Bounds');
    }
}

$ssl	= !empty($matches['5']);
$source	= urldecode($matches['6']);

// Require Source
if (empty($source)) {
	error('No Source');
}

$max_height = (isset(Settings::getInstance()->SETTINGS['img_max_height']) ? Settings::getInstance()->SETTINGS['img_max_height'] : 2000);
$max_width  = (isset(Settings::getInstance()->SETTINGS['img_max_width']) ? Settings::getInstance()->SETTINGS['img_max_width'] : 2000);

// Require Width or Height
if (empty($height) && empty($width)) {
	error('No Dimensions');
} else if ($height > $max_height && $width > $max_width) {
    error('Dimensions are too large.  Try something smaller');
}

// Image Path
$image  = $_SERVER['DOCUMENT_ROOT'] . '/' . $source;

// Is External
$external = false;

// Is IDX Photo
$idx_photo = false;

// IDX Listing Photo
if (false !== strpos($source, '.rewhosting.com')) {
	$idx_photo = true;
	$external = true;
	$image = 'http://' . $source;
} else {

	// Whitelist URLS (Hotlinked IDX Feed Photos)
	$whitelist = array(
		str_replace('www.', '', $_SERVER['HTTP_HOST']),
		$_SERVER['HTTP_HOST'],
		'rackcdn.com',
		'tp.usamls.net',
		'alaskarealestate.com',
		'photos.flexmls.com',
		'rapmls.com',
		'mris.com',
		'carolinaphotos.com',
		'glarmls.com',
		'retsiq.harmls.com',
		'p.iresis.com',
		'gopelanetworks2.com',
		'extimages2.living.net',
		'mlsmedia.metrolistmls.com',
		'mredllc.com',
		'photos.nefmls.com',
		'ntreispictures.marketlinx.com',
		'attach.realcomponline.com',
		'pictures.realtracs.net',
		'tempo5.sandicor.com',
		'media.mlspin.com',
		'panamacityhomesonline.net',
		'photos.iresis.com',
		'iwebagentonline.com',
		'media.neohrex.com',
        'rew-feed-images.global.ssl.fastly.net',
	);
	foreach ($whitelist as $host) {
	    if (false !== strpos($source, $host)) {
			$idx_photo = true;
	        $external = true;
			$file = substr($source, strrpos($source, '/') + 1);
			if (strpos($file, '?')) $file = str_replace(substr($file, strpos($file, '?')), '', $file);
			$image = (!empty($ssl) ? 'https' : 'http') . '://' . str_replace($file, rawurlencode($file), $source);
	        break;
	    }
	}

}

// Remove query string from image
if (empty($external) && strpos($source, '?')) {
	$source = str_replace(substr($source, strpos($source, '?')), '', $source);
	$image = str_replace(substr($image, strpos($image, '?')), '', $image);
}

// Cache Output
$thumb_output = '/inc/cache/img/' . $width . 'x' . $height . '/';
$thumb_output .= (!empty($force) ? 'f/' : '');
$thumb_output .= (!empty($nozc) ? 'r/' : ''); // no zoom crop, aka regular mode
$thumb_output .= $source;
$thumb_output_abs = $_SERVER['DOCUMENT_ROOT'] . $thumb_output;

// Thumbnail Already Exists
if (file_exists($thumb_output_abs)) {
	$info = getimagesize($thumb_output_abs);
	if (!empty($info['mime'])) {

		// Last Mofidified
		$modified = filemtime($thumb_output_abs);

		// Send Content-Type Headers
		header('Content-Type: ' . $info['mime']);

		// Send Cache Headers
		$age = (365 * 24 * 60 * 60);
		header('Expires: ' . date('D, d M Y H:i:s', $modified + $age) . ' GMT');

		// Last Modified
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $modified) . ' GMT');

		// Check Cache
		if (array_key_exists('HTTP_IF_MODIFIED_SINCE', $_SERVER)) {
			$if_modified_since = strtotime(preg_replace('/;.*$/', '', $_SERVER['HTTP_IF_MODIFIED_SINCE']));
			if ($if_modified_since >= $modified) {
				header('HTTP/1.0 304 Not Modified');
				exit;
			}
		}

		// Output File
		readfile($thumb_output_abs);

		// Kill Script
		exit;

	}
}

// Require Local File
if (empty($external) && !file_exists($image)) {
	$image = strtok($image, '?');
	if (!empty($image) && !file_exists($image)) {
		error('No Source Image');
	}
}

// Create Thumbnail
$phpThumb = new phpThumb();
$phpThumb->setSourceFilename($image);

// Setting to x will use the exif_read_data function to check the exif orientation only 1,3,6,8 are supported
$phpThumb->setParameter('ar', 'x');

if ($external === true) {
	// Source passed our whitelist check
	$phpThumb->setParameter('config_nohotlink_enabled', false);
}
if (!empty($force)) {
	$phpThumb->setParameter('fltr', 'crop');
	$phpThumb->setParameter('far', 'C');
	$phpThumb->setParameter('zc', true);
	$phpThumb->setParameter('aoe', true);
} elseif (!empty($nozc)) {
	$phpThumb->setParameter('zc', false);
} else {
	$phpThumb->setParameter('zc', true);
}

if (empty($quality)) {
    $quality = Settings::getInstance()->SETTINGS['img_quality'];
    $phpThumb->setParameter('q', (!empty($quality) ? $quality : '75'));
}

// Set Dimensions
if (!empty($width))  $phpThumb->setParameter('w', $width);
if (!empty($height)) $phpThumb->setParameter('h', $height);

// Set Output Format
if (substr($thumb_output, -3, 3) == 'png') {
	$phpThumb->setParameter('f', 'png');
} else if (substr($thumb_output, -3, 3) == 'gif') {
	$phpThumb->setParameter('f', 'gif');
} else {
	$phpThumb->setParameter('f', 'jpeg');
}

// Generate Thumbnail
if (!$phpThumb->GenerateThumbnail()) {
	if (!empty($idx_photo)) {
		$phpThumb->setSourceFilename('/img/no-image.jpg');
		$phpThumb->setParameter('f', 'jpg');
		if (!$phpThumb->GenerateThumbnail()) {
			error('cannot generate thumbnail');
		}
	} else {
		error('cannot generate thumbnail');
	}
}

// make the directory to put the image
if (!mkpath(dirname($thumb_output_abs), true)) {
	error('cannot create directory');
}

// Cache Thumbnail
if (!$phpThumb->RenderToFile($thumb_output_abs)) {
	error('cannot save thumbnail');
}

// Send Content-Type Headers
if (substr($thumb_output_abs, -3, 3) == 'png') {
	header('Content-Type: image/png', true);
} else {
	header('Content-Type: image/jpeg', true);
}

// Output File
readfile($thumb_output_abs);

// Kill Script
exit;

?>
