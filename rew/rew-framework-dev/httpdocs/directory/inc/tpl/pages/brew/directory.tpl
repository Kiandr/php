<?php

// Categories
echo '<div class="categories">';

// Include Header
include $page->locateTemplate('directory', 'misc', 'header');

// Display Categories
if (!empty($categories)) {
	echo '<div class="grid_12">';
	foreach (array_chunk($categories, ceil(count($categories) / 2)) as $k => $categories) {
		echo '<div class="x6 o' . ($k * 6) . '">';
		foreach ($categories as $category) {
			echo '<div class="nav">';
			echo '<h2><a href="' . sprintf(URL_DIRECTORY_CATEGORY, $category['link']) . '">' . Format::htmlspecialchars($category['title']) . '</a></h2>';
			if (!empty($category['subcategories'])) {
				echo '<ul>';
				foreach( $category['subcategories'] as $subcategory) {
					echo '<li><a href="' . sprintf(URL_DIRECTORY_CATEGORY, $subcategory['link']) . '">' . Format::htmlspecialchars($subcategory['title']) . '</a></li>';
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
	echo '<div class="msg"><p>There are currently no categories.</p></div>';
}

echo '</div>';

// Sitemap Link
echo '<p><a href="/directory/sitemap.html">Directory Site Map</a></p>';