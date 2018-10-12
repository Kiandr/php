<?php

// Exclude from IDX Snippets
if (empty($_REQUEST['snippet'])) {

	// Backend user
	$backend_user = Auth::get();

	// Backend: Create Saved Search
	if (!empty($_REQUEST['create_search']) && $backend_user->isValid() && !empty($lead)) {
		echo '<div class="msg vanilla results">';
		echo 'Creating search for: <a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/searches/?id=' . $lead['id'] . '">' . Format::htmlspecialchars($lead['first_name'] . ' ' . $lead['last_name']) . '</a>';
		echo '</div>';

	// Backend: Edit Saved Search
	} else if (!empty($saved_search) && !empty($_REQUEST['edit_search']) && $backend_user->isValid() && !empty($lead)) {
		$edit_controls = true;
		echo '<div class="msg vanilla results">';
		echo 'Refining search for: <a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/searches/?id=' . $lead['id'] . '">' . Format::htmlspecialchars($lead['first_name'] . ' ' . $lead['last_name']) . '</a>';
		echo '</div>';

	// Edit Saved Search
	} else if (!empty($saved_search) && !empty($_REQUEST['edit_search'])) {
		//echo '<div class="msg vanilla results">';
		//echo 'Refining saved search: ' . Format::htmlspecialchars($saved_search['title']);
		//echo '</div>';

	// View Saved Search
	} else if (!empty($saved_search)) {
		echo '<div class="msg vanilla results">';
		echo 'Viewing saved search: ' . Format::htmlspecialchars($saved_search['title']);
		echo '</div>';

	}

}