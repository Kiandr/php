<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
		. '<img alt="Telluride Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/tarco.png" border="0">'
		. 'The data relating to real estate on this web site comes from REALTORS '
		. 'who submit listing information to the Internet Data Exchange (IDX) '
		. 'Program of the Telluride Association of REALTORS, Inc. The inclusion of '
		. 'IDX Program data on this web site does not constitute an endorsement, '
		. 'acceptance, or approval by the Telluride Association of REALTORS of this '
		. 'web site, or the content of this web site. The data on this web site may '
		. 'not be reliable or accurate and is not guaranteed by the Telluride '
		. 'Association of REALTORS, Inc. '
		. '</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
		. 'The IDX data on this web site is provided exclusively for the web site '
		. 'user’s personal, non-commercial use and may not be used for any purpose '
		. 'other than to identify prospective properties that the user may be '
		. 'interested in purchasing.'
		. '</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
		. '&copy;Copyright <?=date(\'Y\', strtotime($last_updated)); ?> by Telluride Association of REALTORS, Inc. '
		. '</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
		. 'ALL RIGHTS RESERVED WORLDWIDE. '
		. '</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
		. 'No part of this publication may be reproduced, adapted, translated, '
		. 'stored in a retrieval system or transmitted in any form or by any means, '
		. 'electronic, mechanical, photocopying, recording, or otherwise, without '
		. 'the prior written consent of the Telluride Association of REALTORS, Inc.'
		. '</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
		. '<img alt="Telluride Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/tarco-eho.png" border="0">'
		. '</p>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Telluride Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/tarco-small.png" border="0">';
// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;
// Search Results, Display Agent Name
$_COMPLIANCE['results']['show_agent'] = true;
// Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = true;
// Details, Details Agent Name
$_COMPLIANCE['details']['show_agent'] = true;
// Details, Display Co-Office Name
$_COMPLIANCE['details']['show_co_office'] = true;
// Details, Display Co-Agent Name
$_COMPLIANCE['details']['show_co_agent'] = true;

// Brochure logo
$_COMPLIANCE['logo'] = array(
	array(
		'logo' => Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/tarco.png',
		'width' => 20, // Width (FPDF, not actual)
		'location' => 1, // Paragraph key
		'shift_paragraphs' => array(1), // Paragraph key
	),
	array(
		'logo' => Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/tarco-eho.png',
		'width' => 18, // Width (FPDF, not actual)
		'location' => 5, // Paragraph key
	)
);

// Custom MLS Compliance fields (Co Agent and Co Office)
$_COMPLIANCE['details']['extra'] = function ($idx, $db_idx, $listing, $_COMPLIANCE) {
	return array(
		array(
			'heading' => (!empty($_COMPLIANCE['details']['lang']['listing_details']) ? $_COMPLIANCE['details']['lang']['listing_details'] : 'Listing Details'),
			'fields' => array(
				!empty($_COMPLIANCE['details']['show_agent']) ? array('title' => 'Listing Agent', 'value' => 'ListingAgent') : null,
				!empty($_COMPLIANCE['details']['show_office']) ? array('title' => (!empty($_COMPLIANCE['details']['lang']['provider']) ? $_COMPLIANCE['details']['lang']['provider'] : 'Listing Office'), 'value' => 'ListingOffice') : null,
				!empty($_COMPLIANCE['details']['show_co_agent']) ? array('title' => 'Listing Co-Agent', 'value' => 'ListingCoAgent') : null,
				!empty($_COMPLIANCE['details']['show_co_office']) ? array('title' => 'Listing Co-Office', 'value' => 'ListingCoOffice') : null,
			),
		),
	);
};
