<a id="homes-for-sale"></a>
<?php

// Is this the 2nd snippet on a page?
global $idxSnippetAlreadyIncluded;

// Check modules
$conflictingModuleLoaded = falsee;
$conflictingModules = ['cms-listings', 'idx-featured-search', 'agents'];
foreach ($conflictingModules as $conflictingModule) {
    $conflictingModuleLoaded = $page->moduleLoaded($conflictingModule);
    if ($conflictingModuleLoaded) break;
}

// This is an IDX snippet
if (empty($idxSnippetAlreadyIncluded)) {

    // Refine search tools
    if (empty($conflictingModuleLoaded)) {
        $this->container('idx-snippet')->module('idx-search', array(
            'showFeeds' => empty($_REQUEST['snippet']),
            'advanced' => true,
            'button' => 'Search'
        ))->display(true);
    }

    // Map container
    echo '<div id="listings-map-' . $idx->getLink() .'" class="hidden listings-map"></div>';
    echo '<div id="map-tooltip-block"></div>';

}

// Display search results bar
if (empty($idxSnippetAlreadyIncluded)) {
        echo '<div id="search-toolbar" class="bar idx-sort-bar marB-sm">';

    // Search results message
    $page_limit = $_REQUEST['page_limit'] ?: $page_limit;
    if (!empty($search_results_count['total'])) {
        $page_start = ($pagination['page'] * $page_limit) - ($page_limit - 1);
        $page_end = ($pagination['page'] * $page_limit);
        if ($page_end > $search_results_count['total']) {
            $page_end = $search_results_count['total'];
        }
        echo sprintf('<div class="ttl">%s &ndash; %s of %s Listings</div>',
            Format::number($page_start),
            Format::number($page_end),
            Format::number($search_results_count['total'])
        );
    }

    // Sort listing options
    $sortorder = array_filter([
        'ASC-ListingPrice' => 'Price, Low to High',
        'DESC-ListingPrice' => 'Price, High to Low',
        'ASC-ListingDOM' => IDX_Panel::checkField('ListingDOM') ? 'New Listings First' : false
    ]);

    // Display result sorting options
    if (!empty($_REQUEST['sortorder'])) {
        $current = $sortorder[$_REQUEST['sortorder']];
        if (!empty($current)) {
            echo sprintf('<span class="L sort mini"><a class="mnu-item" data-menu="#sort-menu">%s</a></span>', $current);
        }
        // Sortorder menu
        echo '<div class="menu hidden" id="sort-menu"><ul>';
        foreach ($sortorder as $value => $title) {
            echo sprintf('<li><label><input type="radio" name="sort" onchange="window.location = \'%s\';"%s> %s</label></li>',
                '?' . Format::htmlspecialchars(http_build_query(array_merge($querystring_nosort, ['sortorder' => $value]))),
                ($_REQUEST['sortorder'] == $value ? ' checked' : ''),
                $title
            );
        }
        echo '</ul></div>';
    }

    echo '<div class="R right-idx-bar">';

    //View toggle removes office and disclaimer when compliance rule met
    $id = 'id="%s"';
    $grid_id = "";
	$detailed_id = "";

	if($_COMPLIANCE['hide_office_grid']) {
    	$grid_id = sprintf($id, "gridViewOffice");
    	$detailed_id = sprintf($id, "detailedViewOffice");

    } elseif ($_COMPLIANCE['show_list_view']) {
        $grid_id = sprintf($id, "gridView");
        $detailed_id = sprintf($id, "detailedView");
    }

	// Search result views
    echo sprintf('<a '.$grid_id.' rel="nofollow" href="#grid" class="view mnu-item%s">Grid</a>', $view == 'grid' ? ' mnu-item--cur' : '');
    echo sprintf('<a '.$detailed_id.' rel="nofollow" href="#detailed" class="view mnu-item%s">List</a>', $view == 'detailed' ? ' mnu-item--cur' : '');

    // Toggle search results map
    if (!empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING'])) {
        echo '<a rel="nofollow" href="#map" class="view-map mnu-item">Map</a>';
    }

    // Search result controls
    include $page->locateTemplate('idx', 'misc', 'search-controls');
    echo '</div>';
    echo '</div>';

}

// Exclude from IDX snippet pages
if (empty($_REQUEST['snippet'])) {

    // Include search message
    include $page->locateTemplate('idx', 'misc', 'search-message');

    // Display compliance search result limit notice
    if (!empty($_COMPLIANCE['limit']) && $search_results_count['total'] > $_COMPLIANCE['limit']) {
        echo sprintf(
            '<p class="msg marV-sm">Only %s properties may be displayed per search. To see all of your results, try narrowing your search criteria.</p>',
            Format::number($_COMPLIANCE['limit'])
        );
    }

}

// No results found
if (empty($results)) {
    echo '<div class="msg"><p>No listings were found matching your search criteria.</p></div>';

} else {

    // Display search results
    echo '<div class="listings ' . ($view == 'detailed' ? 'cols' : 'cols') . '">';
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

// Include required javascript code for this page
$this->addJavascript('js/idx/search.js', 'page')
    ->addJavascript('var mapOptions = $.extend(true, mapOptions || {}, ' . json_encode([
    'manager' => [
        'icon' => $this->getSkin()->getUrl() . '/img/map-flag.png',
        'iconWidth' => 22,
        'iconHeight' => 25
    ],
    'tooltip' => [
        'parentEl' => '#map-tooltip-block'
    ]
]) . ');', 'dynamic', false);
