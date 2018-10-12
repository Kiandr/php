<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {
	// Require Broker Name
	$broker_name = '[INSERT BROKER NAME]';

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
		. '<img alt="Carolina Multiple Listing Services, Inc Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/cmls.jpg" border="0" style="float: left; margin: 0 15px 0.5em 0;" />  '
		. 'The data relating to real estate on this Web site derive in part from '
		. 'the Carolina Multiple Listing Services, Inc. IDX program. Brokers make '
		. 'an effort to deliver accurate information, but buyers should independently '
		. 'verify any information on which they will rely in a transaction. All '
		. 'properties are subject to prior sale, change or withdrawal. Neither '
		. $broker_name . ' nor any listing broker shall be responsible '
		. 'for any typographical errors, misinformation or misprints, and they shall '
		. 'be held totally harmless from any damages arising from reliance upon this data. '
		. 'This data is provided exclusively for consumers\' personal, non-commercial use and '
		. 'may not be used for any purpose other than to identify prospective '
		. 'properties they may be interested in purchasing. '
		. '&copy; ' . date("Y") . ' Carolina Multiple Listing Services, Inc.'
		. '</p>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Carolina Multiple Listing Services, Inc Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/cmls_house.jpg" border="0" />';

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
if (!in_array($_GET['load_page'], array('map'))) $_COMPLIANCE['details']['show_office'] = true;

// Brochure Compliance
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/cmls.jpg';
$_COMPLIANCE['logo_width'] = 20; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key
$_COMPLIANCE['shift_paragraphs'] = array(1, 2);
