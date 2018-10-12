<?php

// Include Header
include $page->locateTemplate('directory', 'misc', 'header');

// Require Listing
if (!empty($entry)) {

	// Listing Details
	echo '<div id="directory-details">';
	include $page->locateTemplate('directory', 'misc', 'details');
	echo '<p><a href="' . $url_back . '" class="btn strong">Back to ' . Format::htmlspecialchars($category['title']) . '</a></p>';
	echo '</div>';

// Listing Not Found
} else {
	echo '<h1>Directory Error</h1>';
	echo '<div class="msg negative"><p>The selected listing could not be found.</p></div>';

}