<?php

// IDX Compliance Settings
global $_COMPLIANCE;
$_COMPLIANCE = array();

// Search Result Limit
$_COMPLIANCE['limit'] = 400;

// Display Update Time
$_COMPLIANCE['update_time'] = in_array($_GET['load_page'], array('search', 'sitemap', ''));

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

// Only Show On Certain Pages
if (in_array($_GET['load_page'], array('search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', '', 'sitemap', 'dashboard'))) {

	// MLS Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p>IDX information is provided by the Greater Fort Lauderdale REALTORS&reg; exclusively for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p>';
	$_COMPLIANCE['disclaimer'][] = 'The data relating to real estate for sale/lease on this web site come in part from a cooperative data exchange program of the multiple listing service (MLS) in which this real estate firm (Broker) participates. ';
	$_COMPLIANCE['disclaimer'][] = 'The properties displayed may not be all of the properties in the MLS’s database, or all of the properties listed with Brokers participating in the cooperative data exchange program. ';
	$_COMPLIANCE['disclaimer'][] = 'Properties listed by Brokers other than this Broker are marked with either the listing Broker’s logo or name or the MLS name or a logo provided by the MLS. ';
	$_COMPLIANCE['disclaimer'][] = 'Detailed information about such properties includes the name of the listing Brokers. Information provided is thought to be reliable but is not guaranteed to be accurate; you are advised to verify facts that are important to you. ';
	$_COMPLIANCE['disclaimer'][] = 'No warranties, expressed or implied, are provided for the data herein, or for their use or interpretation by the user. The Greater Fort Lauderdale REALTORS&reg; and its cooperating MLSs do not create, control or review the property data displayed herein and take no responsibility for the content of such records. ';
	$_COMPLIANCE['disclaimer'][] = 'Federal law prohibits discrimination on the basis of race, color, religion, sex, handicap, familial status or national origin in the sale, rental or financing of housing. ';
	$_COMPLIANCE['disclaimer'][] = '</p>';

}

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'local'));
