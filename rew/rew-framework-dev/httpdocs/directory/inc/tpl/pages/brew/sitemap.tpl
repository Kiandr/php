<?php

// Include Header
include $page->locateTemplate('directory', 'misc', 'header');

// Featured Listings
if (!empty($listings)) {
	echo '<div class="nav">';
	echo '<h2>Featured Listings</h2>';
	echo '<ul>';
	foreach ($listings as $listing) {
		echo '<li><a href="' . $listing['url_details'] . '">' . Format::htmlspecialchars($listing['business_name']) . '</a></li>';
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

	echo '<div class="nav">';
	echo '<h2>Directory Categories</h2>';
	echo '<ul>';
	foreach ($sitemap_cats as $sitemap_cat) {
		echo '<li>';
		echo '<a href="' . sprintf(URL_DIRECTORY_CATEGORY, $sitemap_cat['link']) . '"> ' . Format::htmlspecialchars($sitemap_cat['title']) . '</a>';
        if (!empty($sitemap_cat['subcategories'])) {
			echo '<ul>';
			foreach ($sitemap_cat['subcategories'] as $sub_cat) {
				echo '<li>';
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
	echo '<div class="msg caution">';
	echo '<p>Sorry, but there are currently no categories at this time.</p>';
	echo '</div>';

}