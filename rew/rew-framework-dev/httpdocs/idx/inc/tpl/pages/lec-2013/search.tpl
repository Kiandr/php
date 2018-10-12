<a id="homes-for-sale"></a>

<div id="search_head">

	<div class="line line_1">
		<?php

			// Search Title
			if (empty($_REQUEST['snippet'])) {
				echo '<h1>' . (!empty($search_title) ? $search_title : 'Search Results') . '</h1>';
			}

			// Sort Orders
			$sortorders = IDX_Builder::getSortOptions();

			// Sort Options
			if (!empty($sortorders) && is_array($sortorders)) {

				// Display Sort Order
				if (!empty($_REQUEST['sortorder'])) {
					$current = null;
					foreach ($sortorders as $sortorder) {
						if ($sortorder['value'] == $_REQUEST['sortorder']) {
							$current = $sortorder['title'];
						}
					}
					if (!empty($current)) {
						echo '<div class="sort" data-menu="#sort-menu"><span>' . $current . '</span></div>';
					}
				}

				// Display Menu
				echo '<div class="menu hidden" id="sort-menu">';
				echo '<ul>';
				foreach ($sortorders as $sortorder) {
					$checked = ($_REQUEST['sortorder'] == $sortorder['value']) ? ' checked' : '';
					$value = '?' . Format::htmlspecialchars(http_build_query(array_merge($querystring_nosort, array('sortorder' => $sortorder['value']))));
					echo '<li><label><input type="radio" name="sort" onchange="window.location = this.value"' . $checked . ' value="' . $value . '"> ' . $sortorder['title'] . '</label></li>';
				}
				echo '</ul>';
				echo '</div>';
			}

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
			}?>

		<div class="nav horizontal" id="compliance">
			<ul>
				<li <?=$grid_id; ?> class="grid<?=(empty($view) || $view == 'grid' || $view == 'map') ? ' current' : ''; ?>"><a class="view" href="#grid" title="View Results as Grid"> <span>Grid</span></a></li>
				<li <?=$detailed_id; ?> class="list<?=($view == 'detailed' ? ' current"' : ''); ?>"><a class="view" href="#detailed" title="View Results as List"> <span>List</span></a></li>
				<?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING'])) { ?>
				    <li class="map<?=($view == 'map' || !empty($_REQUEST['map']['open']) ? ' current' : ''); ?>"><a class="view" href="#map"> <span>Show Map</span></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>

	<div class="line line_2">
		<div class="msg">
			<?=number_format($search_results_count['total']); ?> Properties Found.
			<?php

				// Exclude from IDX Snippets
				if (empty($_REQUEST['snippet'])) {

					// Can Save Search
					$can_save = isset($_GET['auto_save']) || ($search_results_count['total'] <= 500);

					// Must Refine Search
					if (empty($can_save)) {
						echo '<em>Please refine your search to less than 500 results to save.</em>';

					// Backend: Create Saved Search
					} else if (!empty($_REQUEST['create_search']) && $backend_user->isValid() && !empty($lead)) {

						// Refine Search
						echo '<a class="action-saveSearch" href="javascript:void(0);" id="save-link">Save this Search for ' . Format::htmlspecialchars($lead['first_name'] . ' ' . $lead['last_name']) . '</a>';

					// Backend: Create Saved Search
					} else if (!empty($_REQUEST['edit_search']) && $backend_user->isValid() && !empty($lead)) {

						// Save Search
						echo '<a class="action-saveSearch" href="javascript:void(0);" id="edit_search">Save Changes</a>';
						echo '<a class="action-saveSearch" href="javascript:void(0);" id="edit_search_email">Save and Email Results</a>';

						// Delete Search
						echo '<a class="action-deleteSearch" href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/searches/?id=' . $lead['id'] . '&delete=' . $saved_search['id'] . '" onclick="return confirm(\'Are you sure you want to delete this saved search?\');">Delete Search</a>';

					// Viewing Saved Search
					} else if (!empty($saved_search)) {

						// Save Changes
						if (!empty($_REQUEST['edit_search'])) {
							echo '<a class="action-saveSearch" href="javascript:void(0);" id="edit_search">Save Changes</a>';
							echo '<a class="action-saveSearch" href="javascript:void(0);" id="edit_search_email">Save and Email Results</a>';

						// Edit Search
						} else {
							echo '<a class="action-editSearch" href="?edit_search=true&saved_search_id=' . $saved_search['id'] . '">Edit this Search</a>';

						}

					// Save Search
					} else {
						echo '<a class="action-saveSearch" href="javascript:void(0);" id="save-link">Save this Search</a>';

					}

				}

			?>
			<?php if (!empty($_COMPLIANCE['limit']) && $search_results_count['total'] > $_COMPLIANCE['limit']) { ?>
				<p class="message">Only <?=number_format($_COMPLIANCE['limit']); ?> properties may be displayed per search. To see all of your results, try narrowing your search criteria.</p>
			<?php } ?>
		</div>
		<?php if (!empty($pagination)) { ?>
			<div class="pagination">
				<div class="msg">Page <?=number_format($pagination['page']); ?> of <?=number_format($pagination['pages']); ?></div>
				<div class="nav horizontal">
					<ul>
						<?php if (!empty($pagination['prev'])) { ?>
							<li class="prev"><a href="<?=$pagination['prev']['url']; ?>"> Prev</a></li>
						<?php } ?>
						<?php if (!empty($pagination['next'])) { ?>
							<li class="next"><a href="<?=$pagination['next']['url']; ?>"> Next</a></li>
						<?php } ?>
					</ul>
				</div>
			</div>
		<?php } ?>
	</div>

