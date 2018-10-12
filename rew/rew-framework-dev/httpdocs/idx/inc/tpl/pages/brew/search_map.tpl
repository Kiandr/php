<?php

// Results Message TPL
include $page->locateTemplate('idx', 'misc', 'results-message');

?>

<div class="msg vanilla results">
	<span class="summary" id="idx-map-message"></span>
</div>

<?php if (!empty($_COMPLIANCE['limit'])) { ?>
	<p class="message hidden" id="compliance-message">Only <?=number_format($_COMPLIANCE['limit']); ?> properties may be displayed per search. To see all of your results, try narrowing your search criteria.</p>
<?php } ?>

<div class="toolbar">
	<div class="sort">
		<strong>Sort by:</strong>
		<select name="sortorder">
			<option value="ASC-ListingPrice"<?=($_REQUEST['sortorder'] == 'ASC-ListingPrice' ? ' selected' : ''); ?>>Price, Low to High</option>
			<option value="DESC-ListingPrice"<?=($_REQUEST['sortorder'] == 'DESC-ListingPrice' ? ' selected' : ''); ?>>Price, High to Low</option>
		</select>
	</div>
</div>

<div id="idx-map-search-wrap">

	<div id="idx-map-search"></div>

	<div id="idx-map-legend" class="closed">
		<div class="legend-tabs">
			<div class="legend_tab legendTrigger"></div>
			<div class="stats_tab statsTrigger"></div>
		</div>
		<div class="legend_content">
			<div class="legend_contents hidden">

				<?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_ONBOARD'])) { ?>
					<h4>Show Nearby</h4>
					<div class="field x12 toggleset">
						<label><input type="checkbox" name="map[layers][]" value="schools"> <img src="/img/map/legend-school@2x.png" width="20" height="20" alt=""> Schools</label>
						<label><input type="checkbox" name="map[layers][]" value="hospitals"> <img src="/img/map/legend-hospital@2x.png" width="20" height="20" alt=""> Hospitals</label>
						<label><input type="checkbox" name="map[layers][]" value="airports"> <img src="/img/map/legend-airport@2x.png" width="20" height="20" alt=""> Airports</label>
						<label><input type="checkbox" name="map[layers][]" value="parks"> <img src="/img/map/legend-park@2x.png" width="20" height="20" alt=""> Parks</label>
						<label><input type="checkbox" name="map[layers][]" value="golf-courses"> <img src="/img/map/legend-golf@2x.png" width="20" height="20" alt=""> Golf Courses</label>
						<label><input type="checkbox" name="map[layers][]" value="churches"> <img src="/img/map/legend-church@2x.png" width="20" height="20" alt=""> Churches</label>
						<label><input type="checkbox" name="map[layers][]" value="shopping"> <img src="/img/map/legend-shopping@2x.png" width="20" height="20" alt=""> Shopping</label>
					</div>
				<?php } ?>

				<h4>Map View</h4>
				<div class="field x12">
					<select name="map[type]">
						<option value="roadmap">Normal</option>
						<option value="satellite">Satellite</option>
						<option value="hybrid">Hybrid</option>
						<option value="terrain">Terrain</option>
					</select>
				</div>

			</div>
			<div class="stats_contents hidden">
				<div class="keyvalset">

					<h4>Search Statistics</h4>

					<ul>
						<li class="keyval">
							<strong>Listings</strong>
							<strong id="stats-total">0</strong>
						</li>
					</ul>

					<h5>Listing Price</h5>

					<ul>
						<li class="keyval">
							<strong>Average</strong>
							<span id="stats-price-avg">0</span>
						</li>
						<li class="keyval">
							<strong>Highest</strong>
							<span id="stats-price-high">$0</span>
						</li>
						<li class="keyval">
							<strong>Lowest</strong>
							<span id="stats-price-low">$0</span>
						</li>
					</ul>

					<h5>Property Size</h5>

					<ul>
						<li class="keyval">
							<strong>Average</strong>
							<span id="stats-sqft-avg">0 ft&sup2;</span>
						</li>
						<li class="keyval">
							<strong>Highest</strong>
							<span id="stats-sqft-high">0 ft&sup2;</span>
						</li>
						<li class="keyval">
							<strong>Lowest</strong>
							<span id="stats-sqft-low">0 ft&sup2;</span>
						</li>
					</ul>

				</div>
			</div>
		</div>
	</div>

</div>

<?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_ONBOARD'])) { ?>
	<p class="disclaimer">Disclaimer / Sources: <?=Locale::spell('Neighborhood');?> data provided by Onboard Informatics &copy; <?=date('Y'); ?></p>
