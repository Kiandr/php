<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {
    // Disclaimer
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The data relating to real estate for sale on this website comes in part from the (IDX)Internet Data Exchange Program of the Republic of Testers, Multiple Listing Service. Real estate listings held by brokerage firms other than the Realtor owner of this website are marked with the Internet Data Exchange logo (IDX) or the Internet Data Exchange brief/thumbnail logo and detailed information about them includes the name of the listing firm. </p>';
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Information on this website is provided exclusively for consumers personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing.</p>';
}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Republic of Testers Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/idx-logo.png" border="0" />';

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = true;
