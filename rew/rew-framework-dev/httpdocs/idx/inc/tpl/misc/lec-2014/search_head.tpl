<div id="search_head">

	<div class="line line_1">
		<?php

			// Search Title
			if (empty($_REQUEST['snippet'])) {
				echo '<h1>' . (!empty($search_title) ? $search_title : 'Search Results') . '</h1>';
			}

		?>

		<?php

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

		?>

		<div class="display-options">
			<div class="nav horizontal">
				<ul>
					<li class="grid<?=(empty($view) || $view == 'grid' || $view == 'map') ? ' current' : ''; ?>"><a class="view" data-view="grid" title="View Results as Grid"> <span>Grid</span></a></li>
					<li class="list<?=($view == 'detailed' ? ' current"' : ''); ?>"><a class="view" data-view="list" title="View Results as List"> <span>List</span></a></li>
				</ul>
			</div>
			<?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING'])) { ?>
				<span class="button map<?=($view == 'map' || !empty($_REQUEST['map']['open']) ? ' current' : ''); ?>"><a class="view" data-view="map"> <span>Show Map</span></a></span>
			<?php } ?>
		</div>

	</div>

	<div class="line line_2">

		<i class="icon-exclamation-sign" <?php if (!empty($_COMPLIANCE['limit']) && $search_results_count['total'] > $_COMPLIANCE['limit']) { ?>title="Only <?=Format::number($_COMPLIANCE['limit']); ?> properties may be displayed per search. To see all of your results, try narrowing your search criteria."<?php } ?>></i>
		<?=Format::number($search_results_count['total']); ?> Properties Found.

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

					// Delete Search
					echo '<a class="action-deleteSearch" href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/searches/?id=' . $lead['id'] . '&delete=' . $saved_search['id'] . '" onclick="return confirm(\'Are you sure you want to delete this saved search?\');">Delete Search</a>';

				// Viewing Saved Search
				} else if (!empty($saved_search)) {

					// Save Changes
					if (!empty($_REQUEST['edit_search'])) {
						echo '<a class="action-saveSearch" href="javascript:void(0);" id="edit_search">Save Changes</a>';

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

		<div class="results-sum">

			<?php if (!empty($pagination) && !empty($pagination['pages']) && $pagination['pages'] > 1) { ?>
				<span style="padding: 12px">Page <?=Format::number($pagination['page']); ?> of <?=Format::number($pagination['pages']); ?></span>
			<?php } ?>

			<?php if (!empty($pagination)) { ?>
				<div class="pagination" style="float: right; padding: 0; margin: 0 0 0 5px;">

					<?php if (!empty($pagination['prev'])) { ?>
						<a href="<?=$pagination['prev']['url']; ?>"> Prev</a>
					<?php } ?>
					<?php if (!empty($pagination['next'])) { ?>
						<a href="<?=$pagination['next']['url']; ?>"> Next</a>
					<?php } ?>

				</div>

			<?php } ?>
		</div>

	</div>


</div>

<?php if (empty($_REQUEST['snippet'])) { ?>
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
<?php } ?>
