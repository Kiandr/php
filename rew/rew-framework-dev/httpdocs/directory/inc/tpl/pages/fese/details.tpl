<?php

// Include Header
include $page->locateTemplate('directory', 'misc', 'header');

// Require Listing
if (!empty($entry)) {

    // Listing Details
    echo '<div id="directory-details">';
    include $page->locateTemplate('directory', 'misc', 'details');
    echo '<div class="btns"><a href="' . $url_back . '" class="btn">Back to ' . Format::htmlspecialchars($category['title']) . '</a></div>';
    echo '</div>';

// Listing Not Found
} else {
    echo '<h1>Directory Error</h1>';
    echo '<p class="msg msg--neg">The selected listing could not be found.</p>';

}