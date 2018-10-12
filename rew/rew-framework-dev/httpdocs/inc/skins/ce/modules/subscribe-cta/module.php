<?php

use \REW\Core\Interfaces\SettingsInterface;
use \REW\Core\Interfaces\User\SessionInterface;

// Getdependencies from container
$di = \Container::getInstance();
$user = $di->get(SessionInterface::class);
$settings = $di->get(SettingsInterface::class);

// Logged in user
if ($user->isValid()) {
    // Is user already opt in to marketing
    $isOptIn = $user->info('opt_marketing') === 'in';
// New user
} else {
    $isOptIn = false;
}

$form = $settings->SETTINGS['URL_IDX_NEWSLETTER'];

// Allow user to goe back to what they were doing.
$user->setBackUrl($_SERVER['REQUEST_URI']);
