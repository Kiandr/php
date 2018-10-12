<?php

// Categories
echo '<div class="categories">';

// Include Header
include $page->locateTemplate('directory', 'misc', 'header');

// Display Categories
if (!empty($categories)) {
	echo '<div class="category-results uk-grid uk-width-1-1 uk-margin-bottom uk-margin-large-top" data-uk-margin>';
	foreach (array_chunk($categories, ceil(count($categories) / 2)) as $k => $categories) {
		echo '<div class="uk-width-1-1 uk-margin-bottom">';
		foreach ($categories as $category) {
			echo '<div class="uk-width-1-1 uk-margin-large-bottom">';
			echo '<h2 class="uk-h2"><a href="' . sprintf(URL_DIRECTORY_CATEGORY, $category['link']) . '">' . Format::htmlspecialchars($category['title']) . '</a></h2>';
			if (!empty($category['subcategories'])) {
				echo '<ul class="uk-list uk-grid">';
				foreach( $category['subcategories'] as $subcategory) {
					echo '<li class="uk-width-small-1-2 uk-width-medium-1-3"><a href="' . sprintf(URL_DIRECTORY_CATEGORY, $subcategory['link']) . '">' . Format::htmlspecialchars($subcategory['title']) . '</a></li>';
				}
				echo '</ul>';
			}
			echo '</div>';
		}
		echo '</div>';
	}
	echo '</div>';

// No Categories
} else {
	echo '<div class="category-results uk-grid uk-width-1-1 uk-margin-bottom uk-margin-large-top" data-uk-margin>';
	echo '<div class="msg"><p>There are currently no categories.</p></div>';
	echo '</div>';
}

echo '</div>';

// Sitemap Link
echo '<p><a href="/directory/sitemap.html">Directory Site Map</a></p>';