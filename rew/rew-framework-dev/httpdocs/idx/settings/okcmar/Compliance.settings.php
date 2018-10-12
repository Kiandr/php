<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form','dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<div>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
	// If a page where there may be other content load the listing content version
	if (in_array($_GET['load_page'], array('', 'dashboard'))) {
		$_COMPLIANCE['disclaimer'][] = 'Listing Content: Copyright&copy; ' . date('Y') . ' MLSOK, Inc. listing content is believed to be accurate but is not guaranteed. Subject to verification by all parties. The listing information being provided is for consumers\' personal, non‐commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. This listing data is copyrighted and may not be transmitted, retransmitted, copied, framed, repurposed, or altered in any way for any other site, individual and/or purpose without the express written permission of MLSOK, Inc. Information last updated on <?=date(\'F jS, Y \a\t g:ia T\', strtotime($last_updated)); ?>.';
	} else {
		$_COMPLIANCE['disclaimer'][] = 'Copyright&copy; ' . date('Y') . ' MLSOK, Inc. This information is believed to be accurate but is not guaranteed. Subject to verification by all parties. The listing information being provided is for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. This data is copyrighted and may not be transmitted, retransmitted, copied, framed, repurposed, or altered in any way for any other site, individual and/or purpose without the express written permission of MLSOK, Inc. Information last updated on <?=date(\'F jS, Y \a\t g:ia T\', strtotime($last_updated)); ?>.';
	}
	$_COMPLIANCE['disclaimer'][] = '</p>';
	$_COMPLIANCE['disclaimer'][] = '<img alt="Oklahoma City Metro Association of Realstors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/okcmar_BR.jpg" border="0" />';
	$_COMPLIANCE['disclaimer'][] = '</div>';

}

// IDX Dashboard, Display Disclaimer
$_COMPLIANCE['dashboard']['show_disclaimer'] = true;

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Oklahoma City Metro Association of Realstors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/okcmar_BR.jpg" border="0" />';

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = true;
