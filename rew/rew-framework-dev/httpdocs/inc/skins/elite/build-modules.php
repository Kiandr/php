<?php

use REW\Core\Interfaces\PageInterface;
use REW\Core\Interfaces\SkinInterface;

define('BUILDING_ASSETS', true);
define('SKIP_CUSTOM_LOADERS', true);

// Set HTTP Host & Document Root
$_SERVER['HTTP_HOST'] = basename($_SERVER['HOME']);
$rootDir = realpath(__DIR__ . '/../../../..');

$_SERVER['DOCUMENT_ROOT'] = realpath($rootDir . '/httpdocs');
$_SERVER['REQUEST_URI'] = '/';

// Require Composer Vendor Auto loader
require $rootDir . '/boot/app.php';

if (!Http_Host::isDev()) {
    $_SERVER['HTTP_HOST'] = 'www.' . $_SERVER['HTTP_HOST'];
    // Reset class caches
    Http_Host::isDev(true);
}

// Require dependencies

// Destroy session
@session_destroy();

// Load skin
$page = $app->get(PageInterface::class);

// Set variables
$skin = $app->get(SkinInterface::class);
$skin->setPage($page);

// Compile CSS breakpoints and paths
$build = function ($filename, $contents) {
    echo 'Building ' . $filename . '...';

    $autogen_header = '// THIS FILE IS AUTO-GENERATED. DO NOT UPDATE IT MANUALLY!' . PHP_EOL . PHP_EOL;

    $tmp_filename = $filename . '.tmp';
    $fp = fopen($tmp_filename, 'w');
    fwrite($fp, $autogen_header);
    fwrite($fp, $contents);
    fclose($fp);

    if (exec('diff ' . escapeshellarg($filename) . ' ' . escapeshellarg($tmp_filename) . ' 2>&1')) {
        @unlink($filename);
        rename($tmp_filename, $filename);
        echo ' Done.' . PHP_EOL;
    } else {
        unlink($tmp_filename);
        echo ' No changes.' . PHP_EOL;
    }
};

$skin_dir = __DIR__ . '/';

$autogen_scss_file = $skin_dir . 'scss/inc/_auto_vars.scss';
$source = $skin_dir . 'config/breakpoints.json';
$breakpoints = json_decode(file_get_contents($source), true);
$generated = '';
$generated .= '// THE FOLLOWING CODE ORIGINATES FROM ' . $source . PHP_EOL;
foreach ($breakpoints as $name => $pixels) {
    $generated .= '$' . $name . ': ' . $pixels . 'px !default;' . PHP_EOL;
}
$generated .= PHP_EOL . '// THE FOLLOWING CODE ORIGINATES FROM ' . realpath($rootDir . '/httpdocs/inc/classes/Settings.php') . PHP_EOL;
$generated .= '$skin-url: "' . Format::htmlspecialchars(str_replace($_SERVER['DOCUMENT_ROOT'], '', $skin->getUrl())) . '/";' . PHP_EOL;
$generated .= '$scheme-url: "' . Format::htmlspecialchars(str_replace($_SERVER['DOCUMENT_ROOT'], '', $skin->getSchemeUrl())) . '/";' . PHP_EOL;
$build($autogen_scss_file, $generated);
$autogen_scss_file = $skin_dir . 'scss/inc/_scheme_config.scss';
$generated = '';
if (file_exists($skin_dir . '/schemes/' . Settings::getInstance()->SKIN_SCHEME . '/config.scss')) {
    $generated = '@import "../../schemes/' . Format::htmlspecialchars(Settings::getInstance()->SKIN_SCHEME) . '/config.scss";' . PHP_EOL;
}
$build($autogen_scss_file, $generated);

$autogen_scss_file = $skin_dir . 'scss/inc/_scheme_loader.scss';
$generated = '';
if (file_exists($skin_dir . '/schemes/' . Settings::getInstance()->SKIN_SCHEME . '/scheme.scss')) {
    $generated = '@import "../../schemes/' . Format::htmlspecialchars(Settings::getInstance()->SKIN_SCHEME) . '/scheme.scss";' . PHP_EOL;
}
$build($autogen_scss_file, $generated);
