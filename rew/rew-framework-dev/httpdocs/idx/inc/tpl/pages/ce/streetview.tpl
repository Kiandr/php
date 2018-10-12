<?php

// Listing Not Found
if (empty($listing)) {
    include $page->locateTemplate('idx', 'misc', 'listing/not-found');

} else {

	// Details Tabset
	include $page->locateTemplate('idx', 'misc', 'listing/header');

	// Available
	$points = !empty($listing['Latitude']) && !empty($listing['Longitude']) ? true : false;

	// Require Points
	if (!empty($points)) {

		// Streetview Container
		echo '<div id="streetview-container"></div>';

	// No Geopoints
	} else {
        include $page->locateTemplate('idx', 'misc', 'listing/not-mapped');

	}

	// Show MLS Office / Agent
	if (!isset($_COMPLIANCE['details']['provider_first']) || $_COMPLIANCE['details']['provider_first']($listing) == false) {
		\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProvider($listing);
	}

}