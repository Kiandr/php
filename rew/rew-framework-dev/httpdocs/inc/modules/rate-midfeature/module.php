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

    // IDX quicksearch
    case 'quicksearch':
        $moduleName = 'idx-search';
        break;

    // Guaranteed Sale Form
    case 'guaranteed-sold':
        $moduleName = 'rate-midfeature-form';
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
