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
	echo '<h1>$' . Format::number($listing['ListingPrice']) . ' - ' . $listing['Address'] . ', ' . $listing['AddressCity'] . ', ' . $listing['AddressState'] . '</h1>';

	// Map Available
	if (!empty($points)) {
		echo '<div id="map_canvas"></div>';

	// Map Un-Available
	} else {
		echo '<p class="msg negative">This listing is not able to be mapped at this time.</p>';

	}

	// Feed-specific compliance
	if (!empty($_COMPLIANCE['details']['disclaimer_under_map'])) {

		// Show MLS Office / Agent
		\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProvider($listing);

		// Show Disclaimer
		echo '<div class="show-immediately-below-listings">';
		\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(true);
		echo '</div>';

	}

	// "Next Steps"
	include $page->locateTemplate('idx', 'misc', 'nextsteps');

	// Require Points
	if (!empty($points)) {

        // Include javascript code for this page
        $this->addJavascript('js/idx/map.js', 'page');

		// Directions Enabled
		if (!empty(Settings::getInstance()->MODULES['REW_IDX_DIRECTIONS'])) {

?>
	<div id="map-directions">
	    <h4>Get Directions</h4>
	    <form>
	        <div class="field x6">
	            <label>From Address</label>
	            <input name="from" value="<?=(isset($_GET['from']) ? htmlspecialchars($_GET['from']) : ''); ?>" required>
	        </div>
	        <div class="field x6 last">
	            <label>To Address</label>
	            <input name="to" value="<?=htmlspecialchars(isset($_GET['to']) ? $_GET['to'] : !empty($listing['Latitude']) && !empty($listing['Longitude']) ? $listing['Latitude'].','.$listing['Longitude'] : $listing['Address'] . ', ' . $listing['AddressCity'] . ' ' . $listing['AddressZipCode']); ?>" required>
	        </div>
	        <div class="btnset">
	            <button class="strong" type="submit">Get Directions</button>
	        </div>
	    </form>
	    <div id="directions"></div>
	</div>
<?php

		}

	}

	// Show MLS Office / Agent
	if ((!isset($_COMPLIANCE['details']['provider_first']) || $_COMPLIANCE['details']['provider_first']($listing) == false) && empty($_COMPLIANCE['details']['disclaimer_under_map'])) {
		\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProvider($listing);
	}

	// Tooltip
	ob_start();
	$listing_tooltip = $listing;
	include $page->locateTemplate('idx', 'misc', 'tooltip');
	$tooltip = str_replace(array("\r\n", "\n", "\t"), "", ob_get_clean());

	// Map Options
	$mapOptions = array(
		'streetview'	=> !empty(Settings::getInstance()->MODULES['REW_IDX_STREETVIEW']),
		'center'		=> array('lat' => $listing['Latitude'], 'lng' => $listing['Longitude']),
		'manager'		=> array(
			'bounds'	=> false,
			'markers'	=> array(array(
				'tooltip'	=> $tooltip,
				'lat'		=> $listing['Latitude'],
				'lng'		=> $listing['Longitude']
			))
		)
	);

	// Details map variables
	$page->addJavascript('var mapOptions = ' . json_encode($mapOptions) . ';', 'dynamic', false);
}
