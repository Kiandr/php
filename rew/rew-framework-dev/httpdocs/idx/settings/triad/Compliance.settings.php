<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array();

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	// Broker Name
	$broker_name  = '[INSERT BROKER NAME]';

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="Triad MLS Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/triad.gif" border="0" style="float: left; margin: 5px 15px 0.5em 0;" />The data relating to real estate for sale on this web site comes in part from the Internet Data Exchange (IDX) Program of the Triad MLS, Inc. of High Point, NC. Real estate listings held by brokerage firms other than ' .  $broker_name . ' are marked with the Internet Data Exchange logo or the Internet Data Exchange (IDX) thumbnail logo (the TRIAD MLS logo) and detailed information about them includes the name of the listing brokers. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The IDX information being provided is for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify perspective properties consumers may be interested in purchasing. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The broker providing this data believes it to be correct, but advises interested parties to confirm the data before relying on it in a purchase decision. Information Deemed Reliable But Not Guaranteed. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">&copy; ' .  date('Y') . ' Triad MLS, Inc. of North Carolina. All rights reserved. </p>';

	// if the site displays less than the full IDX, additional disclosures are recommended:
	// ------------------------------------------------------------------------------------
	// Scenario 1: if claiming to be the most complete complication of houses, but limiting is in place, disclousre of the limitation is good to prevent claims of false advertising
	// Scenario 2: if limiting, savvy consumers may see the limation and begin to distrust the site
	// Sample Disclosure 1: "[Your firm's name here] participates in Triad MLS's Internet Data Exchange (IDX) program, allowing us to display other broker's listings on our site. However, [firm name] displays only [listings in Guilford County] [only condominium listings] [exceptional properties with list prices above $500,000]."
	// Sample Disclosure 2: "[Your firm name] does not display the entire Triad MLS Internet Data Exchange (IDX) database on this site. The listings of some real estate brokerage firms have been excluded."

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Triad MLS Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/triad.gif" border="0" width="64" height="26" style="width: 64px; height: 26px;" />';

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'directions', 'local'));

// Brochure Compliance
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/triad.gif';
$_COMPLIANCE['logo_width'] = 20; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key
$_COMPLIANCE['shift_paragraphs'] = array(1, 2);
