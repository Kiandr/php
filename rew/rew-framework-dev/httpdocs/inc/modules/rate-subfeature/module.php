<?php

// Skin instance
$container = $this->getContainer();
$page = $container->getPage();
$skin = $page->getSkin();

// Module Class List
$moduleClass = array('module');

// Module Mode
$module = $this->config('module');
$moduleName = false;
switch ($module) {
    // No Feature
    case 'none':
        break;

    // Featured Communities
    case 'communities':
        $moduleClass[] = 'featured-communities';
        $moduleName = 'communities';
        break;

    // Featured Listings
    case 'listings':
        $moduleClass[] = 'featured-listings';
        $moduleName = 'idx-listings';
        break;

    // Video feature
    case 'video':
        $moduleClass[] = 'featured-videos';
        $moduleName = 'rate-subfeature-videos';
        break;

    // Radio spot feature
    case 'radio':
        $moduleClass[] = 'featured-radio';
        $moduleName = 'rate-subfeature-radio';
        break;
}

// Load module instance
if (!empty($moduleName)) {
    $module = $page->container('snippet')->module($moduleName, $this->config($moduleName));
}

// Display module HTML
if ($module instanceof Module) {
    echo '<div id="feature-deck" class="' . implode(' ', $moduleClass) . '">';
    echo '<div class="wrap">';
    $module->display();
    echo '</div>';
    echo '</div>';
}
