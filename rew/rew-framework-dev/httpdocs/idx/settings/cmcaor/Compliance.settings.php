<?php
// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	// Require Broker Name
	$broker_name = '[INSERT BROKER NAME]';

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="Cape May County Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/mls2_161.gif" style="float: left; padding-right: 1em;" />&copy;' . date("Y") . ' Cape May '
		. 'County Multiple Listing Service. The data relating to real estate '
		. 'for sale on this web site comes in part from the Broker Reciprocity '
		. 'program of the Cape May County MLS. IDX information is provided '
		. 'exclusively for consumers’ personal, non-commercial use and may not '
		. 'be used for any purpose other than to identify prospective properties '
		. 'consumers may be interested in purchasing. Real estate listings held '
		. 'by brokerage firms other than ' . $broker_name . ' are '
		. 'marked with the Broker Reciprocity logo or the Broker Reciprocity '
		. 'thumbnail logo (a little black house) and detailed information about '
		. 'them includes the name of the listing brokers. Some properties which '
		. 'appear for sale on this website may no longer be available because '
		. 'they are under contract, have sold or are no longer being offered for '
		. 'sale. Information is deemed reliable but is not guaranteed accurate.</p>';

}

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Cape May County Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/mls_161.gif" />';

// Brochure Compliance
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/mls2_161.gif';
$_COMPLIANCE['logo_width'] = 20; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key
$_COMPLIANCE['shift_paragraphs'] = array(1);
