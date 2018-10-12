<?php

// Include Header
include $page->locateTemplate('directory', 'misc', 'header');

// Featured Listings
if (!empty($listings)) {
	echo '<div class="nav uk-margin-large site-map-category">';
	echo '<h2>Featured Listings</h2>';
	echo '<ul class="category-results uk-grid uk-width-1-1" data-uk-margin>';
	foreach ($listings as $listing) {
		echo '<li class="uk-width-1-1 uk-margin-large-bottom"><a href="' . $listing['url_details'] . '">' . Format::htmlspecialchars($listing['business_name']) . '</a></li>';
	}
	echo '</ul>';
	echo '</div>';

	// Include Pagination TPL (Top)
	if (!empty($pagination_tpl)) {
		$pagination['extra'] = 'bottom';
		include $pagination_tpl;
	}

}

// Category Sitemap
if (!empty($sitemap_cats)) {

	echo '<div class="nav site-map-category">';
	echo '<h2>Directory Categories</h2>';
	echo '<ul class="category-results uk-grid uk-width-1-1" data-uk-margin>';
	foreach ($sitemap_cats as $sitemap_cat) {
		echo '<li class="uk-width-1-1 uk-margin-large-bottom">';
		echo '<h3 class="uk-h2"><a href="' . sprintf(URL_DIRECTORY_CATEGORY, $sitemap_cat['link']) . '"> ' . Format::htmlspecialchars($sitemap_cat['title']) . '</a></h3>';
        if (!empty($sitemap_cat['subcategories'])) {
			echo '<ul class="uk-list uk-grid">';
			foreach ($sitemap_cat['subcategories'] as $sub_cat) {
				echo '<li class="uk-width-small-1-2 uk-width-medium-1-3">';
				echo '<a href="' . sprintf(URL_DIRECTORY_CATEGORY, $sub_cat['link']) . '">' . Format::htmlspecialchars($sub_cat['title']) . '</a>';
				if (!empty($sub_cat['subcategories'])) {
					echo '<ul>';
					foreach ($sub_cat['subcategories'] as $tet_cat) {
						echo '<li><a href="' . sprintf(URL_DIRECTORY_CATEGORY, $tet_cat['link']) . '">' . Format::htmlspecialchars($tet_cat['title']) . '</a></li>';
					}
					echo '</ul>';
				}
				echo '</li>';
			}
			echo '</ul>';
        }
		echo '</li>';
	}
	echo '</ul>';
	echo '</div>';

// No Categories Found
} elseif ($directory_settings['sitemap'] == 'cat') {
	echo '<div class="uk-alert uk-alert-warning">';
	echo '<p>Sorry, but there are currently no categories at this time.</p>';
	echo '</div>';

}