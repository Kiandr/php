<?php

// Can Save Search
$can_save = (isset($_GET['auto_save']) || ($search_results_count['total'] <= 500));

// Backend user
$backend_user = Auth::get();

// Backend: Create Saved Search
if (!empty($_REQUEST['create_search']) && $backend_user->isValid() && !empty($lead) && !empty($can_save)) {
	echo '<a class="buttonstyle mini strong" id="save-link">Save this Search</a>' . PHP_EOL;

// Backend: Edit Saved Search
} else if (!empty($_REQUEST['edit_search']) && $backend_user->isValid() && !empty($lead)) {

	// Must refine
	if (empty($can_save)) {
		echo '<a class="buttonstyle mini strong" style="text-transform: none;"><em>Refine Search Results</em></a>' . PHP_EOL;

	// Can save
	} else {
		echo '<a class="buttonstyle mini strong" id="edit_search">Save Changes</a>' . PHP_EOL;
		echo '<a class="buttonstyle mini strong" id="edit_search_email">Save and Email Results</a>' . PHP_EOL;
	}

	// Delete Search
	echo '<a class="buttonstyle mini" href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/searches/?id=' . $lead['id'] . '&delete=' . $saved_search['id'] . '" onclick="return confirm(\'Are you sure you want to delete this saved search?\');">Delete Search</a>' . PHP_EOL;

// Edit Saved Search
} else if (!empty($saved_search) && !empty($_REQUEST['edit_search'])) {
	echo '<a class="buttonstyle mini strong" id="edit_search">Save Changes</a>' . PHP_EOL;
	echo '<a class="buttonstyle mini strong" id="edit_search_email">Save and Email Results</a>' . PHP_EOL;

// View Saved Search
} else if (!empty($saved_search)) {

	// Link to edit saved search
	echo '<a class="buttonstyle mini strong" href="?edit_search=true&saved_search_id=' . $saved_search['id'] . '">Edit this Search</a>' . PHP_EOL;

	// Delete this saved search by sending POST request to IDX dashboard
	echo '<form action="/idx/dashboard.html?view=searches" method="post" onsubmit="return confirm(\'Are you sure you want to remove this search?\');" style="display: inline-block; float: none; padding: 0; margin: 0;">';
	echo '<button type="submit" class="buttonstyle mini" style="float: right; margin: 0;"><i class="icon-trash"></i> Delete</button>';
	echo '<input name="delete" value="' . Format::htmlspecialchars($saved_search['id']) . '" type="hidden">';
	echo '</form>';

// Can save this search
} else if (!empty($can_save)) {
	echo '<a class="buttonstyle mini strong" id="save-link"><i class="icon-save"></i> Save this Search</a>' . PHP_EOL;

}

// Sort Orders
$sortorders = IDX_Builder::getSortOptions();

// Sort Options
if ($this->info('app') !== 'idx-map' && !empty($sortorders) && is_array($sortorders)) {
	// Display Sort Order
	if (!empty($_REQUEST['sortorder'])) {
		$current = null;
		foreach ($sortorders as $sortorder) {
			if ($sortorder['value'] == $_REQUEST['sortorder']) {
				$current = $sortorder['title'];
			}
		}
		if (!empty($current)) {
			echo '<div class="sort mini">';
			echo '<span class="sort-text">Sort listings by:</span>';
			echo '<a class="buttonstyle" data-menu="#sort-menu">';
			echo $current . PHP_EOL;
			echo preg_match('/^DESC\-/', $_REQUEST['sortorder']) ? '<i class="icon-chevron-down"></i>' : '<i class="icon-chevron-up"></i>';
			echo '</a>';
			echo '</div>';
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
