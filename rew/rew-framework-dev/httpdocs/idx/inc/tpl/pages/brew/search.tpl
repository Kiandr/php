<a id="homes-for-sale"></a>

<?php if (!empty($_REQUEST['snippet'])) { ?>
	<div id="listings-map" class="hidden"></div>
<?php } ?>

<?php global $idxSnippetAlreadyIncluded; ?>
<?php if (empty($_REQUEST['snippet'])) include $page->locateTemplate('idx', 'misc', 'results-message'); ?>

<?php if (empty($idxSnippetAlreadyIncluded)) { ?>

    <div class="msg vanilla results">
	    <?php if (!empty($results)) { ?>
	       <span class="summary"><em><?=number_format($search_results_count['total']); ?> Properties Found.</em> Showing Page <?=number_format($pagination['page']); ?> of <?=number_format($pagination['pages']); ?></span>
	    <?php } else { ?>
	       <span class="summary">No listings were found matching your search criteria.</span>
	    <?php } ?>
	    <span class="nav">
	        <?php if (!empty($pagination['prev'])) { ?>
	            <a href="<?=$pagination['prev']['url']; ?>" class="prev"><i class="icon-caret-left"></i> Prev</a>
	        <?php } ?>
	        <?php if (!empty($pagination['next'])) { ?>
	            <a href="<?=$pagination['next']['url']; ?>" class="next">Next <i class="icon-caret-right"></i></a>
	        <?php } ?>
	    </span>
    </div>

    <?php if (!empty($_COMPLIANCE['limit']) && $search_results_count['total'] > $_COMPLIANCE['limit']) { ?>
		<p class="message">Only <?=number_format($_COMPLIANCE['limit']); ?> properties may be displayed per search. To see all of your results, try narrowing your search criteria.</p>
    <?php } ?>

    <div class="toolbar">

    	<div class="tabset pills mini">
		    <ul class="tabset">
		        <li<?=(empty($view) || $view == 'grid' || $view == 'map') ? ' class="current"' : ''; ?>><a rel="nofollow" href="#grid" class="view"><i class="icon-th"></i></a>
		        <li<?=($view == 'detailed') ? ' class="current"' : ''; ?>><a rel="nofollow" href="#detailed" class="view"><i class="icon-th-list"></i></a>
		    </ul>
	    </div>

        <?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING']))  { ?>
        	<a rel="nofollow" href="#map" class="btn view<?=($view == 'map' || !empty($_REQUEST['map']['open'])) ? ' current' : ''; ?>"><i class="icon-map-marker"></i> Map</a>
        <?php } ?>

        <div class="sort">
		    <form action="<?=Settings::getInstance()->SETTINGS['URL_IDX']; ?>">
		        <strong>Sort by:</strong>
		        <select name="sort" onchange="window.location = this.value">
		            <option value="?<?=htmlspecialchars(http_build_query(array_merge($querystring_nosort, array('sortorder' => 'ASC-ListingPrice')))); ?>"<?=($_REQUEST['sortorder'] == 'ASC-ListingPrice' ? ' selected' : ''); ?>>Price, Low to High</option>
		            <option value="?<?=htmlspecialchars(http_build_query(array_merge($querystring_nosort, array('sortorder' => 'DESC-ListingPrice')))); ?>"<?=($_REQUEST['sortorder'] == 'DESC-ListingPrice' ? ' selected' : ''); ?>>Price, High to Low</option>
		        </select>
		    </form>
	    </div>

    </div>

<?php } else { ?>

    <div class="msg vanilla results">
    	<?php $count = !empty($results) ? count($results) : 0; ?>
	    <?php if (!empty($count) && $count >= $search_results_count['total']) { ?>
	       <span class="summary"><em><?=number_format($search_results_count['total']); ?> Properties Found.</em></span>
	    <?php } else if (!empty($count)) { ?>
	       <span class="summary"><em><?=number_format($search_results_count['total']); ?> Properties Found.</em> Showing First <?=number_format($count); ?> Results.</span>
	    <?php } else { ?>
	       <span class="summary">No listings were found matching your search criteria.</span>
	    <?php } ?>
    </div>

<?php } ?>

<div id="search_summary"></div>

<?php

// Search Results
if (!empty($results)) {
    echo '<div class="articleset listings ' . ($view == 'detailed' ? 'flowgrid_x1' : 'flowgrid') . '">';
	foreach ($results as $index => $result) {
		include $result_tpl;
	}
	echo '</div>';
}

// Include Pagination
if (empty($idxSnippetAlreadyIncluded)) include $page->locateTemplate('idx', 'misc', 'pagination');

if (!empty($_COMPLIANCE['results']['show_immediately_below_listings'])) {
	echo '<div class="show-immediately-below-listings">';
    \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer();
	echo '</div>';
}

// Start Javascript
ob_start();

?>
/* <script> */

