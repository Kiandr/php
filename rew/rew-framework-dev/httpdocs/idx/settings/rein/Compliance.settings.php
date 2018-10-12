<?php

// IDX Compliance Settings
global $_COMPLIANCE;
$_COMPLIANCE = array();

// Search Result Limit
$_COMPLIANCE['limit'] = 250;

// MLS Disclaimer
$_COMPLIANCE['disclaimer']   = array('');

// Only Show on Certain Pages
if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	// MLS Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<a href="' . Settings::getInstance()->URLS['URL_IDX'] . 'disclosure.html">&copy; ' . date('Y') . ' REIN, Inc. Information Deemed Reliable But Not Guaranteed.</a>';
}

// Compliance Page: disclosure.html
$_COMPLIANCE['pages']['disclosure'] = array();
$_COMPLIANCE['pages']['disclosure']['page_title'] = 'REIN Disclosure';
$_COMPLIANCE['pages']['disclosure']['category_html'] = '<h1>&copy;' . date('Y') . ' REIN, Inc.</h1>';
$_COMPLIANCE['pages']['disclosure']['category_html'] .= '<p>The listings data displayed on this medium comes in part from the Real Estate Information Network, Inc. (REIN) and has been authorized by participating listing Broker Members of REIN for display. REIN\'s listings are based upon data submitted by its Broker Members, and REIN therefore makes no representation or warranty regarding the accuracy of the data. All users of REIN\'s listings database should confirm the accuracy of the listing information directly with the listing agent.</p>';
$_COMPLIANCE['pages']['disclosure']['category_html'] .= '<p>&copy;' . date('Y') . ' REIN. REIN\'s listings data and information is protected under federal copyright laws. Federal law prohibits, among other acts, the unauthorized copying or alteration of, or preparation of derivative works from, all or any part of copyrighted materials, including certain compilations of data and information. COPYRIGHT VIOLATORS MAY BE SUBJECT TO SEVERE FINES AND PENALTIES UNDER FEDERAL LAW.</p>';
$_COMPLIANCE['pages']['disclosure']['category_html'] .= '<p>REIN updates its listings on a daily basis. Data last updated: ' . date('Y-m-d H:i:s T', strtotime(\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->lastUpdated())) . '</p>';
$_COMPLIANCE['pages']['disclosure']['category_html'] .= '<p>Real Estate Webmasters has used the REIN listing data, price current list and approximate square foot, to calculate the average, highest, and lowest listing price and average, highest, and lowest property size statistical data on the map search page.</p>';

// Listing Details: Display Office Name
$_COMPLIANCE['details']['show_office'] = (in_array($_GET['load_page'], array('map', 'streetview', 'birdseye', 'local'))) ? false : true;
$_COMPLIANCE['details']['lang']['provider'] = "Listing Courtesy of";
$_COMPLIANCE['details']['above_inquire'] = true;
