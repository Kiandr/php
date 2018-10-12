<?php

// Require Composer Vendor Auto loader
require_once __DIR__ . '/../../boot/app.php';

// Make sure we can access Realtytrac
if (empty(\Settings::getInstance()->MODULES['REW_RT']) || !class_exists('RealtyTrac\\App')) {
    header('Location: /');
    exit;
}

// Start a new app.
$app = new RealtyTrac\App('framework');

// Run the app.
$app->run();