// Search Form
var $form = $('form.idx-search');

// Search Toolbar
var $toolbar = $('.toolbar');

// Switch View
$toolbar.on(BREW.events.click, 'a.view', function () {
	var $this = $(this), view = $(this).attr('href');

	// Grid
	if (view == '#grid') {
		BREW.Cookie('results-view', state.view = 'grid');
		$this.parent().addClass('current').siblings().removeClass('current');
		$('#content .articleset.listings').removeClass('flowgrid_x1').addClass('flowgrid').eqHeight();
		 return true;

	// List
	} else if (view == '#detailed') {
		BREW.Cookie('results-view', state.view = 'detailed');
		$this.parent().addClass('current').siblings().removeClass('current');
		$('#content .articleset.listings').removeClass('flowgrid').addClass('flowgrid_x1').find('.listing').css('min-height', 0);
		return true;

	// Map
	} else if (view == '#map') {

		// Show Map
		if ($map.hasClass('hidden')) {

			// Save State
			BREW.Cookie('results-map', state.map = 1);

			// Scroll to Map
			$('html, body').animate({ scrollTop : $map.show().offset().top }, 500); $map.hide();

			// Show Map
			$map.REWMap('show', function () {

				// Show Map Search Tools
				$('#field-polygon').removeClass('hidden');
				$('#field-radius').removeClass('hidden');
				$('#field-bounds').removeClass('hidden');

			});

			//Add body class for map specific template adaptations
			$('body').addClass('map-displayed');

		} else {

			// Scroll to Map
			$('html, body').animate({ scrollTop : $map.offset().top }, 500);

			// Save State
			BREW.Cookie('results-map', state.map = 0);

			// Hide Map
			$map.REWMap('hide', function () {

				// Hide Map Search Tools
				$('#field-polygon').addClass('hidden');
				$('#field-radius').addClass('hidden');
				$('#field-bounds').addClass('hidden');

			});

			//Rmove body class
			$('body').removeClass('map-displayed');

		}

		return false;
	}
});

// Show Map
var show_map = <?=!empty($_REQUEST['map']['open']) ? 'true' : 'false'; ?>;

// Results State
var state = {
	view : window.location.hash.indexOf('#') != -1 ? window.location.hash.substr(window.location.hash.indexOf('#') + 1) : view,
	map : (show_map || BREW.Cookie('results-map') == 1) && BREW.Cookie('results-map') != 0 ? true : false
};

// Trigger View
if (state.view) $toolbar.find('a.view[href="#' + state.view + '"]').trigger(BREW.events.click);

// Show Map
if (!BREW.mobile && state.map && state.view != 'map') $toolbar.find('a.view[href="#map"]').trigger(BREW.events.click);

// Bind to Form Submit
$form.bind('submit', function (e) {

    // Save Map Details
    var center = $map.REWMap('getCenter');
    if (center) {
        $form.find('input[name="map[latitude]"]').val(center.lat());
    	$form.find('input[name="map[longitude]"]').val(center.lng());
    }

    // Zoom Level
    var zoom = $map.REWMap('getZoom');
    if (zoom) {
    	$form.find('input[name="map[zoom]"]').val(zoom);
    }

    // Map Bounds
    var bounds = $map.REWMap('getBounds');
	$form.find('input[name="map[ne]"]').val(bounds ? bounds.getNorthEast().toUrlValue() : '');
	$form.find('input[name="map[sw]"]').val(bounds ? bounds.getSouthWest().toUrlValue() : '');

    // Polygon Searches
    var polygons = $map.REWMap('getPolygons'), $polygons = $form.find('input[name="map[polygon]"]');
    if (typeof polygons !== 'undefined') $polygons.val(polygons ? polygons : '');

    // Radius Searches
    var radiuses = $map.REWMap('getRadiuses'), $radiuses = $form.find('input[name="map[radius]"]');
    if (typeof radiuses !== 'undefined') $radiuses.val(radiuses ? radiuses : '');

});

// Save This Search
$('#save-link').live(BREW.events.click, function () {

	// Search Data
	var search = $.extend(true, criteria, {
		view : state.view
	});

	// Clear Search Id
	delete search.saved_search_id;

	// Save Search
	saveSearch(search);

	// Return
	return false;

});

// Edit Search
$('#edit_search, #edit_search_email').live(BREW.events.click, function () {

    var email_results_immediately = $(this).attr('id') == 'edit_search_email' ? 'true' : 'false';
	// Search Data
	var search = $.extend(true, criteria, {
		view : state.view,
        email_results_immediately : email_results_immediately
	});

	// Edit Search
	editSearch(search);

	// Return
	return false;

});

/* </script> */
<?php

// Trigger Save Search
if (isset($_GET['auto_save'])) {
	echo '$(\'#save-link\').trigger(\'click\');' . PHP_EOL;
}

// Write Javascript
$page->writeJS(ob_get_clean());