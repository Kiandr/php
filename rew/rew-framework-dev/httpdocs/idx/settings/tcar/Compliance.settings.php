<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

        // Disclaimer
        $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
		. '<img style="width: 70px;" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/metrolist-co.png" border="0" /> ' 
                . 'The data relating to real estate for sale in this web site comes from the Internet Data exchange ("IDX") Program of TCAR MLS, Inc. Not all members of TCAR\'s MLS are IDX Subscriber Brokers in the IDX Program. ' 
		. 'Real estate listings held by IDX Subscriber Brokers are marked with the IDX Logo. All data in this web site is deemed reliable but is not guaranteed. '
                . '</p>';

}

if (in_array($_GET['load_page'], array('details', 'brochure'))) {
	$_COMPLIANCE['details']['show_office'] = 'true';
}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Taos County Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/metrolist-co-thumb.png" />';

// Brochure logo
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/metrolist-co-thumb.png';    // Path
$_COMPLIANCE['logo_width'] = 15; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key