<?php } ?>

<?php

// Start Javascript
ob_start();

?>
/* <script> */

// IDX Search Form
var $form = $('form.idx-search'), searches = 0;

// Map Search Sort Order
$('select[name="sortorder"]').on('change', function () {
	$form.find('input[name="sortorder"]').val(this.value);
	$form.trigger('submit');
	return true;
});

// Search in Bounds
var eventDragEnd, eventZoomEnd, $searchBounds = $form.find('input[name="map[bounds]"]'), forceBounds = false;
$searchBounds.on({
	'click' : function () {
		$(this).trigger('bounds');
	},
	'bounds' : function () {
		var $this = $(this), checked = $this.attr('checked') ? true : false, $tooltip = $this.closest('.details').find('small');
		if (checked) {

			// Show Tooltip
			$tooltip.removeClass('hidden');

			// Google Map
			var gmap = $map.REWMap('getMap');

			// Bind Map Event (dragend)
			eventDragEnd = google.maps.event.addListener(gmap, 'dragend', function() {
				$form.trigger('submit');
			});

			// Bind Map Event (zoomend)
			eventZoomEnd = google.maps.event.addListener(gmap, 'zoom_changed', function(oldLevel, newLevel) {
				$form.trigger('submit');
			});

			// Trigger Search Request
			$form.trigger('submit');

		} else {

			// Hide Tooltip
			$tooltip.addClass('hidden');

			// Unbind Map Event (dragend)
			if (eventDragEnd) google.maps.event.removeListener(eventDragEnd);

			// Unbind Map Event (zoomend)
			if (eventZoomEnd) google.maps.event.removeListener(eventZoomEnd);

		}

	}

});

// Map Legend
var $legend = $('#idx-map-legend');

// Toggle Map Legend
$legend.on('toggleLegend', function () {
	if ($legend.hasClass('closed')) {
		$legend.stop().animate({
			'right' : 0
		}, 500).removeClass('closed');
		BREW.Cookie('idx-map-legend', 'open');
	} else {
		$legend.stop().animate({
			'right' : $legend.find('.legend_tab').width() - $legend.width()
		}, 500).addClass('closed');
		BREW.Cookie('idx-map-legend', 'close');
	}
});

// Position Map Legend
$legend.show().css({
	'position' : 'absolute',
	'right' : $legend.find('.legend_tab').width() - $legend.width(),
	'top' : 75
});

// Show Options
$legend.on(BREW.events.click, '.legendTrigger', function() {
	var $this = $(this);
	$('.legend_contents').removeClass('hidden').siblings().addClass('hidden');
	if ($this.hasClass('current') || $legend.hasClass('closed')) $legend.trigger('toggleLegend');
	$this.addClass('current').siblings().removeClass('current');
});

// Show Stats
$legend.on(BREW.events.click, '.statsTrigger', function() {
	var $this = $(this);
	$('.stats_contents').removeClass('hidden').siblings().addClass('hidden');
	if ($this.hasClass('current') || $legend.hasClass('closed')) $legend.trigger('toggleLegend');
	$this.addClass('current').siblings().removeClass('current');
});

// Switch Map Type
$legend.on('change', 'select[name="map[type]"]', function () {
	var $this = $(this).blur(), type = $this.val();
	$map.REWMap('setType', type);
});

// Search Query
var search_query, $searchMessage = $('#idx-map-message'), $searchTitle = $('#save-prompt');

