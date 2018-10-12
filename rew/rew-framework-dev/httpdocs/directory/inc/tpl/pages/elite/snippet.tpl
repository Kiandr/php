<?php

// Require Results
if (!empty($count_entries)) {

	// Snippet Title
	if (!empty($_GET['snippet_title'])) echo '<h2>' . Format::htmlspecialchars($_GET['snippet_title']) . '</h2>';

	// Include Pagination TPL (Top)
	if (!empty($pagination_tpl)) {
		$pagination['extra'] = 'top';
		include $pagination_tpl;
	}

	// Display Listings
	if (!empty($entries)) {
		echo '<div class="articleset">';
		foreach ($entries as $entry) {
			include $page->locateTemplate('directory', 'misc' ,'result');
		}
		echo '</div>';
	}

	// Include Pagination TPL (Bottom)
	if (!empty($pagination_tpl)) {
		$pagination['extra'] = 'bottom';
		include $pagination_tpl;
	}

}