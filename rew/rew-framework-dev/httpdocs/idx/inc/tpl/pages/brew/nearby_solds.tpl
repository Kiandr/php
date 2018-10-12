<?php

// Listing Not Found
if (empty($property)) {
	echo '<h1>Property Record Not Found</h1>';
	echo '<p class="msg negative">The selected public record property could not be found.</p>';

} else {

	// Details Tabset
	if (Settings::getInstance()->SKIN != 'lec-2013') {
		include $page->locateTemplate('idx', 'misc', 'details');
	}

	// Require Points
	if (!empty($points)) {

		// Title
		echo '<div id="map_info">'
			. '<h2 class="miles_from">You are viewing comparable properties within <span class="miles">0.25</span> miles from ' . $property['SitusAddress'] . '</h2>'
			. '</div>';

		// Map Container
		echo '<div id="map_canvas"></div>';

		// If we are on popup we need to add a module
		if (isset($_GET['popup'])) {
			$page->container('snippet')->addModule('rt-sold-search', array(
					'state' => $listing['AddressState'],
			))->display();
		}

	// No Geopoints
	} else {
		echo '<p class="msg negative">This property is not able to be mapped at this time.</p>';
	}

}