<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

	$broker_name = '[INSERT BROKER NAME]';

        // Disclaimer
        $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
                . 'The data relating to real estate for sale on this web site comes in part from the Internet Data Exchange program of the Savannah Multi-List Corporation. '
		. 'Real estate listings held by brokerage firms other than ' . $broker_name . ' are marked with the name of the listing brokers. Information deemed reliable but not guaranteed. '
                . '&copy; ' . date('Y') . ' Savannah Multi-List Corporation. All rights reserved. '
                . 'This data up-to-date as of ' . date('F jS, Y \a\t h:ia, T') . '. For the most current information, contact [your firm name, phone number, and e-mail address]'
                . '</p>';

}

if (in_array($_GET['load_page'], array('details', 'brochure'))) {
        $_COMPLIANCE['details']['show_office'] = true;
        $_COMPLIANCE['details']['show_agent'] = true;
}