// Bind to Form Submit
$form.on('submit', function (e) {

	// Force Bounds on First Search (Reduces bouncing around of map when first loaded...)
	forceBounds = (searches === 0) ? true : false;

	// Increment
	searches++;

	// Prevent Default
	e.preventDefault();

	// Already Loading...
	if ($map.hasClass('loading')) return;

	// Clear Map
	$map.REWMap('clear');

	// Clear Map Layers
	if (layerManager) layerManager.clear();

	// Show Map Loader
	$map.addClass('loading').REWMap('showLoader');

	// Map Data
	var center = $map.REWMap('getCenter'),
		zoom = $map.REWMap('getZoom'),
		bounds = $map.REWMap('getBounds'),
		polygons = $map.REWMap('getPolygons'),
		radiuses = $map.REWMap('getRadiuses');

	// Map Criteria
	search_query = {
		// Map Data...
		'latitude'	: center.lat(),
		'longitude'	: center.lng(),
		'zoom'		: zoom,
		'ne'		: bounds.getNorthEast().toUrlValue(),
		'sw'		: bounds.getSouthWest().toUrlValue(),
		'polygon'	: (polygons ? polygons : ''),
		'radius'	: (radiuses ? radiuses : ''),
		// Form Fields...
		'sortorder' : $('select[name="sortorder"]').val(),
		'bounds'	: ($searchBounds.attr('checked') || forceBounds) ? 1 : 0
	};

	// Update Form Data
	$form.find('input[name="map[latitude]"]').val(search_query.latitude);
	$form.find('input[name="map[longitude]"]').val(search_query.longitude);
	$form.find('input[name="map[ne]"]').val(search_query.ne);
	$form.find('input[name="map[sw]"]').val(search_query.sw);
	$form.find('input[name="map[polygon]"]').val(search_query.polygon);
	$form.find('input[name="map[radius]"]').val(search_query.radius);
	$form.find('input[name="map[zoom]"]').val(search_query.zoom);
	$form.find('input[name="sortorder"]').val(search_query.sortorder);

	// Disable Locations for First Search
	if (searches === 1) $form.find('.location').prop('disabled', true);

	// Search Criteria
	search_query = $.extend(search_query, {
		criteria	  : $form.serialize(),
		feed		  : '<?=Settings::getInstance()->IDX_FEED; ?>',
		search_url	: '<?=parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); ?>?' + $form.serialize(),
		search_title  : $form.find('input[name="search_title"]').val(),
		lead_id	   : $form.find('input[name="lead_id"]').val(),
		create_search : ($form.find('input[name="create_search"]').length == 1 ? true : false),
		edit_search   : ($form.find('input[name="edit_search"]').length == 1 ? true : false),
	});

	// Reset Locations for First Search
	if (searches === 1) $form.find('.location').prop('disabled', false);

	// Save Title
	$searchTitle.html('"Loading Results..."');

	// Search Message
	$searchMessage.html('');

	// POST HTTP Request, Expect JSON
	$.ajax({
		'url'		: '/idx/inc/php/ajax/map.php?searchListings',
		'type'		: 'POST',
		'dataType'	: 'json',
		'data'		: search_query,
		'error'		: function () {

			// Hide Loader
			$map.removeClass('loading').REWMap('hideLoader');

		},
		'success'  : function (json) {

			// Load Map Layers
			$layers.filter(':checked').trigger(BREW.events.click).attr('checked', 'checked');

			// Hide Loader
			$map.removeClass('loading').REWMap('hideLoader');

			// Successful Query
			if (json.returnCode === 200) {

				// Statistics
				var stats = json.stats;
				if (stats) {

					// Update Statistics
					$('#stats-total').html(stats.total);
					$('#stats-price-avg').html(stats.price_avg);
					$('#stats-price-high').html(stats.price_high);
					$('#stats-price-low').html(stats.price_low);
					$('#stats-sqft-avg').html(stats.sqft_avg);
					$('#stats-sqft-high').html(stats.sqft_high);
					$('#stats-sqft-low').html(stats.sqft_low);

					// Show Statistics
					if (BREW.Cookie('idx-map-legend') != 'close') {
						$legend.find('.statsTrigger').removeClass('current').trigger(BREW.events.click);
					}

				}

				// Search Title
				if (json.search_title) $searchTitle.html('"' + json.search_title + '"');

				// Results Found
				var results = json.results, count = results && results.length;
				if (results && count > 0) {

					// Search Message
					if (json.limit > 0) {
						$searchMessage.html('<em>' + parseInt(json.count).format() + '</em> listings found, first <em>' + parseInt(count).format() + '</em> shown. Refine your search');
					} else {
						$searchMessage.html('Showing <em>' + parseInt(json.count).format() + '</em> Properties Found.');
					}

					// Fit Bounds If Not Searching Within Bounds
					var fitBounds = ($searchBounds.is(':checked') !== true && !forceBounds) ? true : false;
					$map.REWMap('getSelf').manager.opts.bounds = fitBounds;

					// Load Markers
					$map.REWMap('load', results);

				// No Results
				} else {
					$searchMessage.html('No results were found matching your search criteria.');

				}

				// Compliance Message
				if (complianceLimit > 0) {
					if (json.total > complianceLimit) {
						$('#compliance-message').removeClass('hidden');
					} else {
						$('#compliance-message').addClass('hidden');
					}
				}

			// Error Occurred
			} else {
				$searchMessage.html('An error has occurred. Please try your search again.');

			}

		}
	});

});

