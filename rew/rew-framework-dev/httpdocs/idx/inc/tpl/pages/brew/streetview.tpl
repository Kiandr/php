<?php

// Listing Not Found
if (empty($listing)) {
	echo '<h1>Listing Not Found</h1>';
	echo '<p class="msg negative">The selected listing could not be found. This is probably because the listing has been sold, removed from the market, or simply moved.</p>';

} else {

	// Listing Title
	echo (!empty($listing['ListingTitle']) ? '<h1>' . $listing['ListingTitle'] . '</h1>' : '') ;

	// Details Tabset
	include $page->locateTemplate('idx', 'misc', 'details');

	// Listing Heading
	if (empty($_COMPLIANCE['details']['remove_heading'])) {
		echo '<h1>$' . Format::number($listing['ListingPrice'])
			. ($_COMPLIANCE['results']['show_mls'] ? ' - ' . Lang::write('MLS_NUMBER') . $listing['ListingMLS'] : '')
			. ' - ' . $listing['Address'] . ', ' . $listing['AddressCity'] . ', ' . $listing['AddressState'] . '</h1>';
	}

	// Available
	$points = !empty($listing['Latitude']) && !empty($listing['Longitude']) ? true : false;

	// Require Points
	if (!empty($points)) {

		// Streetview Container
		echo '<div id="streetview-container"></div>';

	// No Geopoints
	} else {
		echo '<p class="msg negative">This listing is not able to be mapped at this time.</p>';

	}

	// "Next Steps"
	include $page->locateTemplate('idx', 'misc', 'nextsteps');

	// Show MLS Office / Agent
	if (!isset($_COMPLIANCE['details']['provider_first']) || $_COMPLIANCE['details']['provider_first']($listing) == false) {
		\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProvider($listing);
	}

}