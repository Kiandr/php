<?php

use REW\Core\Interfaces\HooksInterface;

if (empty($_SERVER['DOCUMENT_ROOT'])) {
    // Set docroot so our installation scripts can get correct paths from Settings
    $_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . '/../httpdocs/');
}

require __DIR__ . '/../vendor/autoload.php';

// Initialize app.
$app = new Container();
$appBkp = $app;
Container::setInstance($app);
$app->boot(
    require __DIR__ . '/../config/dirs.php',
    require __DIR__ . '/../config/bindings.php',
    require __DIR__ . '/../config/providers.php'
);

/**
 * Not Running From Command Line
 */
if (php_sapi_name() != 'cli') {
    // SSL redirect
    if (!isset($_SERVER['HTTPS']) && Settings::getInstance()->SSL === true) {
        $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $redirect);
        exit;
    }

    // Set Cookie Domain
    ini_set('session.cookie_domain', Http_Host::getCookieDomain());
}

/**
 * Initialize skin hooks
 */
if ($app->has(HooksInterface::class)) {
    $app->get(HooksInterface::class)->initHooks();
}

/**
 * Require custom loaders
 */
if (!defined('SKIP_CUSTOM_LOADERS')) {
    foreach (glob(__DIR__ . '/loaders/*.php') as $loader) {
        require $loader;
    }
}
