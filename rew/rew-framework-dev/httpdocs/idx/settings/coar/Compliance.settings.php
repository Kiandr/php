<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form','dashboard'))) {

	// Require Broker Name
	$broker_name = '[INSERT BROKER NAME]';

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The content relating to real estate for sale on this website comes in part from the MLS of Central Oregon. Real Estate listings held by Brokerages other than ' . $broker_name . ' are marked with the Reciprocity/IDX logo, and detailed information about these properties includes the name of the listing Brokerage. &copy; MLS of Central Oregon (MLSCO).</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">This content last updated on <?=date(\'F jS, Y \a\t g:ia T\', strtotime($last_updated)); ?>. Some properties which appear for sale on this website may subsequently have sold or may no longer be available.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">All information provided is deemed reliable but is not guaranteed and should be independently verified.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">All content displayed on this website is restricted to personal, non-commercial use, and only for ascertaining information regarding real property for sale. The consumer will not copy, retransmit nor redistribute any of the content from this website. The consumer is reminded that all listing content provided by automatic transmission by MLSCO is &copy; MLS of Central Oregon (MLSCO).</p>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Central Oregon Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/coar-brslogosmll.gif" border="0" />';

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'local'));
