<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

$broker_name  = '[INSERT BROKER NAME]';

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

        // Disclaimer
        $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
		. '<img alt="Realtor Association of Acadiana Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br.jpg" border="0" /> '
                . 'The data relating to real estate for sale on this web site comes in part from the IDX Program of the Realtor&reg; Association of Acadiana\' MLS. '
		. 'Real Estate listings held by brokerage firms other than ' . $broker_name . ' are marked with the MLS approved icon for IDX (a little black house). '
                . 'Information is provided exclusively for consumers\' personal, non-commercial use. It may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. '
		. 'The data is deemed reliable but is not guaranteed accurate by the MLS. Participants are responsible for the content of their listings and the listings of their licensees. '
                . 'Participants agree to defend, indemnify and hold the Realtor&reg; Association of Acadiana, its members, and other MLS Subscribers, harmless against any claims or lawsuits arising out of the contents of or any inaccuracy or inadequacy in the MLS data and/or IDX information or data. '
                . 'Copyright ' . date(' Y ') . ' REALTOR&reg; Association of Acadiana MLS. All rights reserved. '
                . '</p>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Realtor Association of Acadiana Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br.jpg" />';