// Layer Icons
var layerIcons = [];
layerIcons['schools']		= ['/img/map/marker-school@2x.png',		'/img/map/cluster-school@2x.png'];
layerIcons['hospitals']		= ['/img/map/marker-hospital@2x.png',	'/img/map/cluster-hospital@2x.png'];
layerIcons['airports']		= ['/img/map/marker-airport@2x.png',	'/img/map/cluster-airport@2x.png'];
layerIcons['parks']			= ['/img/map/marker-park@2x.png',		'/img/map/cluster-park@2x.png'];
layerIcons['golf-courses']	= ['/img/map/marker-golf@2x.png',		'/img/map/cluster-golf@2x.png'];
layerIcons['churches']		= ['/img/map/marker-church@2x.png',		'/img/map/cluster-church@2x.png'];
layerIcons['shopping']		= ['/img/map/marker-shopping@2x.png',	'/img/map/cluster-shopping@2x.png'];

// Layers
var $layers = $legend.find('input[name="map[layers][]"]'), layers = [], layerManager = null;

// Toggle Layers
$layers.on(BREW.events.click, function () {

	// Map Layer Type
	var $this = $(this),
		type = $this.val(),
		checked = $this.attr('checked'),
		title = (type.charAt(0).toUpperCase() + type.slice(1)).replace('-', ' ');

	// Un-Check Other Layers
	$layers.not($this).removeAttr('checked');

	// Update Layer Manager
	if (layerManager) {
		layerManager.clear();
		layerManager.icon.url = layerIcons[type][0];
		layerManager.iconCluster.url = layerIcons[type][1];
		layerManager.opts.titleStacked = '{x} ' + title + ' Found at This Location.';
		layerManager.opts.titleCluster = '{x} ' + title + ' Found.';
		$map.REWMap('getTooltip').hide(true);

	// Setup Layer Manager
	} else {
		layerManager = new REWMap.MarkerManager({
			map				: $map.REWMap('getSelf'),
			icon			: layerIcons[type][0],
			iconWidth		: 20,
			iconHeight		: 25,
			iconCluster		: {
				url: layerIcons[type][1],
				labelOrigin: { x: 16, y: 11 },
				scaledSize: { width: 31, height: 25 }
			},
			iconClusterWidth	: 42,
			iconClusterHeight	: 25,
			bounds			: false,
			cluster			: true,
			titleStacked	: '{x} ' + title + ' Found at This Location.',
			titleCluster	: '{x} ' + title + ' Found.'
		});

	}


	// Show Map Layers
	if (checked) {

		// Keep Checked
		$this.attr('checked', 'checked');

		// Random Process ID
		pid = Math.random() * 5;

		// Map Data
		var center = $map.REWMap('getCenter'),
			bounds = $map.REWMap('getBounds'),
			polygons = $map.REWMap('getPolygons'),
			radiuses = $map.REWMap('getRadiuses');

		// POST HTTP Request, Expect JSON
		$.ajax({
			'url' : '<?=Settings::getInstance()->SETTINGS['URL_IDX_AJAX']; ?>map.php?searchLayers',
			'type' : 'POST',
			'dataType' : 'json',
			'data' : {
				'pid'		: pid,
				'type'		: type,
				'latitude'	: center.lat(),
				'longitude'	: center.lng(),
				'ne'		: bounds.getNorthEast().toUrlValue(),
				'sw'		: bounds.getSouthWest().toUrlValue(),
				'polygon'	: (polygons ? polygons : ''),
				'radius'	: (radiuses ? radiuses : '')
			},
			'success'  : function (json, textStatus) {
				if (!json || json.pid != pid) return;
				if (json.returnCode == 200) {

					// Load Results
					layerManager.load(json.results);

				}
			}
		});

	}

});

// Save This Search
$('#save-link').live(BREW.events.click, function () {

	// Search Data
	var search = search_query;
	search.search_by = 'map';

	// Save Search
	saveSearch(search);

	// Return
	return false;

});

// Edit Search
$('#edit_search, #edit_search_email').live(BREW.events.click, function () {

	// Trigger search update
	$form.trigger('submit');

	// Search Data
	var search = search_query;
	search.search_by = 'map';
	search.saved_search_id = $form.find('input[name="saved_search_id"]').val();
	search.frequency = $form.find('select[name="frequency"]').val();
	search.email_results_immediately = $(this).attr('id') == 'edit_search_email' ? 'true' : 'false';

	// Edit Search
	editSearch(search);

	// Return
	return false;

});

/* </script> */
<?php

// Write Javascript
$page->writeJS(ob_get_clean());

?>