</div>

<?php

	// Exclude from Snippets
	if (empty($_REQUEST['snippet'])) {

?>
<div id="search_mast" class="grid_12">
	<div class="form">
		<?php

			// Search by Location
			IDX_Panel::get('Location', array(
				'toggle'		=> false,
				'panelClass'	=> 'x4 o0',
				'inputClass'	=> 'location autocomplete',
				'placeholder'	=> 'City, ' . Locale::spell('Neighborhood') . ', ' . Locale::spell('ZIP') . ' or ' . Lang::write('MLS_NUMBER')
			))->display();

		?>
		<div class="field x8 o4">
			<?php

				// All Rental Types
				$rental_types = array('Rental', 'Rentals', 'Lease', 'Residential Lease', 'Commercial Lease', 'Residential Rental');

				// Range Type (Sale/Rental mixed or Rental-types only)
				$type	= $_REQUEST['search_type'];
				$range	=
					(is_string($type) && in_array($type, $rental_types)) ||
					(!empty($type) && is_array($type) && (array_diff($type, $rental_types) === []))
					? 'rent' : 'sale';

				// Price Range Panel
				$price = IDX_Panel::get('Price');

				// Sale Prices
				$disabled = ($range === 'sale' ? '' : ' disabled');
				echo '<div id="search_price"' . (!empty($disabled) ? ' class="hidden"' : '') . '>';
				echo '<div class="field">';
				echo '<label>Price Range</label>';
				echo '<div class="noUiSlider"></div>';
				echo $price->getMinPriceSelect('class="hidden"' . $disabled);
				echo $price->getMaxPriceSelect('class="hidden"' . $disabled);
				echo '</div>';
				echo '</div>';

				// Rental Prices
				$disabled = ($range === 'rent' ? '' : ' disabled');
				echo '<div id="search_rent"' . (!empty($disabled) ? ' class="hidden"' : '') . '>';
				echo '<div class="field">';
				echo '<label>Price Range</label>';
				echo '<div class="noUiSlider"></div>';
				echo $price->getMinRentSelect('class="hidden"' . $disabled);
				echo $price->getMaxRentSelect('class="hidden"' . $disabled);
				echo '</div>';
				echo '</div>';

			?>
		</div>
	</div>
	<div class="save_search">
		<button type="button" id="btn-refine" class="btn strong"><?=Lang::write('IDX_SEARCH_REFINE_BUTTON'); ?></button>
		<a href="#sidebar" class="action-show-sidebar">More Options</a>
	</div>
</div>


<?php

	}


	// Not an IDX Snippet!
	if (empty($_REQUEST['snippet'])) {

		// Display Side Bar
		echo '<aside id="sidebar" class="hidden-tablet">';
		$this->container('sidebar')->loadModules();
		echo '</aside>';

	}

	// Display Map
	echo '<div id="listings-map" class="hidden"></div>';

	// Not an IDX Snippet!
	if (empty($_REQUEST['snippet'])) {

		// Wrap Content
		echo '<div id="content">';

	}

	// Search Results
	if (!empty($results)) {
		echo '<div class="articleset listings' . ($view == 'detailed' ? '' : ' colset_3') . '">';
		foreach ($results as $index => $result) {
			include $result_tpl;
		}
		echo '</div>';

	// No Results
	} else {
		echo '<div class="msg"><p>No listings were found matching your search criteria.</p></div>';

	}

	// Wrap Content
	if (empty($_REQUEST['snippet'])) echo '</div>';

	// Pagination
	if (empty($idxSnippetAlreadyIncluded)) include $this->locateTemplate('idx', 'misc', 'pagination');

	if (!empty($_COMPLIANCE['results']['show_immediately_below_listings'])) {
		echo '<div class="show-immediately-below-listings">';
		\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(true);
		echo '</div>';
	}

