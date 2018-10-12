<?php

// Listing Not Found
if (empty($listing)) {
    include $page->locateTemplate('idx', 'misc', 'listing/not-found');

} else {

	// Details Tabset
	include $page->locateTemplate('idx', 'misc', 'listing/header');

	// Require Points
	if (!empty($points)) {

		// Birdseye Container
		echo '<div id="birdseye-container"></div>';

	// No Geopoints
	} else {
        include $page->locateTemplate('idx', 'misc', 'listing/not-mapped');

	}

	// Show MLS Office / Agent
	if (!isset($_COMPLIANCE['details']['provider_first']) || $_COMPLIANCE['details']['provider_first']($listing) == false) {
		\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProvider($listing);
	}

	// Tooltip
	ob_start();
	$listing_tooltip = $listing;
	include $page->locateTemplate('idx', 'misc', 'tooltip');
	$tooltip = str_replace(array("\r\n", "\n", "\t"), "", ob_get_clean());

	// Birds Eye View
	$page->addJavascript('
	var birdseye = new REWMap($(\'#birdseye-container\'), ' . json_encode(
	array(
		'streetview'	=> !empty(Settings::getInstance()->MODULES['REW_IDX_STREETVIEW']),
		'center'		=> array('lat' => $listing['Latitude'], 'lng' => $listing['Longitude']),
		'manager'		=> array(
			'bounds'	=> false,
			'markers'	=> array(array(
				'tooltip'	=> $tooltip,
				'lat'		=> $listing['Latitude'],
				'lng'		=> $listing['Longitude']
			))
		),
		'type'          => 'satellite',
		'zoom'          => 18,
	)
	) . ');',
	'page', false);
}
