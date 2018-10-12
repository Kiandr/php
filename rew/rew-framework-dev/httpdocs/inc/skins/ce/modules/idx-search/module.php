<?php

use REW\Core\Interfaces\SettingsInterface;

// Build Module Dependencies
$container = Container::getInstance();
$settings = $container->get(SettingsInterface::class);

/** @var array $panels */

// Load default "module.php"
$controller = $this->locateFile('module.php', __FILE__);
if (!empty($controller)) {
    require_once $controller;
}

// Process panels
if (!empty($panels)) {
    // Disable toggling of search panels
    foreach ($panels as $id => $panel) {
        $panel->setToggle(false);
    }

    // Remove standard panels used in basic search bar
    $basicPanels = ['drive_time', 'location', 'price', 'rooms', 'bedrooms', 'bathrooms'];
    foreach ($basicPanels as $basicPanel) {
        unset($panels[$basicPanel]);
    }

    // Remove map panels from search form
    unset($panels['polygon'], $panels['radius'], $panels['bounds']);
}

// Link to advanced search
$linkAdvanced = empty($this->config('advanced'));

// Show advanced options
$showAdvanced = !$linkAdvanced && isset($_GET['advanced']) && empty($_GET['refine']);

// Search search tags
$idxTags = $this->config('hideTags') ? [] : IDX_Panel::tags();

// Remove 'search_location' tag from being included
$idxTags = array_filter($idxTags, function (IDX_Search_Tag $tag) {
    $idxField = $tag->getField();
    return !isset($idxField['search_location']);
});

// Search result sort options
$sortOptions = IDX_Builder::getSortOptions();
$sortOrder = current($sortOptions);
foreach ($sortOptions as $sortOption) {
    if ($_REQUEST['sortorder'] === $sortOption['value']) {
        $sortOrder = $sortOption;
    }
}

// Query string parameters
$queryStringParameters = [];
if (strpos($_SERVER['REQUEST_URI'], '?') !== false) {
    parse_str(substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?') + 1), $queryStringParameters);
    unset($queryStringParameters['search_title']);
    unset($queryStringParameters['sortorder']);
    unset($queryStringParameters['sort']);
}
