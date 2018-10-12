<?php

// Include Header
include $page->locateTemplate('directory', 'misc', 'header');

// Require category
if (empty($category)) {
	echo '<h1>Directory Error</h1>';
	echo '<div class="msg negative"><p>The selected category could not be found.</p></div>';
	return;
}

// Category details
echo '<h1>' . Format::htmlspecialchars($category['title']) . '</h1>';
if (!empty($category['category_content'])) {
    echo '<div class="category-content">' . $category['category_content'] . '</div>';
}

// Sub-categories
if (!empty($refine_categories)) {
	echo '<div class="nav category-results">';
	echo '<h3>Refine Your Search</h3>';
	echo '<ul class="uk-list uk-grid">';
	foreach ($refine_categories as $refine_category) {
		echo '<li class="uk-width-small-1-2 uk-width-medium-1-3">';
		echo '<a href="' . sprintf(URL_DIRECTORY_CATEGORY, $refine_category['link']) . '">' . Format::htmlspecialchars($refine_category['title']) . '</a>' . PHP_EOL;
		echo '<span class="label">' . Format::number($refine_category['count']) . '</span>' . PHP_EOL;
		echo '</li>';
	}
	echo '</ul>';
	echo '</div>';
}

// Directory listings
if (!empty($count_entries)) {
    echo '<div class="uk-alert"><p>There are currently <strong>' . Format::number($count_entries) . '</strong> listings related to this category.</p></div>';

	// Display Listings
	if (!empty($entries)) {
		echo '<div class="category-listings uk-grid uk-grid-small">';
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

} else {
    echo '<div class="uk-alert uk-alert-warning"><p>There are currently no listings related to this category.</p></div>';

}

// Related categories
if (!empty($related_categories)) {
	echo '<div class="nav category-results">';
	echo '<h2>Related Categories</h2>';
	echo '<ul class="uk-list uk-grid">';
	foreach ($related_categories as $related_category) {
		echo '<li class="uk-width-small-1-2 uk-width-medium-1-3">';
		echo '<a href="' . sprintf(URL_DIRECTORY_CATEGORY, $related_category['link']) . '">' . Format::htmlspecialchars($related_category['title']) . '</a>' . PHP_EOL;
		echo '<span class="label">' . Format::number($related_category['count']) . '</span>' . PHP_EOL;
		echo '</li>';
	}
	echo '</ul>';
	echo '</div>';
}