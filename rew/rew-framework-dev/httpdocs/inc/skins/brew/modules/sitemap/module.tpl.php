<?php

// Require Pages
if (!empty($sitemap)) {

	// Show Alpha Bar
	echo '<div class="alpha">';
	if (isset($sitemap['#'])) echo '<li><a href="#num">#</a></li>';
	foreach (range('A', 'Z')  as $letter) {
		if (isset($sitemap[$letter])) {
			echo '<a href="#' . $letter . '">' . $letter . '</a>';
		} else {
			//echo '<a href="#' . $letter . '" class="disabled">' . $letter . '</a>';
		}
	}
	echo '</div>';

	// Sort Pages
	ksort($sitemap, SORT_LOCALE_STRING);

	// Show Pages
	foreach ($sitemap as $letter => $pages) {

		// Sort Pages
		ksort($pages, SORT_LOCALE_STRING);

		// Build HTML
		echo '<article>';
		echo '<header><h2><a name="' . ($letter == '#' ? 'num' : $letter) . '">' . $letter . '</a></h2></header>';
		echo '<ul>';
		foreach ($pages as $page) {
			echo '<li><a href="' . $page['link'] . '">' . $page['title'] . '</a></li>';
		}
		echo '</ul>';
		echo '</article>';

	}

}