// Start Javascript
ob_start();

?>
/* <script> */
var $form = $('form.idx-search');
(function () {

	// Setup Sort Menu
	$('.sort[data-menu]').each(function() {
		var $link = $(this),
			$menu = $($link.data('menu'))
		;

		// No Menu Found
		if ($menu.length === 0) return;

		// better for positioning
		$menu.appendTo('body');

		// Hide Menu on Document Click
		$(document).on('click', function (e) {
			var $t = $(e.target).closest('.menu');
			if ($t.get(0) != $menu.get(0)) {
				$menu.addClass('hidden');
				$link.removeClass('active');
			}
		});

		// Toggle Menu
		$link.on('click', function() {
			// Show Menu
			if ($menu.hasClass('hidden')) {
				var offset = $link.offset();
				$menu.css({
					'position' : 'absolute',
					'left' : offset.left,
					'top' : offset.top + $link.outerHeight()
				}).removeClass('hidden');
				$link.addClass('active');
			// Hide Menu
			} else {
				$menu.addClass('hidden');
				$link.removeClass('active');
			}
			return false;
		});

	});

	// Switch View
	$('a.view').on(BREW.events.click, function () {
		var $this = $(this), view = $(this).attr('href');

		// View as Grid
		if (view == '#grid') {
			BREW.Cookie('results-view', state.view = 'grid');
			$this.parent().addClass('current').siblings().removeClass('current');
			$('#content .articleset.listings').addClass('colset_3');
			return true;

		// View as List
		} else if (view == '#detailed') {
			BREW.Cookie('results-view', state.view = 'detailed');
			$this.parent().addClass('current').siblings().removeClass('current');
			$('#content .articleset.listings').removeClass('colset_3');
			return true;

		// Toggle Map
		} else if (view == '#map') {

			// Show Map
			if ($map.hasClass('hidden')) {

				// Update Anchor
				$this.find('span').text('Hide Map');

				// Save State
				BREW.Cookie('results-map', state.map = 1);

				// Show Map & Map Panels
				$map.REWMap('show', function () {
					$('#field-polygon').removeClass('hidden');
					$('#field-radius').removeClass('hidden');
					$('#field-bounds').removeClass('hidden');
				});

				// Adjust Refine bar positioning
				$('#sidebar').addClass('map-adjust');

			} else {

				// Update Anchor
				$this.find('span').text('Show Map');

				// Save State
				BREW.Cookie('results-map', state.map = 0);

				// Hide Map & Map Panels
				$map.REWMap('hide', function () {
					$('#field-polygon').addClass('hidden');
					$('#field-radius').addClass('hidden');
					$('#field-bounds').addClass('hidden');
				});

				// Adjust Refine bar positioning
				$('#sidebar').removeClass('map-adjust');

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
	if (state.view) $('a.view[href="#' + state.view + '"]').trigger(BREW.events.click);

	// Show Map
	if (!BREW.mobile && state.map && state.view != 'map') $('a.view[href="#map"]').trigger(BREW.events.click);

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
		saveSearch($.extend(true, criteria, {
			view : state.view,
			saved_search_id : false
		}));
		return false;
	});

	// Edit Search
	$('#edit_search, #edit_search_email').live(BREW.events.click, function () {
        var email_results_immediately = $(this).attr('id') == 'edit_search_email' ? 'true' : 'false';
		editSearch($.extend(true, criteria, {
			view : state.view,
            email_results_immediately : email_results_immediately
		}));
		return false;
	});

})();
/* </script> */
<?php

// Trigger Save Search
if (isset($_GET['auto_save'])) {
	echo '$(\'#save-link\').trigger(\'click\');' . PHP_EOL;
}

// Write Javascript
$page->writeJS(ob_get_clean());
