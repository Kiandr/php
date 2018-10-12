<div id="listings-map-<?=$idx->getLink() ?>" class="hidden listings-map"></div>
<div id="map-tooltip-block"></div>
<?php


// Display search results bar
echo '<div id="search-toolbar">';
echo '<div class="container container--toolbar">';

// Search results message
echo '<div class="bar -pad-top -text-center@md -text-center@sm -clear">';
if(!empty($search['title'])) {
    echo '<h1 class="bar__title -text-sm -left@lg -left@xl -text-center@md -text-center@sm">' . $search['title'] . '</h1>';
} else {
    echo '<h1 class="bar__title -text-sm -left@lg -left@xl -text-center@md -text-center@sm">' . $page_title . '</h1>';
}

echo '<div class="buttons -pad-vertical-xs -text-bold -mar-bottom-xs -right@lg -right@xl">';

// Display search tools
include $page->locateTemplate('idx', 'misc', 'search-controls');

// Toggle search results map
if (!empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING'])) {
    echo sprintf('<a rel="nofollow" href="#%s" class="button button--ghost button--sm -text-xs view-map -inline map--link">', $idx->getLink());
    echo '<svg class="icon--map button__icon icon icon--xs">';
    echo '<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/inc/skins/ce/img/assets.svg#icon--map"></use>';
    echo '</svg> <span class="button__label">Show Map</span></a>';
}

echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';


// Exclude from IDX snippet pages
if (empty($_REQUEST['snippet'])) {
    echo '<div class="container">';

    // Include search message
    include $page->locateTemplate('idx', 'misc', 'search-message');

    // Display compliance search result limit notice
    if (!empty($_COMPLIANCE['limit']) && $search_results_count['total'] > $_COMPLIANCE['limit']) {
        echo sprintf(
            '<div class="notice"><div class="notice__message">Only %s properties may be displayed per search. To see all of your results, try narrowing your search criteria.</div></div>',
            Format::number($_COMPLIANCE['limit'])
        );
    }
    echo '</div>';
}



if (empty($_REQUEST['snippet'])) {
    echo '<div class="container">';
}

    if (!empty($search_results_count['total'])) {
        echo sprintf('<div class="-text-center@md -text-center@sm -mar-bottom -text-xs">%s %s Found. Page %s of %s.</div>',
            Format::number($search_results_count['total']),
            Format::plural($search_results_count['total'], 'Properties', 'Property'),
            Format::number($pagination['page']),
            Format::number($pagination['pages'])
        );
    }

// No results found
if (empty($results)) {
    echo '<div class="notice -mar-top -mar-top-0@lg"><div class="notice__message">';
    echo 'No listings were found matching your search criteria.';
    echo '</div></div>';

} else {

    // Display search results
    echo '<div class="listings ' . ($view == 'detailed' ? 'columns' : 'columns') . '">';
    foreach ($results as $index => $result) {
        include $result_tpl;
    }
    echo '</div>';

    // Include Pagination
    if (empty($idxSnippetAlreadyIncluded)) {
        include $page->locateTemplate('idx', 'misc', 'pagination');
    }

    // Show MLS compliance disclaimer
    if (!empty($_COMPLIANCE['results']['show_immediately_below_listings'])) {
        \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer();
    }

}

if (empty($_REQUEST['snippet'])) {
    echo '</div>';
}

// Include required javascript code for this page
$this->addJavascript('js/idx/search.js', 'page')
    ->addJavascript(
        sprintf('mapOptions["%s"] = $.extend(true, mapOptions["%s"] || {}, %s);',
            $idx->getLink(),
            $idx->getLink(),
            json_encode([
                'manager' => [
                    'icon' => $this->getSkin()->getUrl() . '/img/map-flag.png',
                    'iconWidth' => 22,
                    'iconHeight' => 25
                ],
                'tooltip' => [
                    'parentEl' => '#map-tooltip-block'
                ]
            ])
    ), 'dynamic', false);