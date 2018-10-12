<?php

// Exclude from IDX snippets
if (empty($_REQUEST['snippet'])) {

	// Backend auth user
	$backend_user = Auth::get();
	if (!$backend_user->isValid()) {
		unset($backend_user);
	}

	// Search can be saved
	$max_save = 500;
	$can_save = isset($_GET['auto_save']) || $search_results_count['total'] <= $max_save;

	// Backend: Create saved search
	if (!empty($backend_user) && !empty($lead) && !empty($_REQUEST['create_search'])) {
		$leadname =  $lead['first_name'] . ' ' .  $lead['last_name'];
		$leadview = Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/searches/?id=' . $lead['id'];
		echo '<div class="search-controls">';
		echo 'Creating saved search for: <a href="' . $leadview . '">' . Format::htmlspecialchars($leadname) . '</a>';
		echo '<span class="pull-right">';
		if (!empty($can_save)) {
			echo '<a class="buttonstyle mini strong" id="save-link"><i class="icon-search"></i> Save Search</a>' . PHP_EOL;
		} else {
			echo 'Narrow to less than ' . Format::number($max_save) . ' results to save.';
		}
		echo '</span>';
		echo '</div>';

	// Backend: Editing saved search
	} else if (!empty($backend_user) && !empty($lead) && !empty($saved_search) && !empty($_REQUEST['edit_search'])) {
		$leadname =  $lead['first_name'] . ' ' .  $lead['last_name'];
		$leadview = Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/searches/?id=' . $lead['id'];
		echo '<div class="search-controls">';
		echo 'Editing saved search for: <a href="' . $leadview . '">' . Format::htmlspecialchars($leadname) . '</a>';
		echo '<span class="pull-right">';
		if (!empty($can_save)) {
			echo '<a class="buttonstyle mini strong" id="edit_search"><i class="icon-save"></i> Save Changes</a>' . PHP_EOL;
			echo '<a class="buttonstyle mini strong" id="edit_search_email"><i class="icon-save"></i> Save and Email Results</a>' . PHP_EOL;
			echo '<a class="buttonstyle mini" href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/searches/?id=' . $lead['id'] . '&delete=' . $saved_search['id'] . '" onClick="return confirm(\'Are you sure you want to delete this saved search?\');"><i class="icon-trash"></i> Delete</a>';
		} else {
			echo 'Narrow to less than ' . Format::number($max_save) . ' results to save.';
		}
		echo '</span>';
		echo '</div>';

	// Editing saved search
	} else if (!empty($saved_search) && !empty($_REQUEST['edit_search'])) {
		$searchurl = sprintf(Settings::getInstance()->SETTINGS['URL_IDX_SAVED_SEARCH'], $saved_search['id']);
		echo '<div class="search-controls">';
		echo 'Editing saved search: <a href="' . $searchurl . '">' . Format::htmlspecialchars($saved_search['title']) . '</a>';
		echo '<span class="pull-right">';
		echo '<a class="buttonstyle mini strong" id="edit_search"><i class="icon-save"></i> Save Changes</a>' . PHP_EOL;
		echo '<a class="buttonstyle mini strong" id="edit_search_email"><i class="icon-save"></i> Save and Email Results</a>' . PHP_EOL;
		echo '</span>';
		echo '</div>';

	// Viewing saved search
	} elseif (!empty($saved_search)) {
		$searchurl = sprintf(Settings::getInstance()->SETTINGS['URL_IDX_SAVED_SEARCH'], $saved_search['id']);
		echo '<div class="search-controls">';
		echo 'Viewing saved search: <a href="' . $searchurl . '">' . Format::htmlspecialchars($saved_search['title']) . '</a>';
		echo '<div class="pull-right">';

		// Link to edit saved search
		echo '<a class="buttonstyle mini strong" href="?edit_search=true&saved_search_id=' . $saved_search['id'] . '"><i class="icon-pencil"></i> Edit Search</a>' . PHP_EOL;

		// Delete this saved search by sending POST request to IDX dashboard
		echo '<form action="/idx/dashboard.html?view=searches" method="post" onsubmit="return confirm(\'Are you sure you want to remove this search?\');" style="display: inline-block; float: none; padding: 0; margin: 0;">';
		echo '<button type="submit" class="buttonstyle mini" style="float: right;"><i class="icon-trash"></i> Delete</button>';
		echo '<input name="delete" value="' . Format::htmlspecialchars($saved_search['id']) . '" type="hidden">';
		echo '</form>';

		echo '</div>';
		echo '</div>';

	// Must refine to save
	} elseif (empty($can_save)) {
		echo '<p class="pull-right">';
		echo 'Narrow to less than ' . Format::number($max_save) . ' results to save.';
		echo '</p>';

	}

}