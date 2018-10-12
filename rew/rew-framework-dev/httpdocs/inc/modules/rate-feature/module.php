<?php

// Skin instance
$container = $this->getContainer();
$page = $container->getPage();
$skin = $page->getSkin();

// Module Mode
$module = $this->config('module');
$moduleName = false;
switch ($module) {
    // No Feature
    case 'none':
        break;

    // RATE CTA feature
    case 'cta':
        $moduleName = 'rate-feature-cta';
        break;

    // Video feature
    case 'video':
        $moduleName = 'rate-feature-video';
        break;

    // Radio spot feature
    case 'radio':
        $moduleName = 'rate-feature-radio';
        break;
    
    // Search feature
    case 'search':
        $moduleName = 'rate-feature-search';
        break;
}

// Load module instance
if (!empty($moduleName)) {
    $module = $page->container('snippet')->module($moduleName, $this->config($moduleName));
}

// Display module HTML
if ($module instanceof Module) {
    $module->display();
}
